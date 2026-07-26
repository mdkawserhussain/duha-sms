# Implementation Plan: KG-SMS — Duha International School

## Overview

Build a Kindergarten School Management System with three role-separated modules (Admin, Teacher, Guardian) on a Laravel + React/Tailwind + PostgreSQL stack. The system replaces manual diary logs, paper attendance, and fragmented parent communication with a unified digital platform. Full SRS scope — 60+ features across all modules, no deferrals.

## Architecture Decisions

| Decision | Choice | Rationale |
|---|---|---|
| Backend framework | Laravel 11 | User-specified; mature ecosystem, built-in auth, queue system |
| Frontend | React.js + Tailwind CSS | SRS specifies React; Tailwind for rapid UI |
| Database | PostgreSQL | SRS specifies; strong relational model for school data |
| Authentication | Laravel Sanctum (JWT) | Laravel-native; role-based middleware |
| Push notifications | Firebase FCM | Per SRS; Laravel notification channels |
| File storage | Cloudinary / S3 | Per SRS; Laravel filesystem disk |
| Excel export | Maatwebsite/Laravel-Excel | Mature Laravel package for xlsx export |
| API pattern | RESTful JSON API | React SPA consumes Laravel API |

## Vertical Slice Strategy

Each phase delivers one complete user-visible feature path (schema → API → UI) rather than building all DB first, then all API, then all UI.

---

## Phase 1: Project Foundation

### Task 1.1: Laravel project scaffold + composer dependencies
**Description:** Create a fresh Laravel 11 project, configure `.env` with PostgreSQL, install Sanctum, Maatwebsite Excel, and set up basic directory structure.

**Acceptance criteria:**
- [ ] `composer create-project laravel/laravel kg-sms` succeeds
- [ ] `.env` configured for PostgreSQL connection
- [ ] Sanctum installed and configured
- [ ] Maatwebsite/Laravel-Excel installed
- [ ] `php artisan serve` boots without errors

**Verification:**
- [ ] `php artisan migrate` runs cleanly
- [ ] `php artisan route:list` shows default routes

**Dependencies:** None
**Estimated scope:** S — 3-4 files
**Files:** `composer.json`, `.env`, `config/database.php`

---

### Task 1.2: Database schema — users, roles, authentication tables
**Description:** Create migrations for the users table with role enum (admin, teacher, guardian), personal_access_tokens for Sanctum, and a password_resets table. Include fields: name, email, phone, role, password, status (active/pending), timestamps.

**Acceptance criteria:**
- [ ] `users` migration includes: id, name, email (unique), phone (unique), role (enum: admin, teacher, guardian), password, status, email_verified_at, timestamps
- [ ] `password_resets` migration exists
- [ ] Sanctum personal_access_tokens migration present
- [ ] `User` model with role-based scope methods

**Verification:**
- [ ] `php artisan migrate` creates all tables
- [ ] Factory + seeder creates a test admin user

**Dependencies:** Task 1.1
**Estimated scope:** S — 2-3 files
**Files:** `database/migrations/*_create_users_table.php`, `app/Models/User.php`

---

### Task 1.3: Role-based auth API — register, login, logout, me
**Description:** Build API endpoints for admin login (seeded credentials), teacher login (ID + DOB), guardian self-registration (phone number), and logout. Include role-based middleware and Sanctum token management.

**Acceptance criteria:**
- [ ] `POST /api/login` — accepts email+password, returns Sanctum token + role
- [ ] `POST /api/logout` — invalidates current token
- [ ] `GET /api/me` — returns authenticated user profile with role
- [ ] `POST /api/guardian/register` — phone-based self-registration, creates user with status=pending
- [ ] `POST /api/admin/verify-guardian/{id}` — admin approves guardian
- [ ] Role middleware blocks cross-role access (e.g., teacher cannot access admin endpoints)
- [ ] Password update endpoint for first-login flow

**Verification:**
- [ ] Tests: login returns token, logout invalidates, /me returns user, role middleware blocks
- [ ] Postman/curl: full login → access → logout flow

