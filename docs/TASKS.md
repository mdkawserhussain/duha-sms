# KG-SMS (Kindergarten School Management System) Task Breakdown

**Stack:** Laravel 13 + Vue.js 3.5 (with Vapor Mode) + Tailwind CSS 4 (CSS-first configuration, Oxide engine) + Pinia 4

---

## Phase 1: Foundation & Setup (Week 1-2)

### Environment Setup
- [ ] Initialize Laravel 13 project
  - [ ] Configure `composer.json` and install dependencies
- [ ] Install and configure Vue 3.5 via Vite 8
  - [ ] Setup Vue plugin for Vite 8
- [ ] Install Tailwind CSS 4 and configure (CSS-first with @theme directives)
  - [ ] Configure `tailwind.config.js` and `postcss.config.js`
- [ ] Install Pinia 4 state management
  - [ ] Setup root store configuration
- [ ] Install Vue Router 5
  - [ ] Setup basic route structure and router instance
- [ ] Configure Laravel Sanctum 4
  - [ ] Publish Sanctum 4 config and configure stateful domains
- [ ] Setup MySQL 9.7 LTS database
  - [ ] Create initial database and configure `.env`
- [ ] Setup Redis 8 for cache and queues
  - [ ] Configure Redis 8 connection in `.env` and `database.php`
- [ ] Configure `.env` for all environments
  - [ ] Create `.env.example` with standard defaults
- [ ] Setup Git repository and branching strategy
  - [ ] Initialize Git, add `.gitignore`, define `main` and `develop` branches

### Database & Migrations
- [ ] Create users migration (with role enum, status, first_login flag, fcm_token)
- [ ] Create students migration
- [ ] Create classes migration
- [ ] Create class_teacher pivot migration
- [ ] Create subjects migration
- [ ] Create attendance_policy migration
- [ ] Create student_attendances migration
- [ ] Create teacher_attendances migration
- [ ] Create diary_entries migration
- [ ] Create diary_comments migration
- [ ] Create evaluations migration
- [ ] Create report_cards migration
- [ ] Create work_samples migration
- [ ] Create fee_structures migration
- [ ] Create fee_records migration
- [ ] Create class_routines migration
- [ ] Create exam_routines migration
- [ ] Create events migration
- [ ] Create announcements migration
- [ ] Create messages migration
- [ ] Create emergency_contacts migration
- [ ] Create medical_records migration
- [ ] Create pickup_persons migration
- [ ] Create documents migration (polymorphic)
- [ ] Create profile_change_requests migration
- [ ] Create applications migration
- [ ] Create leave_notifications migration
- [ ] Create activity_logs migration
- [ ] Create all Eloquent models with relationships
  - [ ] Define `hasMany`, `belongsTo`, and polymorphic relationships on each model
- [ ] Create database seeders (AdminSeeder, DemoDataSeeder)
  - [ ] Implement factories for all primary entities

### Authentication System
- [ ] Implement Sanctum 4 SPA authentication
  - [ ] Configure CORS and session domains
- [ ] Create LoginController (email/phone + password)
  - [ ] Handle validation and throttling
- [ ] Create RegisterController (guardian self-registration by phone)
  - [ ] Handle OTP/verification logic stub
- [ ] Create PasswordController (change password, reset)
- [ ] Implement role-based middleware (admin, teacher, guardian)
- [ ] Implement first-login password change enforcement
  - [ ] Create middleware to intercept standard routes if `first_login` is true
- [ ] Create AdminSeeder with auto-seeded credentials
- [ ] Frontend: Login page component
  - [ ] Create form with email/phone, password, and validation
- [ ] Frontend: Guardian registration page
  - [ ] Create multi-step form for guardian details
- [ ] Frontend: Password change/reset pages
- [ ] Frontend: Auth store (Pinia 4) with Sanctum 4 CSRF handling
  - [ ] Implement `login`, `logout`, `fetchUser` actions
- [ ] Frontend: Route guards for role-based access
  - [ ] Intercept routes based on user roles and auth status

### Base UI Layout
- [ ] Design and implement app shell layout
- [ ] Sidebar navigation component (role-aware)
  - [ ] Render dynamic links based on user role
- [ ] Top header/navbar component
  - [ ] Include user avatar, notification bell, and logout
- [ ] Breadcrumb component
- [ ] Dashboard layout wrapper
- [ ] Responsive mobile menu
  - [ ] Implement hamburger toggle and off-canvas sidebar
