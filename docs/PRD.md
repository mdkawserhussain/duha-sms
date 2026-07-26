# Product Requirements Document (PRD)
## Kindergarten School Management System (KG-SMS)

### 1. Product Overview

#### 1.1 Product Vision
To provide Duha International School with a modern, centralized, and user-friendly School Management System that seamlessly connects administrators, teachers, and guardians. The system aims to eliminate manual paperwork, enhance transparency, and foster a collaborative educational environment.

#### 1.2 Problem Statement
Currently, Duha International School relies on manual diary logs, paper-based attendance, and fragmented communication channels. This leads to inefficiencies, data loss, delayed updates for parents, and administrative overhead. 

#### 1.3 Target Users
- **Admin (School Administrator):** Oversees the entire operation, manages users, configures system settings, and reviews school-wide reports.
- **Teacher:** Manages day-to-day classroom activities, attendance, grading, daily diaries, and direct communication with guardians.
- **Guardian (Parent):** Monitors their child's academic progress, daily activities, attendance, and communicates with teachers and the school.

#### 1.4 Success Metrics / KPIs
- **Adoption Rate:** 100% of teachers and 80%+ of guardians actively using the system within the first month.
- **Efficiency:** Reduction in time spent on administrative tasks (e.g., attendance tracking, diary writing) by at least 50%.
- **Engagement:** High frequency of daily diary views and messages between guardians and teachers.
- **System Uptime:** 99.9% availability during school hours.

---

### 2. User Personas

| Persona | Role | Goals & Motivations | Pain Points |
| :--- | :--- | :--- | :--- |
| **Principal / Admin** | School Administrator | Centralize operations, monitor school health, ensure smooth communication, manage fees and schedules efficiently. | Fragmented data, difficult to track teacher performance and student attendance school-wide, time-consuming manual reports. |
| **Teacher** | Class Instructor | Focus on teaching rather than paperwork, easily communicate with parents, track student progress systematically. | Writing physical diaries for every student takes too much time, tracking paper attendance is tedious, parent communication is scattered. |
| **Guardian** | Parent / Caregiver | Stay informed about their child's daily activities, academic progress, and health; easily communicate with the school. | Lack of real-time updates, missing physical notices, disconnected from the child's daily school life. |

---

### 3. User Stories & Functional Requirements

#### 3.1 Admin Module

**Login & Setup**
- **User Story:** As an Admin, I want auto-seeded credentials so that I can access the system immediately upon deployment.
- **User Story:** As an Admin, I want to update my password after the first login and change it anytime so that my account remains secure.

**User Management**
- **User Story:** As an Admin, I want to CRUD guardian accounts and the students under them so that the user directory is accurate.
- **User Story:** As an Admin, I want to view a guardian list with search and filter capabilities so that I can easily find specific parent records.
- **User Story:** As an Admin, I want to verify guardian self-registrations via an approval queue so that only authorized parents gain access.
- **User Story:** As an Admin, I want to approve profile changes made by guardians so that data integrity is maintained.

**Teacher Management**
- **User Story:** As an Admin, I want to CRUD teacher profiles and assign them to classes so that the teaching staff is organized.
- **User Story:** As an Admin, I want to view, input, and remove teacher attendance so that I can track staff availability and payroll prerequisites.

**Diary, Attendance & Class Management**
- **User Story:** As an Admin, I want to view student-wise daily diaries with search/filter so that I can oversee classroom activities.
- **User Story:** As an Admin, I want to view class-wise attendance reports and monthly summaries, and export them to Excel so that I can analyze attendance trends.
- **User Story:** As an Admin, I want to be the sole owner of the school-wide attendance policy so that rules are uniformly applied.
- **User Story:** As an Admin, I want to CRUD classes, assign teachers, set/monitor capacity, and transfer students so that class logistics are well-managed.