**Dependencies:** Task 1.2
**Estimated scope:** M — 4-5 files
**Files:** `routes/api.php`, `app/Http/Controllers/Auth/*`, `app/Http/Middleware/RoleMiddleware.php`

---

### Task 1.4: React project scaffold + auth UI
**Description:** Create a React + Vite + Tailwind project. Build login page (role-aware), registration page for guardians, and authenticated dashboard shell with role-based sidebar navigation.

**Acceptance criteria:**
- [ ] React + Vite + Tailwind project created
- [ ] Login page: email/password form, POSTs to `/api/login`, stores token in localStorage
- [ ] Guardian registration page: phone input, POSTs to `/api/guardian/register`
- [ ] Dashboard shell: sidebar with role-specific navigation links
- [ ] Protected routes: redirect to login if no token
- [ ] Axios instance configured with auth interceptor

**Verification:**
- [ ] `npm run dev` starts dev server
- [ ] Can log in as admin, see admin dashboard shell
- [ ] Guardian registration flow works end-to-end

**Dependencies:** Task 1.3
**Estimated scope:** M — 5-6 files
**Files:** `resources/js/*`, `tailwind.config.js`, `vite.config.js`

---

## Checkpoint: Foundation
- [ ] Laravel boots with PostgreSQL
- [ ] Auth flow works (login → token → /me → logout)
- [ ] Role middleware blocks cross-role access
- [ ] React shell loads with role-based nav
- [ ] Guardian self-registration → admin approval flow works

---

## Phase 2: Admin Module — User & Teacher Management

### Task 2.1: Student & Guardian CRUD (Admin)
**Description:** Admin API endpoints and React UI for managing guardian accounts and their linked students. CRUD for guardians (list, create, edit, soft-delete) and students (list, create, edit, assign to guardian/class). Include search and filter on guardian list.

**Acceptance criteria:**
- [ ] `students` migration: id, guardian_id (FK), name, dob, class_id (FK nullable), medical_info, status, timestamps
- [ ] `guardians` migration: user_id (FK), emergency_contacts (JSON), authorized_pickup (JSON), timestamps
- [ ] Admin can list guardians with search/filter, create, edit, soft-delete
- [ ] Admin can add/edit/remove students under a guardian
- [ ] Guardian list shows verification status (pending/approved)
- [ ] Student list filterable by class, guardian

**Verification:**
- [ ] Tests: CRUD operations return correct status codes, soft-delete works
- [ ] UI: create guardian → add student → verify shows in list

**Dependencies:** Task 1.4
**Estimated scope:** L — 6-8 files
**Files:** `database/migrations/*_students_table.php`, `database/migrations/*_guardians_table.php`, `app/Models/*`, `app/Http/Controllers/Admin/*`, React components

---

### Task 2.2: Teacher CRUD (Admin)
**Description:** Admin API and UI for managing teacher profiles and class assignments. Create, edit, remove teachers. Assign teachers to classes. View teacher attendance.

**Acceptance criteria:**
- [ ] `teachers` migration: user_id (FK), employee_id, assigned_classes (JSON or pivot), status, timestamps
- [ ] Admin can list, create, edit, soft-delete teachers
- [ ] Admin can assign teachers to specific classes
- [ ] Teacher list shows assigned classes
- [ ] Teacher attendance view (manual input/removal)

**Verification:**
- [ ] Tests: teacher CRUD, class assignment
- [ ] UI: create teacher → assign class → verify assignment persists

**Dependencies:** Task 2.1 (needs class model first)
**Estimated scope:** M — 4-5 files
**Files:** `database/migrations/*_teachers_table.php`, `app/Models/Teacher.php`, `app/Http/Controllers/Admin/TeacherController.php`

---

### Task 2.3: Class & Attendance Policy Management (Admin)
**Description:** Admin can create/edit/delete classes, set capacity limits, transfer students between classes, and set school-wide attendance policy (sole owner).

**Acceptance criteria:**
- [ ] `classes` migration: id, name, capacity, teacher_id (FK nullable), timestamps
- [ ] CRUD for classes: create, edit, delete, view enrolled students
- [ ] Student transfer between classes (update class_id)
- [ ] `attendance_policies` migration: id, school_id, policy_rules (JSON), timestamps
- [ ] Admin is sole owner of attendance policy — teachers view-only
- [ ] Capacity enforcement warning when class is full

