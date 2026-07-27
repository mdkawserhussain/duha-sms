const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const BASE = 'http://127.0.0.1:8000';
const TOKEN = '1|Xk5jzCPh7LISzNFdGns3kQPv4rw3QVqp2g2svyIY1f0f32eb';
const OUT = 'D:/Rasel/duha-sms/debug-screenshots';

(async () => {
  if (!fs.existsSync(OUT)) fs.mkdirSync(OUT, { recursive: true });

  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({ viewport: { width: 1400, height: 900 } });
  const page = await context.newPage();

  // Collect console errors
  const consoleErrors = [];
  page.on('console', msg => {
    if (msg.type() === 'error') consoleErrors.push(msg.text());
  });
  page.on('pageerror', err => consoleErrors.push(err.message));

  // Set auth
  await page.goto(BASE);
  await page.evaluate((t) => {
    localStorage.setItem('auth_token', t);
    localStorage.setItem('user_role', 'admin');
  }, TOKEN);

  // Pages to test
  const pages = [
    { name: 'guardians', path: '/admin/guardians', addBtn: 'Add Guardian' },
    { name: 'teachers', path: '/admin/teachers', addBtn: 'Add Teacher' },
    { name: 'classes', path: '/admin/classes', addBtn: 'Add Class' },
    { name: 'students', path: '/admin/students', addBtn: 'Add Student' },
    { name: 'subjects', path: '/admin/subjects', addBtn: 'Add Subject' },
    { name: 'rooms', path: '/admin/rooms', addBtn: 'Add Room' },
    { name: 'academic-years', path: '/admin/academic-years', addBtn: 'Add Academic Year' },
    { name: 'terms', path: '/admin/terms', addBtn: 'Add Term' },
    { name: 'routines', path: '/admin/routines', addBtn: 'Add Routine' },
    { name: 'exam-routines', path: '/admin/exam-routines', addBtn: 'Add Exam Routine' },
    { name: 'attendance', path: '/admin/attendance', addBtn: 'Add Attendance' },
    { name: 'evaluations', path: '/admin/evaluations', addBtn: 'Add Evaluation' },
    { name: 'report-cards', path: '/admin/report-cards', addBtn: 'Add Report Card' },
    { name: 'fees', path: '/admin/fees', addBtn: 'Add Fee' },
    { name: 'messages', path: '/admin/messages', addBtn: 'Compose' },
    { name: 'announcements', path: '/admin/announcements', addBtn: 'Add Announcement' },
    { name: 'events', path: '/admin/events', addBtn: 'Add Event' },
    { name: 'applications', path: '/admin/applications', addBtn: '' },
    { name: 'profile-change-requests', path: '/admin/profile-change-requests', addBtn: '' },
    { name: 'promotions', path: '/admin/promotions', addBtn: 'Add Promotion' },
  ];

  for (const p of pages) {
    console.log(`\n=== Testing ${p.name} ===`);
    consoleErrors.length = 0;

    await page.goto(`${BASE}${p.path}`, { waitUntil: 'networkidle', timeout: 15000 });
    await page.waitForTimeout(1500);

    // Screenshot the page before clicking
    await page.screenshot({ path: path.join(OUT, `${p.name}-before.png`), fullPage: false });

    // Find and click the add button
    if (p.addBtn) {
      const btn = await page.$(`button:has-text("${p.addBtn}")`);
      if (btn) {
        const isVisible = await btn.isVisible();
        const isEnabled = await btn.isEnabled();
        console.log(`  Button "${p.addBtn}" found: visible=${isVisible}, enabled=${isEnabled}`);

        if (isVisible && isEnabled) {
          await btn.click();
          await page.waitForTimeout(1500);

          // Screenshot after clicking
          await page.screenshot({ path: path.join(OUT, `${p.name}-after-click.png`), fullPage: false });

          // Check what appeared
          const modal = await page.$('.fixed.inset-0');
          const dialog = await page.$('[role="dialog"]');
          const anyOverlay = await page.$$('.fixed');
          console.log(`  Modal (.fixed.inset-0): ${!!modal}`);
          console.log(`  Dialog ([role=dialog]): ${!!dialog}`);
          console.log(`  .fixed elements: ${anyOverlay.length}`);

          // Check if dark overlay is blocking everything
          const overlay = await page.$('.bg-gray-500, .bg-black, .bg-opacity-75, [class*="bg-opacity"]');
          if (overlay) {
            const box = await overlay.boundingBox();
            console.log(`  Overlay bounding box: ${JSON.stringify(box)}`);
          }

          // Check if form fields are visible inside modal
          const formInputs = await page.$$('.fixed input, [role="dialog"] input');
          console.log(`  Form inputs inside .fixed: ${formInputs.length}`);

          // Check modal content
          const modalText = await page.$eval('.fixed.inset-0', el => el.innerText).catch(() => 'N/A');
          console.log(`  Modal text: "${modalText.substring(0, 100)}..."`);

          // Close modal
          await page.keyboard.press('Escape');
          await page.waitForTimeout(300);
        }
      } else {
        console.log(`  Button "${p.addBtn}" NOT FOUND`);
      }
    } else {
      console.log(`  No add button specified — skipping`);
    }

    if (consoleErrors.length > 0) {
      console.log(`  Console errors:`);
      consoleErrors.forEach(e => console.log(`    ❌ ${e.substring(0, 200)}`));
    }
  }

  await browser.close();
  console.log(`\nScreenshots saved to ${OUT}`);
})();