- [ ] Base UI components:
  - [ ] Button
  - [ ] Input (text, password, number)
  - [ ] Select
  - [ ] Modal
  - [ ] Table (sortable, paginated)
  - [ ] Card
  - [ ] Badge (status indicators)
  - [ ] Alert (success, error, warning)
  - [ ] Pagination
  - [ ] SearchBar
  - [ ] FilterDropdown
  - [ ] DatePicker
  - [ ] FileUpload
  - [ ] LoadingSpinner
  - [ ] EmptyState
  - [ ] Avatar
- [ ] Design tokens: colors, typography, spacing in Tailwind config
- [ ] Dark mode support (optional)

---

## Phase 2: Admin Module (Week 3-4)

### Admin Dashboard
- [ ] Backend: DashboardController (stats API)
  - [ ] Aggregate counts for students, teachers, classes, pending verifications
- [ ] Frontend: Dashboard page with stat cards
- [ ] Frontend: Quick action buttons (Add Student, Send Notice)
- [ ] Frontend: Recent activity feed component

### Guardian/User Management
- [ ] Backend: GuardianController (index, store, show, update, destroy)
- [ ] Backend: Guardian search and filter (by name, phone, status)
- [ ] Backend: GuardianVerificationController (pending list, approve, reject)
- [ ] Backend: ProfileChangeRequestController (list, approve, reject)
- [ ] Backend: Form validation requests for all guardian operations
- [ ] Frontend: Guardian list page with search, filter, pagination
- [ ] Frontend: Add/Edit guardian modal/page
- [ ] Frontend: Guardian detail page
- [ ] Frontend: Verification queue page
- [ ] Frontend: Profile change approval page

### Student Management
- [ ] Backend: StudentController (index, store, show, update, destroy)
- [ ] Backend: Student search and filter (by class, name, status)
- [ ] Backend: Student transfer between classes
- [ ] Backend: Form validation requests
- [ ] Frontend: Student list page with search, filter, pagination
- [ ] Frontend: Add/Edit student form (with guardian selection)
- [ ] Frontend: Student detail/profile page
- [ ] Frontend: Student transfer modal

### Teacher Management
- [ ] Backend: TeacherController (CRUD)
- [ ] Backend: Teacher class assignment logic
- [ ] Backend: TeacherAttendanceController (index, store, update, destroy)
- [ ] Backend: Form validation requests
- [ ] Frontend: Teacher list page
- [ ] Frontend: Add/Edit teacher form
- [ ] Frontend: Teacher detail page with class assignments
- [ ] Frontend: Teacher attendance management page

### Class Management
- [ ] Backend: ClassController (CRUD)
- [ ] Backend: Class capacity monitoring logic
- [ ] Backend: Class teacher assignment
- [ ] Frontend: Class list page
- [ ] Frontend: Add/Edit class form
- [ ] Frontend: Class detail page (students, teachers, capacity bar)

### Admin Settings & Documents
- [ ] Backend: SettingsController (email config, SMS config)
- [ ] Backend: DocumentController (CRUD school documents with file upload)
- [ ] Backend: ApplicationController (list guardian/teacher applications)
- [ ] Frontend: Settings page (email, SMS tabs)
- [ ] Frontend: Documents management page
- [ ] Frontend: Applications inbox page

---

## Phase 3: Teacher Module (Week 4-5)

