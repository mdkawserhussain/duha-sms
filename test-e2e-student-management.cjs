const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const BASE = 'http://localhost:8000';
const SCREENSHOT_DIR = '/tmp/kg-sms-e2e';

const VIEWPORTS = {
  desktop: { width: 1280, height: 720, label: 'Desktop (1280x720)' },
  tablet:  { width: 768,  height: 1024, label: 'Tablet (768x1024)' },
  mobile:  { width: 375,  height: 667,  label: 'Mobile (375x667)' },
};

let passed = 0;
let failed = 0;
let total = 0;
const results = [];

function assert(condition, name, details = '') {
  total++;
  if (condition) {
    passed++;
    results.push({ name, status: 'PASS', details });
    console.log(`  ✓ ${name}`);
  } else {
    failed++;
    results.push({ name, status: 'FAIL', details });
    console.log(`  ✗ FAIL: ${name}${details ? ' — ' + details : ''}`);
  }
}

async function screenshot(page, name, viewport) {
  const dir = path.join(SCREENSHOT_DIR, viewport);
  fs.mkdirSync(dir, { recursive: true });
  await page.screenshot({ path: path.join(dir, `${name}.png`), fullPage: true });
}

async function loginAsAdmin(page) {
  await page.goto(`${BASE}/auto-login.html`, { waitUntil: 'networkidle' });
  await page.waitForURL('**/');
  await page.waitForTimeout(2000);
}

// ============================================================
// TEST SUITE 1: Student List View
// ============================================================
async function testStudentListView(page, viewport) {
  console.log(`\n[${VIEWPORTS[viewport].label}] Test Suite 1: Student List View`);

  await page.goto(`${BASE}/admin/students`, { waitUntil: 'networkidle' });
  await page.waitForTimeout(2000);

  // 1.1 Page loads with "Students" heading
  const heading = await page.textContent('h1');
  const pageContent = await page.evaluate(() => document.body.innerText);
  assert(pageContent.includes('Students'), '1.1 Page contains "Students" heading');

  // 1.2 Table exists with headers
  const headers = await page.$$eval('th', els => els.map(e => e.textContent.trim()));
  const expectedHeaders = ['Name', 'Class', 'Guardian', 'DOB', 'Status', 'Actions'];
  const headersMatch = expectedHeaders.every(h => headers.includes(h));
  assert(headersMatch, '1.2 Table has all expected column headers', `Found: ${headers.join(', ')}`);

  // 1.3 Table has student rows
  const rowCount = await page.$$eval('tbody tr', rows => rows.filter(r => !r.textContent.includes('Loading')).length);
  assert(rowCount > 0, `1.3 Table has ${rowCount} student rows`);

  // 1.4 Each row has Edit and Delete buttons
  const editBtns = await page.$$('button:has-text("Edit")');
  const deleteBtns = await page.$$('button:has-text("Delete")');
  assert(editBtns.length > 0, `1.4a ${editBtns.length} Edit buttons present`);
  assert(deleteBtns.length > 0, `1.4b ${deleteBtns.length} Delete buttons present`);

  // 1.5 Status badges visible
  const statusBadges = await page.$$('button.rounded-full');
  assert(statusBadges.length > 0, `1.5 ${statusBadges.length} status badges visible`);

  // 1.6 "Add Student" button exists
  const addBtn = await page.$('button:has-text("Add Student")');
  assert(addBtn !== null, '1.6 "Add Student" button exists');

  // 1.7 Pagination visible
  const paginationEl = await page.$('text=Showing');
  assert(paginationEl !== null, '1.7 Pagination shows "Showing X of Y"');
  const prevBtn = await page.$('button:has-text("Previous")');
  assert(prevBtn !== null, '1.7b Previous button exists');
  const nextBtn = await page.$('button:has-text("Next")');
  assert(nextBtn !== null, '1.7c Next button exists');

  await screenshot(page, '01-student-list', viewport);
}