**Verification:**
- [ ] Tests: class CRUD, transfer student, policy read-only for teacher role
- [ ] UI: create class → assign teacher → transfer student → verify

**Dependencies:** Task 2.1
**Estimated scope:** M — 4-5 files
**Files:** `database/migrations/*_classes_table.php`, `app/Models/ClassRoom.php`, controllers + React components

---

## Checkpoint: Admin User Management
- [ ] Admin can manage guardians, students, teachers, classes
- [ ] Guardian verification queue works
- [ ] Class assignment and transfer works
- [ ] Attendance policy set by admin, view-only by teachers

---

## Phase 3: Admin Module — Attendance, Diary & Reports

### Task 3.1: Attendance system (Admin view + Teacher mark)
**Description:** Teachers mark daily attendance (Present/Absent). Admin views class-wise and student-wise attendance, generates monthly summaries, exports to Excel. Guardian-submitted absence/leave notifications visible when marking.

**Acceptance criteria:**
- [ ] `attendance_records` migration: id, student_id, date, status (present/absent), marked_by, timestamps
- [ ] `leave_requests` migration: id, student_id, guardian_id, date_from, date_to, reason, status (pending/approved/rejected), timestamps
- [ ] Teacher API: mark attendance (bulk per class), view history
- [ ] Admin API: view class-wise reports, monthly summaries, export to Excel
- [ ] Guardian API: submit advance leave request
- [ ] Teacher sees pending leave requests when marking attendance
- [ ] Excel export via Maatwebsite package

**Verification:**
- [ ] Tests: attendance CRUD, leave request flow, Excel export generates valid file
- [ ] UI: teacher marks attendance → admin sees report → exports Excel

**Dependencies:** Task 2.3
**Estimated scope:** L — 6-8 files
**Files:** migrations, models, controllers, `app/Exports/AttendanceExport.php`, React components

---

### Task 3.2: Daily diary system (Teacher write, Admin/Guardian view)
**Description:** Teachers record daily activities, meals, behavior observations per student. Bulk homework assignment. Admin views student-wise diary with search/filter. Guardians view active diary and browse history.

**Acceptance criteria:**
- [ ] `diary_entries` migration: id, student_id, teacher_id, date, activities (JSON), meals (JSON), behavior, homework, timestamps
- [ ] Teacher API: create/edit diary entries, bulk-add homework for class
- [ ] Admin API: view student-wise diary with search/filter by date, student, class
- [ ] Guardian API: view own child's diary (current + historical by date/week/month)
- [ ] Diary entries timestamped and linked to teacher
- [ ] Past diary history browsable

**Verification:**
- [ ] Tests: diary CRUD, bulk homework, guardian scoped to own child
- [ ] UI: teacher writes diary → guardian sees same-day entry → admin sees all

**Dependencies:** Task 3.1
**Estimated scope:** L — 6-8 files
**Files:** migrations, models, controllers, React components

---

## Checkpoint: Admin Attendance & Diary
- [ ] Attendance marking → reporting → Excel export works
- [ ] Diary entry by teacher → viewable by admin and guardian
- [ ] Leave request flow works end-to-end

---

## Phase 4: Admin Module — Calendar, Fees & Notices

### Task 4.1: Fee management (Admin set, Guardian view)
**Description:** Admin sets fee structure per class, creates fee categories, marks paid/due per student. Guardians view monthly fee breakdown and status.

**Acceptance criteria:**
- [ ] `fee_structures` migration: id, class_id, category, amount, frequency, timestamps
- [ ] `fee_records` migration: id, student_id, fee_structure_id, month, year, status (paid/due), paid_at, timestamps
- [ ] Admin API: CRUD fee structures, mark paid/due per student
- [ ] Guardian API: view own child's fee breakdown and status
- [ ] Fee summary per class

**Verification:**
- [ ] Tests: fee CRUD, payment status update, guardian scoped view
- [ ] UI: admin sets fees → guardian sees breakdown

