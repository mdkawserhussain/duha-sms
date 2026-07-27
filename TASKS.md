# TASKS.md — Duha SMS: Full Implementation Task List

> Cross-checked against PRD feature list (7 modules). Generated from codebase audit.
> Last updated: 2026-07-26

---

## Legend

| Symbol | Meaning |
|--------|---------|
| ✅ | Already exists (may need fixes) |
| 🔧 | Exists but broken/mismatched |
| ❌ | Missing entirely |
| ⚠️ | Exists but not enforced/functional |

---

## MODULE 1: Administrative Architecture & System Setup

### 1.1 RBAC Enforcement

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Wire policies into AuthServiceProvider `$policies` array | ❌ | S | None |
| Add `$this->authorize()` or `->can()` calls to all admin controllers | ❌ | M | Above |
| Add `RoleServiceProvider` to register policies globally | ❌ | S | None |
| Add super_admin vs admin role distinction | ❌ | S | None |

### 1.2 Multi-Tenant Architecture

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Add `school_id` column to all relevant tables (students, classes, users, etc.) | ❌ | L | None |
| Create TenantMiddleware to resolve school from subdomain/domain | ❌ | M | Above |
| Scope all queries by `school_id` via global scope | ❌ | L | Above |
| Add domain/subdomain mapping table and admin UI | ❌ | M | Above |

### 1.3 Public Portal & CMS

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Create public-facing layout (no auth required) | ❌ | M | None |
| Build online admission form (student name, DOB, guardian info, documents) | ❌ | L | None |
| Admin admission review/approval dashboard | ❌ | M | Above |
| Auto student ID + credential generation on approval | ❌ | S | Above |
| School website CMS pages (about, board, director message) | ❌ | L | None |
| Public event calendar | ❌ | S | None |

### 1.4 System Settings

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Create `system_settings` table (school name, logo, timezone, currency) | ❌ | S | None |
| Admin settings CRUD controller + Vue page | ❌ | M | Above |
| Email/SMS gateway configuration UI | ❌ | M | None |

---

## MODULE 2: Academic Infrastructure & Class Scheduling

### 2.1 Fix Existing Mismatches 🔧

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Add `room` column to `class_routines` migration | 🔧 | S | None |
| Add `exam_name` + `room` columns to `exam_routines` migration | 🔧 | S | None |
| Update `ClassRoutine` model `$fillable` with `room` | 🔧 | XS | Above |
| Update `ExamRoutine` model `$fillable` with `exam_name`, `room` | 🔧 | XS | Above |
| Fix `Routines.vue`: `form.subject` → `form.subject_id` (dropdown) | 🔧 | M | None |
| Fix `ExamRoutines.vue`: `form.subject` → `form.subject_id`, `form.date` → `form.exam_date` | 🔧 | M | None |
| Fix teacher name rendering: `t.first_name`/`t.last_name` → `t.name` | 🔧 | S | None |
| Add `room` field to routine controllers' validation | 🔧 | S | None |

### 2.2 Subject Management

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Create `Admin\SubjectController` (CRUD + class filter) | ❌ | M | None |
| Add `apiResource('subjects')` route | ❌ | XS | Above |
| Create `Subjects.vue` admin page (table, add/edit modal, search, filter) | ❌ | L | Above |
| Add Subjects link to admin sidebar + router | ❌ | S | Above |

### 2.3 Room Management

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Create `rooms` migration (name, building, floor, capacity, status) | ❌ | S | None |
| Create `Room` model | ❌ | XS | Above |
| Create `Admin\RoomController` (CRUD + building/status filter) | ❌ | M | Above |
| Add `apiResource('rooms')` route | ❌ | XS | Above |
| Add `belongsTo(Room)` to ClassRoutine + ExamRoutine models | ❌ | S | Above |
| Update routine controllers to accept `room_id` + eager-load room | ❌ | S | Above |
| Replace text room inputs with dropdown in Routines.vue + ExamRoutines.vue | ❌ | M | Above |
| Create `Rooms.vue` admin page | ❌ | L | Above |
| Add Rooms link to admin sidebar + router | ❌ | S | Above |

### 2.4 Academic Year & Term Management

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Create `academic_years` migration (name, start_date, end_date, is_current) | ❌ | S | None |
| Create `terms` migration (academic_year_id, name, start_date, end_date, is_current) | ❌ | S | Above |
| Create `AcademicYear` + `Term` models | ❌ | S | Above |
| Create `AcademicYearController` + `TermController` (CRUD + set-current) | ❌ | M | Above |
| Add API routes | ❌ | XS | Above |
| Create `AcademicYears.vue` + `Terms.vue` admin pages | ❌ | L | Above |
| Add links to admin sidebar + router | ❌ | S | Above |