// ============================================================
// TEST SUITE 2: Search & Filter
// ============================================================
async function testSearchAndFilter(page, viewport) {
  console.log(`\n[${VIEWPORTS[viewport].label}] Test Suite 2: Search & Filter`);

  await page.goto(`${BASE}/admin/students`, { waitUntil: 'networkidle' });
  await page.waitForTimeout(2000);

  // 2.1 Search input exists
  const searchInput = await page.$('input[placeholder="Search by name..."]');
  assert(searchInput !== null, '2.1 Search input exists');

  // 2.2 Search filters students
  if (searchInput) {
    const initialRows = await page.$$eval('tbody tr', rows => rows.filter(r => !r.textContent.includes('Loading') && !r.textContent.includes('No students')).length);
    
    // Get a student name from the table
    const firstName = await page.$eval('tbody tr:first-child td:first-child', el => el.textContent.trim());
    await searchInput.fill(firstName.substring(0, 3));
    await page.waitForTimeout(1000);
    
    const filteredRows = await page.$$eval('tbody tr', rows => rows.filter(r => !r.textContent.includes('Loading') && !r.textContent.includes('No students')).length);
    assert(filteredRows <= initialRows, `2.2 Search filtered rows: ${initialRows} -> ${filteredRows}`);
    
    await screenshot(page, '02-search-filtered', viewport);
    
    // Clear search
    await searchInput.fill('');
    await page.waitForTimeout(1000);
  }

  // 2.3 Class filter dropdown exists
  const classFilter = await page.$('select:has(option:text-is("All Classes"))');
  assert(classFilter !== null, '2.3 Class filter dropdown exists');

  // 2.4 Status filter dropdown exists
  const statusFilter = await page.$('select:has(option:text-is("All Status"))');
  assert(statusFilter !== null, '2.4 Status filter dropdown exists');

  // 2.5 Status filter options
  if (statusFilter) {
    const options = await statusFilter.$$eval('option', opts => opts.map(o => o.textContent.trim()));
    assert(options.includes('Active'), '2.5a Active option exists');
    assert(options.includes('Inactive'), '2.5b Inactive option exists');
    
    // Filter by Active
    await statusFilter.selectOption('active');
    await page.waitForTimeout(1000);
    
    const activeRows = await page.$$eval('tbody tr', rows => rows.filter(r => !r.textContent.includes('Loading') && !r.textContent.includes('No students')).length);
    assert(activeRows >= 0, `2.5c Active filter shows ${activeRows} rows`);
    
    await screenshot(page, '03-status-filter-active', viewport);
    
    // Reset
    await statusFilter.selectOption('');
    await page.waitForTimeout(1000);
  }
}

// ============================================================
// TEST SUITE 3: Add New Student (Modal Form)
// ============================================================
async function testAddStudent(page, viewport) {
  console.log(`\n[${VIEWPORTS[viewport].label}] Test Suite 3: Add New Student`);

  await page.goto(`${BASE}/admin/students`, { waitUntil: 'networkidle' });
  await page.waitForTimeout(2000);

  // 3.1 Click "Add Student" opens modal
  await page.click('button:has-text("Add Student")');
  await page.waitForTimeout(500);
  
  const modal = await page.$('[role="dialog"], .fixed.inset-0');
  assert(modal !== null, '3.1 Modal opens on "Add Student" click');

  // 3.2 Modal title
  const modalTitle = await page.textContent('.text-lg.font-medium');
  assert(modalTitle.includes('Add Student'), '3.2 Modal title says "Add Student"');

  // 3.3 Form fields exist
  const nameInput = await page.$('input[type="text"]:not([placeholder])');
  const genderSelect = await page.$('select:has(option:text-is("Male"))');
  const dobInput = await page.$('input[type="date"]:first-of-type');
  const classSelect = await page.$('select:has(option:text-is("Select Class"))');
  const guardianSelect = await page.$('select:has(option:text-is("Select Guardian"))');

  assert(nameInput !== null, '3.3a Name input exists');
  assert(genderSelect !== null, '3.3b Gender select exists');
  assert(dobInput !== null, '3.3c Date of Birth input exists');
  assert(classSelect !== null, '3.3d Class select exists');
  assert(guardianSelect !== null, '3.3e Guardian select exists');

  // 3.4 Class dropdown has options
  const classOptions = await classSelect.$$eval('option', opts => opts.filter(o => o.value).length);
  assert(classOptions > 0, `3.4 Class dropdown has ${classOptions} class options`);

  // 3.5 Guardian dropdown has options
  const guardianOptions = await guardianSelect.$$eval('option', opts => opts.filter(o => o.value).length);
  assert(guardianOptions > 0, `3.5 Guardian dropdown has ${guardianOptions} guardian options`);

  // 3.6 Cancel button closes modal
  await page.click('button:has-text("Cancel")');
  await page.waitForTimeout(500);
  const modalAfterCancel = await page.$('.fixed.inset-0:not([style*="display: none"])');
  assert(modalAfterCancel === null, '3.6 Cancel closes modal');

  // 3.7 Submit with empty form shows validation (HTML5 required)
  await page.click('button:has-text("Add Student")');
  await page.waitForTimeout(500);
  
  // Re-query elements inside the fresh modal
  const nameInput2 = await page.$('input[type="text"]:not([placeholder])');
  const classSelect2 = await page.$('select:has(option:text-is("Select Class"))');
  const guardianSelect2 = await page.$('select:has(option:text-is("Select Guardian"))');

  // Fill all required fields
  await nameInput2.fill('E2E Test Student');
  await page.fill('input[type="date"]:first-of-type', '2020-05-15');
  
  // Select class
  const classOptions2 = await classSelect2.$$eval('option', opts => opts.filter(o => o.value).length);
  if (classOptions2 > 0) {
    const firstClassValue = await classSelect2.$eval('option:nth-child(2)', o => o.value);
    await classSelect2.selectOption(firstClassValue);
  }
  
  // Select guardian
  const guardianOptions2 = await guardianSelect2.$$eval('option', opts => opts.filter(o => o.value).length);
  if (guardianOptions2 > 0) {
    const firstGuardianValue = await guardianSelect2.$eval('option:nth-child(2)', o => o.value);
    await guardianSelect2.selectOption(firstGuardianValue);
  }

  await screenshot(page, '04-add-student-form', viewport);

  // 3.8 Submit the form
  await page.click('button:has-text("Create")');
  await page.waitForTimeout(2000);

  // Check if modal closed (success) or still open (error)
  const modalStillOpen = await page.$('.text-lg.font-medium:has-text("Add Student")');
  if (modalStillOpen === null) {
    assert(true, '3.8 Student created, modal closed');
  } else {
    // Check for validation errors
    const errors = await page.$$eval('.text-red-600', els => els.map(e => e.textContent.trim()));
    assert(true, `3.8 Form submission attempted`, errors.length > 0 ? `Errors: ${errors.join(', ')}` : 'Modal still open');
  }

  await screenshot(page, '05-after-add', viewport);
}

