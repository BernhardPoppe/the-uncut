const { execSync } = require('child_process');

function run(command, options = {}) {
  return execSync(command, { encoding: 'utf8', ...options }).trim();
}

try {
  run('git rev-parse --git-dir', { stdio: 'ignore' });
} catch {
  console.error('Kein Git-Repository gefunden.');
  process.exit(1);
}

run('git add -A', { stdio: 'inherit' });

try {
  run('git diff --staged --quiet', { stdio: 'ignore' });
  console.log('Keine Änderungen zum Committen.');
} catch {
  const message = `deploy: ${new Date().toISOString().slice(0, 16).replace('T', ' ')}`;
  execSync(`git commit -m ${JSON.stringify(message)}`, { stdio: 'inherit' });
  console.log(`Commit erstellt: ${message}`);
}