**Dependencies:** Task 2.1
**Estimated scope:** M — 4-5 files

---

### Task 4.2: Calendar, events & notices (Admin manage, all view)
**Description:** Admin creates school events, adds holidays to calendar, publishes school-wide and class-level announcements. Teachers/Guardians view published notices and events.

**Acceptance criteria:**
- [ ] `events` migration: id, title, description, date, type (event/holiday), created_by, timestamps
- [ ] `notices` migration: id, title, content, scope (school-wide/class-level), class_id nullable, published_by, timestamps
- [ ] Admin API: CRUD events, CRUD notices
- [ ] Teacher API: view notices and events, issue class-level group notices
- [ ] Guardian API: view notices and events
- [ ] School-wide notices visible to all; class-level only to that class's guardians

**Verification:**
- [ ] Tests: event/notice CRUD, scope filtering
- [ ] UI: admin creates event → all roles see it

**Dependencies:** Task 2.3
**Estimated scope:** M — 4-5 files

---

### Task 4.3: Reports & analytics dashboard (Admin)
**Description:** Admin dashboard with: total student count by class, new admissions vs withdrawals, class-wise attendance percentage, summarized student progress reports, class performance analysis.

**Acceptance criteria:**
- [ ] Dashboard API aggregates: student count by class, admission/withdrawal trends, attendance percentages
- [ ] Student progress report aggregation (from diary + evaluation marks)
- [ ] Class performance analysis (attendance + marks combined)
- [ ] React dashboard with charts/summary cards
- [ ] Data filterable by date range, class

**Verification:**
- [ ] Tests: report aggregation queries return correct data
- [ ] UI: dashboard loads with real data, filters work

**Dependencies:** Tasks 3.1, 3.2
**Estimated scope:** L — 6-8 files

---

## Checkpoint: Admin Complete
- [ ] All Admin module features functional
- [ ] Fee management works end-to-end
- [ ] Calendar, events, notices work
- [ ] Reports dashboard shows aggregated data
- [ ] **Review with human before proceeding to Teacher module**

---

## Phase 5: Teacher Module

### Task 5.1: Teacher diary & student management
**Description:** Teachers can view student-wise daily diaries, record activities/meals/behavior, edit existing entries, bulk-add homework, access full past diary history. Manage student profiles and directory.

**Acceptance criteria:**
- [ ] Teacher API: list own class students, view diary entries, create/edit entries
- [ ] Bulk homework endpoint: accepts class_id + homework text, creates entries for all students
- [ ] Full diary history paginated
- [ ] Student profile view (limited fields vs admin)
- [ ] Teacher scoped to own class only

**Verification:**
- [ ] Tests: diary CRUD scoped to own class, bulk homework creates N entries
- [ ] UI: teacher writes diary for all students, edits, views history

**Dependencies:** Task 3.2 (diary schema already exists)
**Estimated scope:** M — 4-5 files

---

### Task 5.2: Teacher attendance & academic features
**Description:** Teachers mark daily attendance, view historical records, download Excel summaries. Input student evaluation marks, publish report cards (controlled — visible to guardians after publish), upload individual work samples.

**Acceptance criteria:**
- [ ] Attendance endpoints scoped to teacher's class
- [ ] Teacher can generate/download Excel attendance summaries
- [ ] `evaluation_marks` migration: student_id, subject, marks, term, teacher_id, timestamps
- [ ] `report_cards` migration: student_id, term, published (boolean), published_at, timestamps
- [ ] Teacher inputs marks → report card draft → publish → guardian-visible
- [ ] File upload endpoint for work samples (Cloudinary/S3)

**Verification:**
- [ ] Tests: attendance scoped to class, marks CRUD, publish flow, file upload
- [ ] UI: mark attendance → upload work sample → input marks → publish report card

**Dependencies:** Task 5.1
**Estimated scope:** L — 6-8 files

---

### Task 5.3: Teacher communication & applications
**Description:** Direct messaging with individual guardians, class-wide group notices, full message history, exam routine management for own class/subject, submit applications to Admin, push notifications.

