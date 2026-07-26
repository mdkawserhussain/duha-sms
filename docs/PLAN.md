# KG-SMS Implementation Plan

## 1. Project Overview

The Kindergarten School Management System (KG-SMS) is a tailored web application designed for Duha International School. It digitizes and streamlines school operations, facilitating seamless interaction between Administrators, Teachers, and Guardians. 

- **Tech Stack:** Laravel 13 (Backend), Vue.js 3.5 (with Vapor Mode) via Vite 8 (Rolldown bundler) (Frontend), MySQL 9.7 LTS (Database), Redis 8 (Caching/Queueing), Tailwind CSS 4 (CSS-first configuration, Oxide engine) (Styling).
- **Team Size:** 3 Developers (CTO/Frontend, CIO/Full-Stack, COO/Backend).
- **Timeline:** 45 working days (9 weeks).

---

## 2. Development Phases

The project is broken down into 6 phases, spanning 45 working days (9 weeks).

### Phase 1: Foundation & Setup (Week 1-2, Days 1-10)
- **Project scaffolding:** Laravel 13 + Vue 3.5 (with Vapor Mode) via Vite 8 (Rolldown bundler).
- **Database design and migrations:** Creating all tables with appropriate relationships.
- **Authentication system:** Laravel Sanctum 4 setup, login/logout, and role-based middleware (Admin, Teacher, Guardian).
- **Admin seeding:** Auto-seeded initial super-admin account.
- **First-login password change flow:** Mandating password updates on the first login for added security.
- **Base UI layout (Vue):** Sidebar, header, and role-based navigation guards.
- **Tailwind CSS 4 design system setup:** Configuring themes using CSS-first configuration with @theme and @import directives instead of tailwind.config.js, brand colors, and global styles.
- **Development environment setup:** Docker/Valet, MySQL 9.7 LTS, and Redis 8 integration.

### Phase 2: Admin Module (Week 3-4, Days 11-20)
- **Admin dashboard:** Overview with key metrics and analytics.
- **User Management:** Guardian CRUD, student CRUD, search/filter functionality, verification queue, and approval queue.
- **Teacher Management:** Teacher CRUD, class assignments, and teacher attendance tracking.
- **Class Management:** Class CRUD, capacity tracking, and student transfer processes.
- **Settings:** Email configuration and SMS gateway setup.
- **School documents management:** Central repository for guidelines and institutional files.

### Phase 3: Teacher Module (Week 4-5, Days 16-25)
*Note: Overlaps with Phase 2 for frontend/backend parallelization.*
- **Teacher dashboard:** Overview of assigned classes and daily schedule.
- **Diary Management:** CRUD entries, bulk homework assignments, history view, and search/filter.
- **Attendance:** Mark Present/Absent, view history, policy view, and leave notification display.
- **Student Management:** View profiles, health information, emergency contacts, and authorized pickup persons.
- **Evaluation:** Input marks and generate report cards.
- **Work sample uploads:** Uploading media/files representing student work.
- **Teacher schedule/routine view:** Class timetables and daily routine.

### Phase 4: Guardian Module (Week 5-6, Days 21-30)
- **Guardian self-registration flow:** Phone number input → verification queue → admin approval.
- **Guardian dashboard:** Overview of linked students and recent updates.
- **Profile management:** View and request updates to profile (with admin approval) and upload necessary documents.
- **Student diary view:** View entries, leave comments, check history, and receive push notifications.
- **Attendance view & leave notification submission:** Checking child's attendance and applying for leaves.
- **Academic view:** Viewing evaluations, report cards, and work samples.
- **Fee status view:** Checking pending and paid fee statuses.

### Phase 5: Communication & Advanced Features (Week 6-7, Days 26-35)
- **Messaging system:** Direct messaging between teachers and guardians.
- **Announcements:** School-wide broadcasts (by admin) and class-level notices (by teacher).
- **Push notifications:** Firebase Cloud Messaging (FCM) integration for real-time alerts.
- **Calendar:** School events and holidays tracking.
- **Class routines & Exam routines:** Scheduled timetables for academic events.
- **Fee management:** Fee structure, categories, and payment status updates.
- **Excel export:** Monthly summaries for student and teacher attendance.
- **Reports:** Dashboards for student counts, admissions/withdrawals, attendance percentages, progress tracking, and class performance.
- **Activity/audit logging:** Tracking critical user actions for security and accountability.