### 2.5 Student Transfer & Capacity Guard

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Add `transfer()` endpoint to StudentController | ❌ | S | None |
| Add transfer route | ❌ | XS | Above |
| Add transfer button + modal to Students.vue | ❌ | M | Above |
| Add capacity guard to StudentController `store()` | ❌ | S | None |

### 2.6 Teacher Exam Routine Management

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Create `Teacher\ExamRoutineController` (index, store, update, destroy scoped to own classes) | ❌ | M | None |
| Add teacher exam routine routes | ❌ | XS | Above |
| Create `teacher/ExamRoutines.vue` page | ❌ | M | Above |
| Add Exam Routines link to teacher sidebar + router | ❌ | S | Above |

### 2.7 Demo Data

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Seed 2 academic years + 4 terms | ❌ | S | 2.4 |
| Seed 8 rooms across 2 buildings | ❌ | S | 2.3 |
| Seed 15+ subjects across classes | ❌ | S | 2.2 |
| Seed 20+ class routines (all weekdays) | ❌ | S | 2.1 |
| Seed 5+ exam routines | ❌ | S | 2.1 |

---

## MODULE 3: Student Lifecycle & Profile Management

### 3.1 Admission Workflow

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Redesign `Application` model with admission-specific fields (child_name, dob, prev_school, documents, photo) | ❌ | M | None |
| Create migration to add admission fields to `applications` | ❌ | S | Above |
| Build guardian-facing admission submission form (public route) | ❌ | L | Above |
| Admin admission review page with approve/reject + auto student creation | ❌ | L | Above |
| Auto-generate admission_no + default password on approval | ❌ | S | Above |

### 3.2 Multi-Guardian Mapping

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Create `student_guardians` pivot table (student_id, guardian_id, relationship_type, is_primary) | ❌ | S | None |
| Update Student model with `belongsToMany(User)` via pivot | ❌ | S | Above |
| Update StudentController to accept multiple guardian_ids | ❌ | M | Above |
| Update Students.vue form to support multiple guardians | ❌ | M | Above |
| Guardian dashboard: multi-child switching | ❌ | L | Above |

### 3.3 Academic Progression

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Create `student_promotions` table (student_id, from_class, to_class, academic_year, status) | ❌ | S | None |
| Admin bulk promotion/retention UI (select class → select target class → confirm) | ❌ | L | Above |
| Section transfer endpoint + UI | ❌ | M | None |
| Historical academic record view (per student) | ❌ | M | None |

### 3.4 Student Profile Management

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Emergency contacts CRUD (controller + Vue page) | ❌ | M | None |
| Medical records CRUD (controller + Vue page) | ❌ | M | None |
| Pickup persons CRUD (controller + Vue page) | ❌ | M | None |
| Document upload/download (controller + Vue page) | ❌ | L | None |
| Bulk student import via Excel | ❌ | L | None |
| Bulk student export via Excel | ❌ | M | None |

---

## MODULE 4: Attendance Monitoring & Policy Automation

### 4.1 Attendance Enhancements

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Teacher attendance management UI (admin marks teacher attendance) | ❌ | M | None |
| Period-wise attendance tracking (link to routine time slots) | ❌ | L | 2.1 |
| Auto-mark absent after configurable cut-off time | ❌ | M | None |
| Attendance dashboard charts (class-wise, date-wise) | ❌ | M | None |
| Excel export of attendance reports | ❌ | M | None |

### 4.2 Attendance Policy

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Attendance policy CRUD controller + Vue page | ❌ | M | None |
| Configure mark deduction rules per absence count | ❌ | S | Above |
| Configure fine/penalty rules | ❌ | S | Above |

### 4.3 SMS Notifications

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Create SMS service class (interface + provider pattern) | ❌ | M | None |
| Add SMS gateway config to `.env.example` + `config/services.php` | ❌ | S | Above |
| Send SMS on student absence (configurable: after N consecutive absences) | ❌ | M | Above |
| Send SMS on leave request status change | ❌ | S | Above |

### 4.4 Leave Requests

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Verify existing leave request flow works end-to-end | ✅ | S | None |
| Add document upload to leave request form | ❌ | M | None |
| Add admin view of all leave requests (not just teacher) | ❌ | S | None |

---

## MODULE 5: Examination, Grading & Seat Planning

### 5.1 Grading Engine

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Create `grading_scales` table (name, min_mark, max_mark, grade, gpa_points) | ❌ | S | None |
| Create `GradingScale` model + CRUD controller | ❌ | M | Above |
| Create `GradingScales.vue` admin page | ❌ | M | Above |
| Auto-calculate grade from marks on evaluation save | ❌ | S | Above |
| GPA/CGPA calculation service (per student per term) | ❌ | L | Above |

