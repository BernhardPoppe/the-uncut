require('dotenv').config({ path: require('path').resolve(__dirname, '..', '.env') });

const crypto = require('crypto');
const fs = require('fs');
const path = require('path');
const SftpClient = require('ssh2-sftp-client');
const lib = require('ftp-deploy/src/lib');

const projectRoot = path.resolve(__dirname, '..');
const manifestPath = path.join(projectRoot, '.deploy-manifest.json');

const required = ['SFTP_HOST', 'SFTP_USER', 'SFTP_REMOTE_PATH'];
const missing = required.filter((key) => !process.env[key]);

if (missing.length) {
  console.error(`Fehlende Umgebungsvariablen in .env: ${missing.join(', ')}`);
  console.error('Kopiere .env.example nach .env und trage deine Zugangsdaten ein.');
  process.exit(1);
}

if (!process.env.SFTP_PASSWORD && !process.env.SFTP_PRIVATE_KEY) {
  console.error('Weder SFTP_PASSWORD noch SFTP_PRIVATE_KEY in .env gesetzt.');
  process.exit(1);
}

const deployConfig = {
  localRoot: projectRoot,
  remoteRoot: process.env.SFTP_REMOTE_PATH.replace(/\/+$/, ''),
  include: [
    '*.php',
    'style.css',
    'screenshot.png',
    'acf-json/**',
    'blocks/**',
    'dist/**',
    'graphics/**',
    'partials/**',
  ],
  exclude: ['dist/**/*.map', 'node_modules/**'],
};

const forceUpload = process.env.DEPLOY_FORCE === 'true';

function buildSftpConfig() {
  const config = {
    host: process.env.SFTP_HOST,
    port: Number(process.env.SFTP_PORT || 22),
    username: process.env.SFTP_USER,
  };

  if (process.env.SFTP_PASSWORD) {
    config.password = process.env.SFTP_PASSWORD;
  }

  if (process.env.SFTP_PRIVATE_KEY) {
    const keyPath = process.env.SFTP_PRIVATE_KEY.startsWith('~')
      ? process.env.SFTP_PRIVATE_KEY.replace(/^~/, process.env.HOME || '')
      : process.env.SFTP_PRIVATE_KEY;
    config.privateKey = fs.readFileSync(keyPath);
    if (process.env.SFTP_PASSPHRASE) {
      config.passphrase = process.env.SFTP_PASSPHRASE;
    }
  }

  return config;
}

function loadManifest() {
  if (!fs.existsSync(manifestPath)) {
    return {};
  }

  try {
    return JSON.parse(fs.readFileSync(manifestPath, 'utf8'));
  } catch {
    return {};
  }
}

function saveManifest(manifest) {
  fs.writeFileSync(manifestPath, `${JSON.stringify(manifest, null, 2)}\n`);
}

function fileHash(filePath) {
  return crypto.createHash('md5').update(fs.readFileSync(filePath)).digest('hex');
}

function collectFiles(config) {
  const filemap = lib.parseLocal(
    config.include,
    config.exclude,
    config.localRoot,
    '/'
  );

  const files = [];

  for (const [relDir, names] of Object.entries(filemap)) {
    for (const name of names) {
      const relPath = relDir === '/' ? name : path.posix.join(relDir, name);
      files.push({
        relPath,
        localPath: path.join(config.localRoot, relPath),
        remotePath: path.posix.join(config.remoteRoot, relPath),
      });
    }
  }

  files.sort((a, b) => a.relPath.localeCompare(b.relPath));
  return files;
}

async function ensureRemoteDir(sftp, remoteDir, cache) {
  if (remoteDir === '/' || remoteDir === '.' || cache.has(remoteDir)) {
    return;
  }

  const exists = await sftp.exists(remoteDir);
  if (!exists) {
    await sftp.mkdir(remoteDir, true);
  }

  cache.add(remoteDir);
}

async function deploy() {
  const manifest = loadManifest();
  const nextManifest = { ...manifest };
  const files = collectFiles(deployConfig);
  const toUpload = [];

  for (const file of files) {
    const hash = fileHash(file.localPath);

    if (!forceUpload && manifest[file.relPath] === hash) {
      continue;
    }

    toUpload.push({ ...file, hash });
  }

  const skipped = files.length - toUpload.length;
  console.log(
    `Starte SFTP-Upload nach ${process.env.SFTP_HOST}${deployConfig.remoteRoot} ...`
  );
  console.log(
    `${toUpload.length} geändert, ${skipped} unverändert (${files.length} gesamt)${
      forceUpload ? ' [force]' : ''
    }`
  );

  if (toUpload.length === 0) {
    console.log('Nichts zu tun.');
    return;
  }

  const sftp = new SftpClient();
  const dirsCreated = new Set();

  try {
    await sftp.connect(buildSftpConfig());
    console.log(`Verbunden mit ${process.env.SFTP_HOST}`);

    let uploaded = 0;

    for (const file of toUpload) {
      const remoteDir = path.posix.dirname(file.remotePath);
      await ensureRemoteDir(sftp, remoteDir, dirsCreated);

      uploaded += 1;
      console.log(`[${uploaded}/${toUpload.length}] ${file.relPath}`);
      await sftp.fastPut(file.localPath, file.remotePath);
      nextManifest[file.relPath] = file.hash;
    }
  } finally {
    await sftp.end();
  }

  for (const relPath of Object.keys(nextManifest)) {
    if (!files.some((file) => file.relPath === relPath)) {
      delete nextManifest[relPath];
    }
  }

  saveManifest(nextManifest);
  console.log('Deploy erfolgreich abgeschlossen.');
}

deploy().catch((error) => {
  console.error('Deploy fehlgeschlagen:', error.message || error);
  process.exit(1);
});
