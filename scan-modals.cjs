const fs = require('fs');
const path = require('path');

const dirs = [
  'D:/Rasel/duha-sms/resources/js/pages/admin',
  'D:/Rasel/duha-sms/resources/js/pages/teacher',
  'D:/Rasel/duha-sms/resources/js/pages/guardian'
];

const results = [];

for (const dir of dirs) {
  const role = dir.split('/').pop();
  const files = fs.readdirSync(dir).filter(f => f.endsWith('.vue'));
  for (const file of files) {
    const content = fs.readFileSync(path.join(dir, file), 'utf8');
    // Find all modal patterns
    const modals = content.match(/<div[^>]*class="fixed inset-0[^"]*"[^>]*>/g);
    if (modals) {
      results.push({ role, file, modalCount: modals.length, patterns: modals.map(m => m.substring(0, 120)) });
    }
  }
}

console.log(`Found modals in ${results.length} files:\n`);
results.forEach(r => {
  console.log(`${r.role}/${r.file} — ${r.modalCount} modal(s)`);
  r.patterns.forEach(p => console.log(`  ${p}`));
});