**Acceptance criteria:**
- [ ] `messages` migration: id, sender_id, receiver_id, class_id nullable (for group), content, read_at, timestamps
- [ ] Direct messaging: teacher ↔ guardian
- [ ] Class-wide notices: teacher posts → all guardians in that class see it
- [ ] `exam_routines` migration: id, class_id, subject, date, time, teacher_id, timestamps
- [ ] Teacher manages exam routine for own class/subject only
- [ ] `applications` migration: id, from_user_id, to_admin, subject, body, status, timestamps
- [ ] Firebase FCM push notification on new messages and diary comments

**Verification:**
- [ ] Tests: message send/receive, class notice scoping, exam routine own-class-only
- [ ] UI: teacher messages guardian, posts class notice, creates exam routine

**Dependencies:** Task 5.2
**Estimated scope:** L — 6-8 files

---

## Checkpoint: Teacher Complete
- [ ] All Teacher module features functional
- [ ] Messaging works between teacher and guardian
- [ ] Push notifications deliver on new messages/diary entries
- [ ] **Review with human before proceeding to Guardian module**

---

## Phase 6: Guardian Module

### Task 6.1: Guardian profile & child management
**Description:** Guardians view/update personal profile (changes require Admin approval), manage emergency contacts, medical info, authorized pickup persons, submit documents electronically.

**Acceptance criteria:**
- [ ] Guardian profile update creates approval request (not direct edit)
- [ ] `profile_change_requests` migration: id, guardian_id, changes (JSON), status, admin_notes, timestamps
- [ ] Emergency contact CRUD (guarded by approval flow)
- [ ] Medical info update
- [ ] Authorized pickup person CRUD
- [ ] Document upload endpoint (DOB cert, academic records)
- [ ] Profile status/verification state visible to guardian

**Verification:**
- [ ] Tests: profile update creates pending request, admin can approve/reject
- [ ] UI: guardian edits profile → sees "pending approval" → admin approves → changes take effect

**Dependencies:** Task 1.3 (auth), Task 2.1 (guardian model)
**Estimated scope:** M — 4-5 files

---

### Task 6.2: Guardian diary, attendance & fees
**Description:** Guardians view active daily diary, browse history by date/week/month, leave comments on entries. Monitor attendance, view teacher remarks and evaluation breakdowns, view uploaded work samples, review fee breakdown.

**Acceptance criteria:**
- [ ] Diary view: current day + paginated history with date/week/month filter
- [ ] `diary_comments` migration: id, diary_entry_id, guardian_id, content, timestamps
- [ ] Guardian can comment on diary entries
- [ ] Attendance view: own child's attendance history
- [ ] Evaluation breakdown view: per-subject marks and teacher remarks
- [ ] Work samples view (file URLs from teacher uploads)
- [ ] Fee breakdown view per month
- [ ] Report card view (once published by teacher)
- [ ] Guardian scoped to own child only

**Verification:**
- [ ] Tests: diary view scoped, comment CRUD, attendance view, fee view
- [ ] UI: guardian sees diary, comments, attendance, fees, report cards

**Dependencies:** Tasks 3.1, 3.2, 4.1, 5.2
**Estimated scope:** L — 6-8 files

---

### Task 6.3: Guardian communication & applications
**Description:** Direct messaging with school (teachers), submit applications to administration, push notifications for messages and announcements.

**Acceptance criteria:**
- [ ] Guardian can message teachers directly
- [ ] Guardian can view class-level notices
- [ ] Guardian can submit applications to admin
- [ ] Push notifications for new messages and announcements
- [ ] Full conversation/message history view

**Verification:**
- [ ] Tests: message send/receive, application CRUD
- [ ] UI: guardian messages teacher, submits application, receives notification

**Dependencies:** Task 5.3 (message schema exists)
**Estimated scope:** M — 4-5 files

---

## Checkpoint: Guardian Complete
- [ ] All Guardian module features functional
- [ ] Profile approval flow works end-to-end
- [ ] Guardian sees diary, attendance, fees, report cards
- [ ] Guardian ↔ Teacher messaging works
- [ ] **Full feature parity with SRS verified**