### Phase 6: Testing, Polish & Deployment (Week 8-9, Days 36-45)
- **End-to-end testing:** Comprehensive testing across Admin, Teacher, and Guardian roles.
- **Bug fixes and UI polish:** Refining mobile responsiveness and UX flows.
- **Performance optimization:** Eager loading database queries, caching frequent data, and adding database indexes.
- **Security audit:** CSRF, XSS protection, rate limiting, and file upload validation.
- **VPS setup:** Ubuntu 26.04 LTS (Resolute Raccoon) server configuration with Nginx 1.30, PHP-FPM, MySQL 9.7 LTS, and Redis 8.
- **SSL setup:** Let's Encrypt automated certificate configuration.
- **Production deployment:** CI/CD or manual release of the live system.
- **Data seeding:** Preparing demo/training data for client onboarding.
- **Client handover:** Training session for school staff.
- **Documentation:** Providing user manuals and technical documentation.

---

## 3. Milestones & Deliverables

| Milestone | Week | Deliverable | Payment |
|---|---|---|---|
| Project Kickoff | Week 0 | Signed agreement | BDT 75,000 (50%) |
| Foundation Complete | Week 2 | Auth + base UI + DB | — |
| Admin Module Complete | Week 4 | Full admin panel | — |
| Teacher Module Complete | Week 5 | Teacher features | — |
| Guardian Module Complete | Week 6 | Guardian features | — |
| Staging Delivery | Week 7 | All features on staging | BDT 45,000 (30%) |
| Production Deployment | Week 9 | Live system | BDT 30,000 (20%) |

---

## 4. Risk Assessment

- **Scope Creep Risk:** High. *Mitigation:* Strictly adhere to the agreed deliverables in this document. Any additional feature requests will be documented and evaluated for a separate Phase 2 contract.
- **Timeline Risk (3-Person Team):** Medium. *Mitigation:* Parallelize frontend and backend tasks (e.g., CTO on Vue, COO on Laravel API). Regular daily stand-ups to unblock issues.
- **Third-Party Dependency Risks:** Medium. (Firebase limits, SMS gateway reliability). *Mitigation:* Implement robust error handling, fallbacks, and queueing for external API calls.
- **Client Feedback Turnaround Time:** Medium. *Mitigation:* Schedule weekly review sessions on staging to gather feedback incrementally rather than at the very end.

---

## 5. Development Conventions

- **Git Branching Strategy:** 
  - `main` (production-ready code)
  - `staging` (pre-production testing)
  - `dev` (integration branch)
  - `feature/issue-number-name` (individual features)
- **Code Review Process:** Pull requests to `dev` must be reviewed by at least one other team member.
- **Naming Conventions:**
  - *Laravel:* PascalCase for Models/Controllers, snake_case for migrations and database columns.
  - *Vue:* PascalCase for Components (`StudentProfile.vue`), camelCase for composables and stores (`useAuthStore`).
- **API Versioning:** Prefix all endpoints with `/api/v1/`.
- **Testing Approach:** 
  - Backend: PHPUnit for unit and feature tests.
  - Frontend: Vitest for component testing.

---

## 6. Dependencies & Prerequisites

### Development Tools
- PHP 8.5
- Node.js 24 LTS
- Composer
- Git

### Third-Party Accounts
- Firebase Account (for FCM Push Notifications)
- SMS Provider API Keys (local Bangladeshi provider preferred)
- DigitalOcean / AWS Account (VPS hosting)
- Domain Registrar access (for DNS/SSL setup)

### Client Deliverables Needed
- School logo (high-res vector/PNG)
- Brand color palette preferences
- Initial static content (About, Guidelines, Terms if any)
- Initial Admin details for the root account