### 5.2 Report Card PDF

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Install DOMPDF or wkhtmltopdf Laravel package | ❌ | S | None |
| Create report card PDF template (blade) | ❌ | L | Above |
| Add download endpoint to ReportCardController | ❌ | M | Above |
| Add download button to admin/guardian ReportCards.vue | ❌ | S | Above |
| Academic transcript PDF generation | ❌ | M | Above |

### 5.3 Bulk Marks Entry

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Excel template download for marks entry | ❌ | M | None |
| Bulk marks upload endpoint (parse Excel → create evaluations) | ❌ | L | Above |
| Add upload button to admin Evaluations.vue | ❌ | S | Above |

### 5.4 Exam Seat Planning

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Create `exam_seats` table (exam_routine_id, room_id, student_id, seat_number) | ❌ | S | None |
| Auto-generate seat plan (room capacity → assign students) | ❌ | L | 2.3 |
| Payment verification gate (block seat if fees due) | ❌ | M | 6.1 |
| Print seat plan | ❌ | M | Above |

### 5.5 Admit Cards

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Admit card PDF template (student info, exam schedule, photo) | ❌ | L | 5.2 |
| Bulk generate admit cards per class | ❌ | M | Above |
| Print-optimized layout | ❌ | S | Above |

---

## MODULE 6: Fees, Billing & Financial Management

### 6.1 Invoice System

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Create `invoices` table (invoice_no, student_id, academic_year, total_amount, due_date, status) | ❌ | S | None |
| Create `invoice_items` table (invoice_id, fee_structure_id, amount, description) | ❌ | S | Above |
| Invoice generation endpoint (bulk per class or per student) | ❌ | L | Above |
| Invoice PDF template + download | ❌ | L | None |
| Link fee_records to invoices | ❌ | M | Above |

### 6.2 Payment Receipts

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Payment receipt PDF template | ❌ | M | None |
| Download receipt endpoint | ❌ | S | Above |
| Receipt numbering system | ❌ | S | Above |

### 6.3 Fee Auto-Allocation

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Bulk create fee_records from fee_structure (per class, per term) | ❌ | M | None |
| Late fee penalty calculation (configurable per day/flat) | ❌ | M | None |
| Scholarship/waiver adjustment model + UI | ❌ | M | None |

### 6.4 bKash Integration

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| bKash sandbox merchant API service class | ❌ | L | None |
| Payment initiation endpoint | ❌ | M | Above |
| Payment callback/webhook handler | ❌ | M | Above |
| Online payment UI (guardian pays fee online) | ❌ | L | Above |

### 6.5 Accounting & Ledger

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Create `transactions` table (type: income/expense, category, amount, date, reference) | ❌ | S | None |
| Income tracking (auto from fee payments) | ❌ | M | Above |
| Expense tracking (manual entry) | ❌ | M | Above |
| Balance sheet report | ❌ | L | Above |
| Fee collection summary report | ❌ | M | None |

### 6.6 Payroll

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Create `salary_structures` table (teacher_id, base_salary, allowances JSON, deductions JSON) | ❌ | S | None |
| Create `payslips` table (teacher_id, month, year, gross, deductions, net, status) | ❌ | S | Above |
| Payroll calculation service | ❌ | L | Above |
| Payslip PDF generation | ❌ | M | Above |
| Admin payroll management UI | ❌ | L | Above |

---

## MODULE 7: Communication, Auxiliary & System Security

### 7.1 SMS Gateway

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| SMS service interface + provider pattern | ❌ | M | None |
| Gateway config in `.env` + `config/services.php` | ❌ | S | Above |
| Send SMS from admin (bulk to class/individual) | ❌ | M | Above |
| Auto-SMS triggers (absence, fee due, leave status) | ❌ | M | Above |

### 7.2 Push Notifications (FCM)

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| FCM service class + config | ❌ | M | None |
| Device token registration endpoint | ❌ | S | Above |
| Send push on announcement, grade publish, fee due | ❌ | M | Above |

### 7.3 Group Messaging

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Class-wide messaging (teacher → all students/parents in class) | ❌ | M | None |
| Group chat threads | ❌ | L | None |
| Message read receipts | ❌ | S | None |

### 7.4 Library Module

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Create `books` table (isbn, title, author, copies, available) | ❌ | S | None |
| Create `book_borrowings` table (book_id, student_id, borrow_date, due_date, return_date, fine) | ❌ | S | Above |
| Book catalog CRUD (admin) | ❌ | M | Above |
| Student checkout/return UI | ❌ | M | Above |
| Overdue fine calculation | ❌ | S | Above |

### 7.5 Hostel Module

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Create `hostels` + `rooms` + `room_allocations` tables | ❌ | S | None |
| Hostel CRUD + room allocation UI | ❌ | L | Above |

