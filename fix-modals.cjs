const fs = require('fs');
const path = require('path');

// Files with the BROKEN pattern (separate overlay div covering content)
const brokenFiles = [
  'D:/Rasel/duha-sms/resources/js/pages/admin/Classes.vue',
  'D:/Rasel/duha-sms/resources/js/pages/admin/Guardians.vue',
  'D:/Rasel/duha-sms/resources/js/pages/admin/Students.vue',
  'D:/Rasel/duha-sms/resources/js/pages/admin/Teachers.vue',
  'D:/Rasel/duha-sms/resources/js/pages/teacher/ExamRoutines.vue',
];

let totalFixed = 0;

for (const filePath of brokenFiles) {
  let content = fs.readFileSync(filePath, 'utf8');
  const file = path.basename(filePath);
  
  // Fix the broken pattern: replace the separate overlay + content structure
  // with the working flex pattern
  
  // Pattern: outer div with z-50 overflow-y-auto containing:
  //   1. sm:block wrapper with overlay div + content div
  // Replace with: single div with bg overlay + flex centering
  
  // Fix for showAddModal pattern
  const patterns = [
    {
      // The broken outer wrapper
      old: /<div\s*\n\s*v-if="(showAddModal|showAssignModal|showTransferModal)"\s*\n\s*class="fixed inset-0 z-50 overflow-y-auto"\s*\n\s*aria-labelledby="modal-title"\s*\n\s*role="dialog"\s*\n\s*aria-modal="true"\s*\n\s*>\s*\n\s*<div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">\s*\n\s*<div\s*\n\s*class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"\s*\n\s*@click="\1 = false"\s*\n\s*\/>\s*\n\s*\n\s*<div class="inline-block align-bottom/g,
      new: '<div v-if="$1" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">\n        <div'
    },
  ];
  
  // Simpler approach: just fix the overlay div class and remove the sm: wrapper
  // Step 1: Replace the overlay background div with just making it part of the flex container
  // Step 2: Remove the sm:block sm:p-0 wrapper
  
  // Actually, the simplest fix: make the overlay have lower z-index
  // Change: class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
  // To: class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
  // And wrap content in a relative z-10 div
  
  // Even simpler: just change the broken pattern to the working pattern
  // The working pattern is: <div v-if="X" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
  
  let fixed = false;
  
  // Fix the outer wrapper for showAddModal
  if (content.includes('class="fixed inset-0 z-50 overflow-y-auto"')) {
    // Replace the entire modal structure
    content = content.replace(
      /<div\s*\n\s*v-if="(show\w+Modal|showAssignModal|showTransferModal)"\s*\n\s*class="fixed inset-0 z-50 overflow-y-auto"\s*\n\s*aria-labelledby="modal-title"\s*\n\s*role="dialog"\s*\n\s*aria-modal="true"\s*\n\s*>[\s\S]*?<div class="inline-block align-bottom/,
      '<div v-if="$1" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">\n        <div class="inline-block align-bottom'
    );
    
    // Remove the sm: wrapper and overlay div
    content = content.replace(
      /<div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">\s*\n\s*<div\s*\n\s*class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"\s*\n\s*@click="[^"]*"\s*\n\s*\/>\s*\n\s*\n\s*/,
      ''
    );
    
    // Remove the closing sm: wrapper div
    content = content.replace(
      /\s*<\/div>\s*\n\s*<\/div>\s*\n\s*<\/div>\s*\n\s*<\/div>\s*\n/,
      '\n      </div>\n    </div>\n'
    );
    
    fixed = true;
  }
  
  if (fixed) {
    fs.writeFileSync(filePath, content, 'utf8');
    console.log(`✅ Fixed: ${file}`);
    totalFixed++;
  } else {
    console.log(`⏭️ Skipped: ${file} (no broken pattern found)`);
  }
}

console.log(`\nTotal fixed: ${totalFixed}`);