---

## Phase 7: Notifications, File Storage & Excel Export

### Task 7.1: Firebase FCM integration
**Description:** Integrate Firebase Cloud Messaging for push notifications. Register device tokens on login, send push on: new diary entry, new message, notice published, leave request status change.

**Acceptance criteria:**
- [ ] `device_tokens` migration: user_id, token, platform, timestamps
- [ ] Token registration endpoint (called on frontend login)
- [ ] Notification dispatch: diary entry, message, notice, leave status
- [ ] Notifications configurable per guardian (opt-in/opt-out)
- [ ] Laravel notification channel for FCM

**Verification:**
- [ ] Tests: token registration, notification dispatched on trigger event
- [ ] Manual: login on device → create diary entry → push notification received

**Dependencies:** Tasks 3.2, 5.3
**Estimated scope:** M — 4-5 files

---

### Task 7.2: Cloudinary/S3 file storage
**Description:** Configure Laravel filesystem for Cloudinary or S3. File upload endpoints for: student work samples (teacher), guardian document uploads, profile images. Serve files via signed URLs.

**Acceptance criteria:**
- [ ] Laravel filesystem disk configured for Cloudinary/S3
- [ ] Upload controller handles multiple file types
- [ ] Signed URL generation for secure file access
- [ ] File size limits enforced (per role)
- [ ] File cleanup on soft-delete of parent record

**Verification:**
- [ ] Tests: upload succeeds, signed URL accessible, cleanup on delete
- [ ] Manual: upload image via teacher → guardian can view it

**Dependencies:** Task 5.2 (work samples)
**Estimated scope:** M — 3-4 files

---

### Task 7.3: Excel export (attendance, reports)
**Description:** Implement Excel export for attendance monthly summaries (admin + teacher) and class performance reports using Maatwebsite/Laravel-Excel.

**Acceptance criteria:**
- [ ] Attendance export: class-wise, date range, includes student names + P/A status
- [ ] Monthly summary export: aggregate attendance percentage per student
- [ ] Class performance export: attendance + marks combined
- [ ] Export accessible from admin and teacher dashboards
- [ ] Exports generate valid `.xlsx` files

**Verification:**
- [ ] Tests: export generates valid Excel with correct data
- [ ] Manual: download attendance report → open in Excel → verify data

**Dependencies:** Task 3.1
**Estimated scope:** S — 2-3 files
**Files:** `app/Exports/*`, `app/Http/Controllers/ExportController.php`

---

## Checkpoint: Infrastructure Complete
- [ ] Push notifications work on real devices
- [ ] File upload/download works via Cloudinary/S3
- [ ] Excel exports generate valid files
- [ ] **All SRS features implemented**

---

## Phase 8: Security, Testing & Polish

### Task 8.1: Security hardening
**Description:** CSRF protection, rate limiting on auth endpoints, input validation on all API endpoints, SQL injection prevention (Eloquent), XSS prevention, CORS configuration, audit trail for sensitive actions.

**Acceptance criteria:**
- [ ] Rate limiting: 5 attempts/min on login, 10/min on registration
- [ ] All API inputs validated with FormRequest classes
- [ ] CORS configured for frontend origin only
- [ ] Audit log table: user_id, action, model, model_id, timestamps
- [ ] Sensitive actions logged (guardian approval, fee changes, student transfer)

**Verification:**
- [ ] Tests: rate limit enforced, validation rejects bad input, audit log records actions
- [ ] Manual: try SQL injection via form → blocked

**Dependencies:** All previous phases
**Estimated scope:** M — 4-5 files

---

### Task 8.2: Full test suite
**Description:** Write comprehensive tests covering all modules: unit tests for models, feature tests for API endpoints, role-based access tests.

**Acceptance criteria:**
- [ ] Unit tests for all models (scopes, relationships)
- [ ] Feature tests for all API endpoints (CRUD + edge cases)
- [ ] Role-based access tests: each role can only access permitted endpoints
- [ ] Test coverage > 80%
- [ ] All tests pass

**Verification:**
- [ ] `php artisan test` — all green
- [ ] Coverage report shows > 80%