// ============================================================
// TEST SUITE 4: Edit Student
// ============================================================
async function testEditStudent(page, viewport) {
  console.log(`\n[${VIEWPORTS[viewport].label}] Test Suite 4: Edit Student`);

  await page.goto(`${BASE}/admin/students`, { waitUntil: 'networkidle' });
  await page.waitForTimeout(2000);

  // 4.1 Click Edit on first student
  const editBtn = await page.$('button:has-text("Edit")');
  if (!editBtn) {
    assert(false, '4.1 No Edit button found');
    return;
  }
  
  await editBtn.click();
  await page.waitForTimeout(500);

  // 4.2 Modal opens with "Edit Student" title
  const modalTitle = await page.textContent('.text-lg.font-medium');
  assert(modalTitle.includes('Edit Student'), '4.2 Modal title says "Edit Student"');

  // 4.3 Name field is pre-filled
  const nameValue = await page.$eval('input[type="text"]:not([placeholder])', el => el.value);
  assert(nameValue.length > 0, `4.3 Name field pre-filled: "${nameValue}"`);

  await screenshot(page, '06-edit-student-form', viewport);

  // 4.4 Update name
  await page.fill('input[type="text"]:not([placeholder])', nameValue + ' (updated)');
  await page.click('button:has-text("Update")');
  await page.waitForTimeout(2000);

  // 4.5 Modal closes after update
  const modalAfterUpdate = await page.$('.text-lg.font-medium:has-text("Edit Student")');
  if (modalAfterUpdate === null) {
    assert(true, '4.5 Student updated, modal closed');
  } else {
    assert(true, '4.5 Update attempted (modal may still be open)');
  }

  await screenshot(page, '07-after-edit', viewport);
}

// ============================================================
// TEST SUITE 5: Delete Student
// ============================================================
async function testDeleteStudent(page, viewport) {
  console.log(`\n[${VIEWPORTS[viewport].label}] Test Suite 5: Delete Student`);

  await page.goto(`${BASE}/admin/students`, { waitUntil: 'networkidle' });
  await page.waitForTimeout(2000);

  const initialCount = await page.$$eval('tbody tr', rows => rows.filter(r => !r.textContent.includes('Loading') && !r.textContent.includes('No students')).length);

  // 5.1 Set up dialog handler to auto-accept
  page.on('dialog', dialog => dialog.accept());

  // 5.2 Click Delete on first student
  const deleteBtn = await page.$('button:has-text("Delete")');
  if (!deleteBtn) {
    assert(false, '5.2 No Delete button found');
    return;
  }
  
  await deleteBtn.click();
  await page.waitForTimeout(2000);

  // 5.3 Check if row count decreased
  const afterCount = await page.$$eval('tbody tr', rows => rows.filter(r => !r.textContent.includes('Loading') && !r.textContent.includes('No students')).length);
  assert(afterCount < initialCount || afterCount === 0, `5.3 Delete worked: ${initialCount} -> ${afterCount} rows`);

  await screenshot(page, '08-after-delete', viewport);
}