**Calendar, Routine & Fees**
- **User Story:** As an Admin, I want to define fee structures per class and fee categories so that billing is standardized.
- **User Story:** As an Admin, I want to mark fee status (paid/due) so that financial tracking is accurate.
- **User Story:** As an Admin, I want to manage master class and exam routines, school events, holidays, and announcements so that everyone stays informed.

**Documents, Settings & Reports**
- **User Story:** As an Admin, I want to manage school policies, email/SMS settings, and API configurations so that the system is properly tailored to the school's needs.
- **User Story:** As an Admin, I want to process guardian and teacher applications (e.g., leave requests) so that administrative workflows are digitized.
- **User Story:** As an Admin, I want dashboards showing total student count, admissions vs. withdrawals, attendance percentages, and class performance so that I can make data-driven decisions.

**Health & Safety**
- **User Story:** As an Admin, I want to view student health info, emergency contacts, and authorized pickup persons, with the ability to edit corrections only, so that student safety is prioritized without overriding parent data ownership.

#### 3.2 Teacher Module

**Login & Setup**
- **User Story:** As a Teacher, I want to log in using my Teacher ID and DOB as an initial password, with a mandatory prompt to update it, so that my account is secure.

**Daily Routine & Scheduling**
- **User Story:** As a Teacher, I want to view schedules, routines, exam routines, notices, and events so that I am prepared for my classes.

**Diary Management**
- **User Story:** As a Teacher, I want to view and search student-wise daily diaries so that I can track individual progress.
- **User Story:** As a Teacher, I want to record activities, meals, and behavior, and edit diary logs so that parents get accurate daily updates.
- **User Story:** As a Teacher, I want to bulk-add homework for the entire class so that I save time.
- **User Story:** As a Teacher, I want to access past diary history so that I can review long-term behavioral and academic trends.

**Attendance**
- **User Story:** As a Teacher, I want to mark daily Present/Absent, view historical attendance, and generate/download Excel summaries so that I can easily track my class's attendance.
- **User Story:** As a Teacher, I want to view the attendance policy and guardian leave notifications so that I can make informed attendance decisions.

**Academic & Student Management**
- **User Story:** As a Teacher, I want to input evaluation marks, upload work samples, and publish report cards (with controlled visibility) so that academic progress is recorded.
- **User Story:** As a Teacher, I want to manage student profiles, review health info, emergency contacts, and pickup persons so that I can ensure student safety and well-being.

**Communication, Exam & Applications**
- **User Story:** As a Teacher, I want to direct message guardians and send class-wide group notices so that communication is seamless.
- **User Story:** As a Teacher, I want to add/update exam routines for my specific class/subject so that students are well-prepared.
- **User Story:** As a Teacher, I want to submit applications (e.g., leave) to the Admin and receive push notifications so that administrative processes are efficient.

#### 3.3 Guardian Module

**Login & Setup**
- **User Story:** As a Guardian, I want to self-register using my phone number, set a password on first login, and reset it via email/phone so that I can easily access the system.

**Profile Management**
- **User Story:** As a Guardian, I want to view and update my profile (pending Admin approval) and see my verification state so that my information is up to date.
- **User Story:** As a Guardian, I want to manage emergency contacts, medical info, authorized pickup persons, and submit documents so that the school has critical safety information.

**Student Diary**
- **User Story:** As a Guardian, I want to view daily diaries, browse past history, and comment on entries so that I can stay engaged with my child's daily life.
- **User Story:** As a Guardian, I want to receive push notifications for new diary entries so that I don't miss important updates.

**Academic, Attendance & Fees**
- **User Story:** As a Guardian, I want to monitor attendance and submit absence/leave notifications so that the school is informed.
- **User Story:** As a Guardian, I want to view evaluations, remarks, work samples, report cards, and fee status so that I am aware of my child's academic and financial standing.

**Communication, Schedule & Notices**
- **User Story:** As a Guardian, I want to direct message the school, submit applications, and receive push notifications for notices and schedules so that I am always connected.

---

### 4. Data Ownership Model

