const fs = require('fs');

const files = [
  'D:/Rasel/duha-sms/resources/js/pages/admin/Classes.vue',
  'D:/Rasel/duha-sms/resources/js/pages/admin/Guardians.vue',
  'D:/Rasel/duha-sms/resources/js/pages/admin/Students.vue',
  'D:/Rasel/duha-sms/resources/js/pages/admin/Teachers.vue',
  'D:/Rasel/duha-sms/resources/js/pages/teacher/ExamRoutines.vue',
];

for (const filePath of files) {
  const content = fs.readFileSync(filePath, 'utf8');
  const name = filePath.split('/').pop();
  
  // Find the modal section
  const modalMatch = content.match(/<!-- [\s\S]*?Add\/Edit Modal[\s\S]*?<\/template>/);
  if (!modalMatch) {
    console.log(`${name}: Could not find modal section`);
    continue;
  }
  
  const modalSection = modalMatch[0];
  
  // Count divs
  const opens = (modalSection.match(/<div[\s>]/g) || []).length;
  const closes = (modalSection.match(/<\/div>/g) || []).length;
  
  const status = opens === closes ? '✅' : '❌';
  console.log(`${status} ${name}: ${opens} opens, ${closes} closes ${opens !== closes ? '(MISMATCH!)' : ''}`);
  
  if (opens !== closes) {
    // Find the problematic area
    let depth = 0;
    const lines = modalSection.split('\n');
    for (let i = 0; i < lines.length; i++) {
      const lineOpens = (lines[i].match(/<div[\s>]/g) || []).length;
      const lineCloses = (lines[i].match(/<\/div>/g) || []).length;
      depth += lineOpens - lineCloses;
      if (depth < 0) {
        console.log(`  Depth goes negative at line ${i + 1}: "${lines[i].trim().substring(0, 80)}"`);
      }
    }
    console.log(`  Final depth: ${depth}`);
  }
}