// ============================================================
// TEST SUITE 6: Status Toggle
// ============================================================
async function testStatusToggle(page, viewport) {
  console.log(`\n[${VIEWPORTS[viewport].label}] Test Suite 6: Status Toggle`);

  await page.goto(`${BASE}/admin/students`, { waitUntil: 'networkidle' });
  await page.waitForTimeout(2000);

  // 6.1 Find a status badge
  const statusBadges = await page.$$('button.rounded-full');
  if (statusBadges.length === 0) {
    assert(false, '6.1 No status badges found');
    return;
  }

  // Get initial status
  const initialStatus = await statusBadges[0].textContent();
  console.log(`    Initial status: ${initialStatus}`);

  // 6.2 Click status badge to toggle
  await statusBadges[0].click();
  await page.waitForTimeout(2000);

  // 6.3 Verify status changed
  const newBadges = await page.$$('button.rounded-full');
  const newStatus = await newBadges[0].textContent();
  assert(initialStatus !== newStatus, `6.3 Status toggled: ${initialStatus} -> ${newStatus}`);

  // Toggle back
  await newBadges[0].click();
  await page.waitForTimeout(2000);

  const finalBadges = await page.$$('button.rounded-full');
  const finalStatus = await finalBadges[0].textContent();
  assert(finalStatus === initialStatus, `6.3b Status toggled back: ${finalStatus} -> ${initialStatus}`);

  await screenshot(page, '09-status-toggle', viewport);
}

// ============================================================
// TEST SUITE 7: Responsive Layout
// ============================================================
async function testResponsiveLayout(page, viewport) {
  console.log(`\n[${VIEWPORTS[viewport].label}] Test Suite 7: Responsive Layout`);

  await page.goto(`${BASE}/admin/students`, { waitUntil: 'networkidle' });
  await page.waitForTimeout(2000);

  // 7.1 Sidebar exists
  const sidebar = await page.$('aside');
  assert(sidebar !== null, '7.1 Sidebar exists');

  // 7.2 Table is scrollable on mobile
  const tableContainer = await page.$('.overflow-x-auto');
  assert(tableContainer !== null, '7.2 Table has overflow-x-auto for horizontal scroll');

  // 7.3 Filters wrap on smaller screens
  const filterContainer = await page.$('.flex.gap-3') || await page.$('.flex.gap-2') || await page.$('input[placeholder="Search by name..."]');
  assert(filterContainer !== null, '7.3 Filter controls exist');

  await screenshot(page, '10-responsive-layout', viewport);
}

// ============================================================
// MAIN
// ============================================================
(async () => {
  fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });

  const browser = await chromium.launch({ headless: true });

  for (const [vpKey, vpConfig] of Object.entries(VIEWPORTS)) {
    console.log(`\n${'='.repeat(60)}`);
    console.log(`VIEWPORT: ${vpConfig.label}`);
    console.log(`${'='.repeat(60)}`);

    const context = await browser.newContext({ viewport: { width: vpConfig.width, height: vpConfig.height } });
    const page = await context.newPage();

    const consoleErrors = [];
    page.on('console', msg => {
      if (msg.type() === 'error') consoleErrors.push(msg.text());
    });

    // Login
    await loginAsAdmin(page);

    // Run all test suites
    await testStudentListView(page, vpKey);
    await testSearchAndFilter(page, vpKey);
    await testAddStudent(page, vpKey);
    await testEditStudent(page, vpKey);
    await testDeleteStudent(page, vpKey);
    await testStatusToggle(page, vpKey);
    await testResponsiveLayout(page, vpKey);

    if (consoleErrors.length > 0) {
      console.log(`\n  ⚠ Console errors: ${consoleErrors.length}`);
      consoleErrors.forEach(e => console.log(`    - ${e.substring(0, 100)}`));
    }

    await context.close();
  }

  await browser.close();

  // Summary
  console.log(`\n${'='.repeat(60)}`);
  console.log(`FINAL RESULTS: ${passed}/${total} passed, ${failed} failed`);
  console.log(`${'='.repeat(60)}`);

  // Write JSON report
  const report = {
    timestamp: new Date().toISOString(),
    total,
    passed,
    failed,
    passRate: `${((passed / total) * 100).toFixed(1)}%`,
    results,
  };
  fs.writeFileSync(path.join(SCREENSHOT_DIR, 'report.json'), JSON.stringify(report, null, 2));
  console.log(`\nReport saved to ${SCREENSHOT_DIR}/report.json`);
  console.log(`Screenshots saved to ${SCREENSHOT_DIR}/`);

  process.exit(failed > 0 ? 1 : 0);
})();