### Teacher Dashboard
- [ ] Backend: Teacher dashboard API (today's schedule, recent diary, class stats)
- [ ] Frontend: Teacher dashboard page

### Diary Management
- [ ] Backend: DiaryEntryController (CRUD for teacher's classes)
- [ ] Backend: Bulk homework creation endpoint
- [ ] Backend: Diary search and filter (by student, date, class)
- [ ] Backend: DiaryCommentController (for viewing guardian comments)
- [ ] Frontend: Diary entry list page with filters
- [ ] Frontend: Create/Edit diary entry form (activities, meals, behavior, homework)
- [ ] Frontend: Bulk homework assignment form
- [ ] Frontend: Diary entry detail page with comments
- [ ] Frontend: Diary history browser (date/week/month)

### Teacher Attendance
- [ ] Backend: StudentAttendanceController (mark P/A for teacher's class)
- [ ] Backend: Attendance history and monthly summary API
- [ ] Backend: Excel export endpoint (maatwebsite/excel 3.1)
- [ ] Backend: Attendance policy view endpoint
- [ ] Backend: Leave notification display in attendance marking
- [ ] Frontend: Daily attendance marking page (class roster with P/A toggle)
- [ ] Frontend: Attendance history page with filters
- [ ] Frontend: Monthly summary view with Excel download button
- [ ] Frontend: Leave notification indicators on attendance page

### Student Academic Management
- [ ] Backend: EvaluationController (input marks per subject/exam)
- [ ] Backend: ReportCardController (generate, publish)
- [ ] Backend: WorkSampleController (upload, list)
- [ ] Frontend: Evaluation marks input page (per class, per exam)
- [ ] Frontend: Report card generation and publish page
- [ ] Frontend: Work sample upload page

### Student Profile & Health (Teacher View)
- [ ] Backend: Student profile view API (teacher scope)
- [ ] Backend: Health info, emergency contacts, pickup persons view API
- [ ] Frontend: Student directory page (teacher's classes)
- [ ] Frontend: Student profile detail (read-only health/emergency/pickup)

### Teacher Schedule
- [ ] Backend: Schedule/routine API (teacher's classes)
- [ ] Backend: Exam routine view/add/edit (own class/subject)
- [ ] Frontend: Weekly schedule/timetable view
- [ ] Frontend: Exam routine view with add/edit for own subjects

### Teacher Communication
- [ ] Backend: Teacher message send/receive API (to guardians)
- [ ] Backend: Class-level notice CRUD
- [ ] Backend: Application submission to admin
- [ ] Frontend: Message inbox/compose pages
- [ ] Frontend: Class notice create/list page
- [ ] Frontend: Application submission form

---

## Phase 4: Guardian Module (Week 5-6)

### Guardian Registration & Onboarding
- [ ] Backend: Guardian registration API (phone-based)
- [ ] Backend: Registration verification queue (linked to admin approval)
- [ ] Frontend: Registration page (phone, name, basic info)
- [ ] Frontend: Pending approval status page

### Guardian Dashboard
- [ ] Backend: Guardian dashboard API (child summary, recent diary, upcoming events)
- [ ] Frontend: Guardian dashboard page

### Guardian Profile
- [ ] Backend: Profile view/update API (with change request generation)
- [ ] Backend: Emergency contact CRUD
- [ ] Backend: Medical record update
- [ ] Backend: Pickup person CRUD
- [ ] Backend: Document upload API
- [ ] Frontend: Profile view/edit page (with pending changes indicator)
- [ ] Frontend: Emergency contacts management
- [ ] Frontend: Medical info form
- [ ] Frontend: Pickup persons management
- [ ] Frontend: Document upload page

### Guardian Diary View
- [ ] Backend: Diary view API (guardian's child only)
- [ ] Backend: Diary comment creation API
- [ ] Frontend: Daily diary view page
- [ ] Frontend: Diary history browser
- [ ] Frontend: Comment form on diary entries

### Guardian Academic & Attendance
- [ ] Backend: Attendance view API (guardian's child)
- [ ] Backend: Leave notification submission API
- [ ] Backend: Evaluation/remarks view API
- [ ] Backend: Report card view API
- [ ] Backend: Work sample view API
- [ ] Backend: Fee status view API
- [ ] Frontend: Attendance status page with leave submission
- [ ] Frontend: Evaluation & remarks page
- [ ] Frontend: Report card view page
- [ ] Frontend: Work samples gallery
- [ ] Frontend: Fee status page (monthly breakdown, paid/due)

### Guardian Communication
- [ ] Backend: Guardian message send/receive API
- [ ] Backend: Application submission API
- [ ] Frontend: Message inbox/compose
- [ ] Frontend: Application form
- [ ] Frontend: Notices/announcements view page

---

## Phase 5: Communication & Advanced Features (Week 6-7)

### Messaging System
- [ ] Backend: Complete messaging CRUD with read receipts
- [ ] Backend: Message notification triggers
- [ ] Frontend: Unified message UI with conversation threads

### Announcements & Notices
- [ ] Backend: AnnouncementController (admin school-wide, teacher class-level)
- [ ] Frontend: Announcement create/list/detail pages
- [ ] Frontend: Notice board view for all roles

### Push Notifications
- [ ] Backend: Firebase FCM integration (laravel-notification-channels/fcm)
- [ ] Backend: FCM token registration API
- [ ] Backend: Notification channels for: diary entry, message, announcement, attendance, approval
- [ ] Frontend: FCM token registration on login
- [ ] Frontend: In-app notification bell with dropdown
- [ ] Frontend: Notification preferences page (optional)

### Calendar & Routines
- [ ] Backend: EventController (school events, holidays)
- [ ] Backend: ClassRoutineController (master timetable)
- [ ] Backend: ExamRoutineController (master exam schedule)
- [ ] Frontend: Calendar view (month/week) with events and holidays
- [ ] Frontend: Class routine timetable view
- [ ] Frontend: Exam routine schedule view

### Fee Management
- [ ] Backend: FeeStructureController (per class, categories)
- [ ] Backend: FeeRecordController (mark paid/due per student)
- [ ] Frontend: Fee structure configuration page (admin)
- [ ] Frontend: Fee record management page (admin)
- [ ] Frontend: Fee overview dashboard

### Excel Exports
- [ ] Backend: Attendance monthly export (maatwebsite/excel 3.1)
- [ ] Backend: Student list export
- [ ] Frontend: Export buttons with download handling

### Reports & Analytics (Admin)
- [ ] Backend: Total student count by class API
- [ ] Backend: New admissions vs withdrawals report API
- [ ] Backend: Class-wise attendance percentage API
- [ ] Backend: Student progress summary API
- [ ] Backend: Class performance analysis API
- [ ] Frontend: Reports dashboard with charts (Chart.js or ApexCharts)
- [ ] Frontend: Individual report pages with filters and date ranges

### Activity/Audit Logging
- [ ] Backend: ActivityLog model and observer/event listeners
- [ ] Backend: Log all CRUD operations with user, action, changes
- [ ] Frontend: Activity log viewer page (admin only)

---

## Phase 6: Testing, Polish & Deployment (Week 8-9)

### Backend Testing
- [ ] Unit tests for all models (relationships, scopes)
- [ ] Feature tests for authentication flows
- [ ] Feature tests for Admin API endpoints
- [ ] Feature tests for Teacher API endpoints
- [ ] Feature tests for Guardian API endpoints
- [ ] Feature tests for authorization policies
- [ ] Feature tests for file uploads
- [ ] Feature tests for Excel exports

### Frontend Testing
- [ ] Component tests for base UI components
- [ ] Page tests for critical flows (login, registration, diary, attendance)
- [ ] Store tests for Pinia 4 stores

### UI/UX Polish
- [ ] Responsive design review on mobile/tablet/desktop
- [ ] Loading states and skeleton screens
- [ ] Error handling and user-friendly error messages
- [ ] Empty state designs for all list pages
- [ ] Form validation feedback (inline errors)
- [ ] Toast/snackbar notifications for actions
- [ ] Accessibility review (semantic HTML, ARIA labels, keyboard navigation)

### Security Hardening
- [ ] CSRF protection verification
- [ ] XSS prevention audit
- [ ] SQL injection prevention verification
- [ ] Rate limiting on auth and API endpoints
- [ ] File upload validation (type, size, malware scan)
- [ ] Sensitive data encryption (where appropriate)
- [ ] HTTPS enforcement
- [ ] CORS configuration

### Performance Optimization
- [ ] Database query optimization (eager loading, N+1 fix)
- [ ] Add database indexes on frequently queried columns
- [ ] Redis 8 caching for dashboard stats and frequently accessed data
- [ ] Frontend code splitting and lazy loading routes
- [ ] Image optimization and lazy loading
- [ ] Gzip/Brotli compression setup

### Deployment
- [ ] Provision VPS (Ubuntu 26.04 LTS)
- [ ] Install and configure Nginx 1.30
- [ ] Install and configure PHP 8.5 + PHP-FPM
- [ ] Install and configure MySQL 9.7 LTS
- [ ] Install and configure Redis 8
- [ ] Setup SSL with Let's Encrypt + auto-renewal
- [ ] Configure Laravel environment (`.env` production)
- [ ] Setup Laravel queue workers (Supervisor)
- [ ] Setup Laravel scheduler (cron)
- [ ] Configure log rotation
- [ ] Setup automated database backups
- [ ] Deploy application (e.g., using Deployer or GitHub Actions)
- [ ] Run migrations and seeders on production
- [ ] Smoke test all critical flows on production

### Handover
- [ ] Create admin user guide
- [ ] Create teacher user guide
- [ ] Create guardian user guide
- [ ] Conduct client training session
- [ ] Provide all credentials and access details
- [ ] Final sign-off documentation