### 7.6 Transport Module

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Create `vehicles` + `routes` + `route_stops` tables | ❌ | S | None |
| Vehicle/route CRUD + student-route assignment UI | ❌ | L | Above |

### 7.7 Database Backups

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Install spatie/laravel-backup | ❌ | S | None |
| Configure backup schedule (daily cron) | ❌ | S | Above |
| Admin backup management UI (download, restore) | ❌ | M | Above |
| CSV/Excel export for key tables | ❌ | M | None |

### 7.8 MFA & Security

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Two-factor authentication (TOTP via Google Authenticator) | ❌ | L | None |
| Active session management (view + revoke) | ❌ | M | None |
| IP-restricted admin login | ❌ | M | None |
| Rate limiting middleware on auth endpoints | ❌ | S | None |
| Password strength validation | ❌ | S | None |

---

## CROSS-CUTTING CONCERNS

### Infrastructure

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Create `Services/` layer (extract business logic from controllers) | ❌ | L | None |
| Create Form Request classes for all validation | ❌ | L | None |
| Create API Resource classes for consistent JSON responses | ❌ | L | None |
| Add Notifications classes (email, SMS, push) | ❌ | L | None |
| Add Jobs for heavy operations (PDF generation, bulk import, report compilation) | ❌ | L | None |

### Testing

| Task | Status | Size | Dependencies |
|------|--------|------|--------------|
| Write unit tests for all models | ❌ | L | None |
| Write feature tests for all API endpoints | ❌ | L | None |
| Write E2E tests for critical flows (admission, attendance, fees, exams) | ❌ | L | None |
| Achieve 80%+ test coverage | ❌ | L | Above |

---

## PRIORITY PHASING

### Phase 1: Fix & Foundation (Week 1-2)
**Goal:** Existing code works correctly, core infrastructure in place.

1. Fix all Module 2 mismatches (2.1)
2. Subject CRUD (2.2)
3. Room management (2.3)
4. Academic year & terms (2.4)
5. Wire RBAC policies (1.1)
6. Demo data seeding (2.7)

### Phase 2: Student Lifecycle (Week 3-4)
**Goal:** Full admission-to-graduation workflow.

1. Admission workflow (3.1)
2. Multi-guardian mapping (3.2)
3. Student transfer + capacity guard (2.5)
4. Student profile management (3.4)
5. Bulk import/export (3.4)

### Phase 3: Attendance & Exams (Week 5-6)
**Goal:** Complete academic operations.

1. Attendance enhancements (4.1)
2. Attendance policy (4.2)
3. Leave request polish (4.4)
4. Grading engine (5.1)
5. Exam seat planning (5.4)
6. Teacher exam routines (2.6)

### Phase 4: Fees & Billing (Week 7-8)
**Goal:** Financial operations complete.

1. Invoice system (6.1)
2. Payment receipts (6.2)
3. Fee auto-allocation (6.3)
4. Accounting ledger (6.5)
5. bKash integration (6.4)

### Phase 5: Communication & Auxiliaries (Week 9-10)
**Goal:** Full communication suite + auxiliary modules.

1. SMS gateway (7.1)
2. Push notifications (7.2)
3. Group messaging (7.3)
4. Library module (7.4)
5. Database backups (7.7)

### Phase 6: Polish & Security (Week 11-12)
**Goal:** Production-ready.

1. Report card PDF (5.2)
2. Bulk marks entry (5.3)
3. Admit cards (5.5)
4. MFA & security (7.8)
5. Hostel + Transport (7.5, 7.6)
6. Testing (cross-cutting)
7. Performance optimization

---

## FILE REFERENCE

### Key Existing Files
```
app/Http/Controllers/Admin/ClassController.php
app/Http/Controllers/Admin/RoutineController.php
app/Http/Controllers/Admin/ExamRoutineController.php
app/Http/Controllers/Admin/StudentController.php
app/Http/Controllers/Admin/FeeController.php
app/Http/Controllers/Admin/EvaluationController.php
app/Http/Controllers/Admin/ReportCardController.php
app/Models/ClassModel.php
app/Models/Subject.php
app/Models/ClassRoutine.php
app/Models/ExamRoutine.php
app/Models/Student.php
app/Models/User.php
resources/js/pages/admin/Classes.vue
resources/js/pages/admin/Routines.vue
resources/js/pages/admin/ExamRoutines.vue
resources/js/pages/admin/Students.vue
resources/js/pages/admin/Fees.vue
resources/js/pages/admin/Evaluations.vue
resources/js/pages/admin/ReportCards.vue
routes/api.php
resources/js/router.js
resources/js/layouts/AppLayout.vue
database/seeders/DemoDataSeeder.php
```

---
*Generated from codebase audit. Update status as tasks complete.*