Data governance and ownership ensure that the right individuals control specific data points while maintaining data integrity across the system.

| Data Entity | Owner | Viewer(s) | Editor(s) | Notes |
| :--- | :--- | :--- | :--- | :--- |
| **Emergency Contacts & Medical Info** | Guardian | Teacher, Admin | Guardian (Full), Admin (Corrections only) | Guardians own this data to ensure accuracy. |
| **Attendance Policy** | Admin | Teacher | Admin | Uniformly applied across the school. |
| **Master Exam Routine** | Admin | Teacher, Guardian | Admin (Master), Teacher (Own Class) | Teachers can append class-specific details. |
| **School-wide Notices** | Admin | Teacher, Guardian | Admin | Global announcements. |
| **Class-level Notices** | Teacher | Guardian | Teacher | Specific to the teacher's classroom. |
| **Guardian Profile** | Guardian | Admin, Teacher | Guardian (Needs Admin Approval) | Admin verifies self-registration and profile changes. |

---

### 5. Non-Functional Requirements

- **Performance:** Page load times must be under 2 seconds. API responses should be under 200ms.
- **Scalability:** The system must support at least 500 concurrent users without performance degradation, utilizing Laravel 13's caching and database indexing.
- **Availability:** Ensure 99.9% uptime. VPS hosting with Nginx 1.30 will be configured to handle traffic reliably.
- **Security:** 
  - Role-based access control (RBAC).
  - All data in transit must be encrypted via SSL (Let's Encrypt).
  - Secure authentication using Laravel Sanctum 4 API tokens.
  - Protection against SQL injection, XSS, and CSRF out-of-the-box via Laravel 13.
- **Mobile Responsiveness:** A web-first approach using Tailwind CSS 4 (CSS-first configuration, Oxide engine) to ensure the UI is fully functional and optimized for mobile browsers, paving the way for a future native app wrapper.
- **Data Backup:** Automated daily backups of the MySQL 9.7 LTS database and file storage to a remote/secure location (e.g., S3 or secondary block storage).
- **Audit Trail:** Maintain logs of critical actions (e.g., grade changes, profile updates, fee status changes) for accountability.

---

### 6. Out of Scope (Future Add-ons)

The following items are explicitly excluded from the current 45-day development phase but may be considered for future phases:
- **Native Mobile Apps:** iOS and Android native applications (current phase is a responsive web app).
- **Biometric/RFID Integration:** Hardware integrations for automated attendance tracking.
- **Online Payment Gateway:** Integration with bKash, SSLCommerz, or Stripe for online fee payment (current system only tracks paid/due status).
- **Third-Party SMS Gateway Setup:** The system will have the logic, but the client must provide and manage the third-party SMS API and associated costs.

---

### 7. Assumptions & Constraints

- **Budget:** Fixed at BDT 1,50,000 (includes 1 year of VPS hosting).
- **Timeline:** 45 working days strictly commencing *after* UI/UX approval.
- **Stack Constraint:** Strict adherence to Laravel 13 and Vue.js 3.5 (with Vapor Mode), as opposed to React/Node.js 24 LTS.
- **Team Size:** Development team constraints (Oikko AI) require strict adherence to the defined scope to meet the deadline.
- **Language:** The system will primarily support English, but UI components and text inputs must gracefully handle Bengali character sets where required by users.
- **Hosting environment:** The client agrees to the specified VPS + Nginx configuration.

---

### 8. Acceptance Criteria

- **Functional:** All features listed in Section 3 must function as described, with data properly flowing between Admin, Teacher, and Guardian interfaces.
- **Security:** Authentication, authorization, and RBAC must prevent unauthorized access to sensitive data or actions.
- **UX/UI:** The application must exactly match the approved UI/UX designs and work flawlessly on desktop, tablet, and mobile device screens.
- **Deployment:** The system must be successfully deployed to the production VPS environment and accessible via a secure HTTPS domain.
- **Documentation:** A basic user manual or handover document outlining system administration must be provided upon delivery.
