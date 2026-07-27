const fs = require('fs');

const dirs = [
  'D:/Rasel/duha-sms/resources/js/pages/admin',
  'D:/Rasel/duha-sms/resources/js/pages/teacher',
  'D:/Rasel/duha-sms/resources/js/pages/guardian'
];

const issues = [];

for (const dir of dirs) {
  const files = fs.readdirSync(dir).filter(f => f.endsWith('.vue'));
  for (const file of files) {
    const content = fs.readFileSync(dir + '/' + file, 'utf8');
    const scriptMatch = content.match(/<script[^>]*>([\s\S]*?)<\/script>/);
    if (!scriptMatch) continue;
    const script = scriptMatch[1];

    const definedFuncs = new Set();
    const funcDefs = script.matchAll(/(?:const|function)\s+(\w+)\s*(?:=\s*(?:async\s*)?\(|function\s*\()/g);
    for (const m of funcDefs) definedFuncs.add(m[1]);
    const refs = script.matchAll(/(?:const|let|var)\s+(\w+)\s*=\s*(?:ref|reactive|computed)/g);
    for (const m of refs) definedFuncs.add(m[1]);

    const templateMatch = content.match(/<template[^>]*>([\s\S]*?)<\/template>/);
    if (!templateMatch) continue;
    const template = templateMatch[1];

    const clickHandlers = template.matchAll(/@click(?:\.(?:prevent|stop|once))?="([^"]+)"/g);
    for (const m of clickHandlers) {
      const handler = m[1].trim();
      const funcName = handler.match(/^(\w+)/)?.[1];
      const skip = ['showAddModal', 'showCompose', 'showModal', 'page', 'totalPages', 'showAdd',
                     'showEditModal', 'activeTab', 'showForm', 'confirm', 'console', 'window',
                     'saving', 'loading', 'search', 'filter'];
      if (funcName && !definedFuncs.has(funcName) && !skip.includes(funcName)) {
        issues.push(`${dir.split('/').pop()}/${file}: @click="${handler}" — "${funcName}" not defined`);
      }
    }
  }
}

if (issues.length === 0) {
  console.log('No broken @click handlers found');
} else {
  console.log(`Found ${issues.length} broken handlers:\n`);
  issues.forEach(i => console.log('⚠️ ' + i));
}
