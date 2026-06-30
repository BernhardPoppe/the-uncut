const { execSync } = require('child_process');
const path = require('path');

const ROOT = path.resolve(__dirname, '..');

function isGitRepo() {
  try {
    execSync('git rev-parse --is-inside-work-tree', { cwd: ROOT, stdio: 'pipe' });
    return true;
  } catch {
    return false;
  }
}

if (!isGitRepo()) {
  console.log('Kein Git-Repository – Commit übersprungen.');
  process.exit(0);
}

execSync('git add -A', { cwd: ROOT, stdio: 'inherit' });

try {
  execSync('git diff --staged --quiet', { cwd: ROOT, stdio: 'pipe' });
  console.log('Keine Änderungen zum Committen.');
} catch {
  const message = `deploy: ${new Date().toISOString().slice(0, 16).replace('T', ' ')}`;
  execSync(`git commit -m ${JSON.stringify(message)}`, { cwd: ROOT, stdio: 'inherit' });
  console.log(`Commit erstellt: ${message}`);
}