**Dependencies:** Task 8.1
**Estimated scope:** L — 8+ files

---

### Task 8.3: UI polish, responsive design & accessibility
**Description:** Ensure all React components are responsive (mobile-friendly for guardians), consistent styling, loading states, error states, empty states, form validation UX.

**Acceptance criteria:**
- [ ] All pages responsive on mobile (guardian primary device)
- [ ] Loading spinners on API calls
- [ ] Error messages displayed on failed operations
- [ ] Empty states for lists with no data
- [ ] Form validation errors inline
- [ ] Consistent color scheme and typography

**Verification:**
- [ ] Manual: test on mobile viewport, verify all states
- [ ] Accessibility: keyboard navigation works, contrast ratios pass

**Dependencies:** All feature phases
**Estimated scope:** L — 8+ files

---

## Checkpoint: Quality Complete
- [ ] Security hardening applied
- [ ] Full test suite passing > 80% coverage
- [ ] UI polished and responsive
- [ ] **Ready for deployment**

---

## Phase 9: Deployment & Handoff

### Task 9.1: VPS setup, Nginx, SSL
**Description:** Provision VPS, install PHP 8.2+, PostgreSQL, Nginx, configure site, SSL via Let's Encrypt, deploy Laravel app.

**Acceptance criteria:**
- [ ] VPS provisioned with Ubuntu 22.04
- [ ] PHP 8.2+, Composer, PostgreSQL installed
- [ ] Nginx configured as reverse proxy to PHP-FPM
- [ ] SSL/HTTPS via Let's Encrypt active
- [ ] Laravel app deployed and accessible via HTTPS
- [ ] `.env` configured for production

**Verification:**
- [ ] `https://domain.com` loads login page
- [ ] `php artisan migrate` runs on production

**Dependencies:** Task 8.2
**Estimated scope:** M — 5-6 files (server config)

---

### Task 9.2: Seed data, admin training & documentation
**Description:** Seed production with admin credentials, sample classes, sample teacher. Provide admin with login credentials and basic usage documentation.

**Acceptance criteria:**
- [ ] Admin account seeded with secure credentials
- [ ] Sample classes and teachers seeded
- [ ] Admin receives written login credentials
- [ ] One-page usage guide for admin
- [ ] Handoff document: architecture, credentials, maintenance procedures

**Verification:**
- [ ] Admin logs in, can see seeded data
- [ ] Usage guide reviewed by admin

**Dependencies:** Task 9.1
**Estimated scope:** S — 2-3 files

---

## Checkpoint: Deployed & Handed Off
- [ ] Production site live and accessible
- [ ] All features working on production
- [ ] Admin trained, documentation provided
- [ ] **Project complete — client sign-off ready**

---

## Risks and Mitigations

| Risk | Likelihood | Impact | Mitigation |
|---|---|---|---|
| Guardian onboarding friction (phone registration) | Medium | High | Admin can create accounts as fallback; keep flow minimal |
| VPS single point of failure | Medium | Medium | Document backup/restore; consider managed DB later |
| Scope creep from SRS interpretation gaps | Medium | High | SRS is the source of truth; changes require addendum per T&C |
| Push notification delivery reliability | Low | Medium | Fallback to in-app notifications; monitor delivery rates |
| Excel export memory on large datasets | Low | Medium | Use chunked export via Maatwebsite; paginate if needed |
| Team velocity on 45-day timeline | Medium | High | Prioritize vertical slices; get core flow working first |

## Open Questions

- [ ] React frontend — is it a separate SPA (Vite) or Laravel Blade with React components? Affects deployment and auth flow.
- [ ] Multi-class teacher assignment — can one teacher teach multiple classes? SRS says "assign teachers to specific classes" (plural).
- [ ] Guardian phone number — is this the primary identifier? What if a guardian has multiple children at the school?
- [ ] Fee payment — SRS says online payment is future add-on, but fee records still need paid/due status. Manual marking only for now?
- [ ] Data migration — are there existing records to import, or greenfield?

---
*Status: PLAN — ready for implementation. Start with Phase 1: Foundation.*
