# Kindergarten School Management System (KG-SMS)
## Technical Specification

**Client:** Duha International School
**Tech Stack:** Laravel 13, Vue.js 3.5 (with Vapor Mode), MySQL 9.7 LTS, Redis 8

---

## 1. System Architecture

### High-level Architecture
The application follows a monolithic backend architecture serving a decoupled Vue.js Single Page Application (SPA). 
- **Backend:** Laravel 13 provides a RESTful API and handles all business logic, database interactions, and background queues.
- **Frontend:** Vue.js 3.5 (with Vapor Mode for near-zero runtime overhead and Alien Signals reactivity) built with Vite 8 (uses Rolldown as unified Rust-based bundler, replacing esbuild/Rollup dual architecture) handles the user interface, state management (Pinia 4), and routing (Vue Router 5).
- **Communication:** The frontend communicates with the backend via Laravel Sanctum 4 for SPA authentication, utilizing session cookies and CSRF tokens.

### Request Flow
1. **Browser** initiates a request.
2. **Nginx 1.30** serves static Vue.js 3.5 assets (JS/CSS/HTML) for frontend routes. API requests are proxied to **PHP 8.5-FPM**.
3. **Laravel 13** processes API requests, authenticating via Sanctum 4 session cookies.
4. Controllers interact with **MySQL 9.7 LTS** via Eloquent ORM.
5. Caching and queued jobs (emails, SMS, notifications) are handled by **Redis 8**.

### Folder Structure
**Laravel 13 (Backend):**
```
app/
├── Http/
│   ├── Controllers/ (Grouped by module/role: Admin/, Teacher/, Guardian/)
│   ├── Requests/ (Form requests for validation)
│   ├── Resources/ (API Resources for JSON serialization)
│   └── Middleware/ (Role checks, FirstLogin check)
├── Models/ (Eloquent models with relationships)
├── Policies/ (Authorization policies)
├── Services/ (Business logic orchestration)
├── Notifications/ (FCM, Email, SMS classes)
└── Jobs/ (Queued tasks)
routes/
└── api.php (API endpoint definitions)
```

**Vue.js 3.5 (Frontend):**
```
src/
├── assets/ (Images, Tailwind CSS 4 - uses CSS-first configuration with @theme and @import directives instead of tailwind.config.js)
├── components/ (Reusable UI components)
├── composables/ (Reusable Vue 3.5 logic)
├── layouts/ (AdminLayout, TeacherLayout, GuardianLayout, AuthLayout)
├── pages/ (View components grouped by role)
├── router/ (Route definitions and navigation guards)
├── stores/ (Pinia 4 state modules)
├── services/ (Axios API calls abstraction)
└── utils/ (Helpers, formatters)
```

---

## 2. Database Schema

### Users & Authentication
| Table | Columns (Type, Constraints, Default) | Indexes/Keys |
|-------|--------------------------------------|--------------|
| `users` | id (bigint unsigned, PK), name (varchar), email (varchar, unique), phone (varchar, unique), password (varchar), role (enum: admin/teacher/guardian), email_verified_at (timestamp, null), phone_verified_at (timestamp, null), avatar (varchar, null), status (enum: pending/active/suspended, default: pending), dob (date, null), address (text, null), is_first_login (boolean, default: true), fcm_token (text, null), timestamps, soft_deletes | idx_role, idx_status |
| `profile_change_requests` | id (PK), user_id (FK->users), changes (json), status (enum: pending/approved/rejected, default: pending), reviewed_by (FK->users, null), reviewed_at (timestamp, null), timestamps | fk_user, idx_status |

### Academics & Students
| Table | Columns (Type, Constraints, Default) | Indexes/Keys |
|-------|--------------------------------------|--------------|
| `classes` | id (PK), name (varchar), section (varchar), capacity (int), academic_year (varchar), status (boolean, default: true), timestamps | idx_academic_year |
| `students` | id (PK), guardian_id (FK->users), name (varchar), dob (date), gender (enum: m/f/other), class_id (FK->classes), admission_date (date), admission_no (varchar, unique), blood_group (varchar, null), photo (varchar, null), status (enum: active/inactive/withdrawn, default: active), timestamps, soft_deletes | fk_guardian, fk_class, idx_status |
| `subjects` | id (PK), name (varchar), code (varchar, unique), class_id (FK->classes), timestamps | fk_class |
| `class_teacher` | class_id (FK->classes), teacher_id (FK->users), is_primary (boolean, default: false), timestamps | Composite PK(class_id, teacher_id) |

### Attendance & Diary
| Table | Columns (Type, Constraints, Default) | Indexes/Keys |
|-------|--------------------------------------|--------------|
| `attendance_policy` | id (PK), policy_data (json), created_by (FK->users), timestamps | fk_creator |
| `student_attendances` | id (PK), student_id (FK->students), class_id (FK->classes), date (date), status (enum: present/absent/late/excused), marked_by (FK->users), remarks (varchar, null), timestamps | fk_student, fk_class, idx_date |
| `teacher_attendances` | id (PK), teacher_id (FK->users), date (date), status (enum: present/absent/late/excused), marked_by (FK->users), remarks (varchar, null), timestamps | fk_teacher, idx_date |
| `diary_entries` | id (PK), student_id (FK->students), class_id (FK->classes), date (date), teacher_id (FK->users), activities (text, null), meals (text, null), behavior (text, null), homework (text, null), timestamps | fk_student, fk_class, idx_date |
| `diary_comments` | id (PK), diary_entry_id (FK->diary_entries), user_id (FK->users), comment (text), timestamps | fk_diary, fk_user |
| `leave_notifications` | id (PK), student_id (FK->students), guardian_id (FK->users), date (date), reason (text), status (enum: pending/approved/rejected, default: pending), timestamps | fk_student, fk_guardian |

### Evaluations & Records
| Table | Columns (Type, Constraints, Default) | Indexes/Keys |
|-------|--------------------------------------|--------------|
| `exam_routines` | id (PK), class_id (FK->classes), subject_id (FK->subjects), exam_date (date), start_time (time), end_time (time), created_by (FK->users), timestamps | fk_class, fk_subject |
| `evaluations` | id (PK), student_id (FK->students), subject_id (FK->subjects), exam_routine_id (FK->exam_routines), marks (decimal(5,2), null), grade (varchar, null), remarks (text, null), evaluated_by (FK->users), timestamps | fk_student, fk_subject, fk_exam |
| `report_cards` | id (PK), student_id (FK->students), class_id (FK->classes), academic_year (varchar), term (varchar), data (json), is_published (boolean, default: false), published_at (timestamp, null), published_by (FK->users, null), timestamps | fk_student, fk_class |
| `work_samples` | id (PK), student_id (FK->students), title (varchar), description (text, null), file_path (varchar), uploaded_by (FK->users), timestamps | fk_student, fk_uploader |

### Financial
| Table | Columns (Type, Constraints, Default) | Indexes/Keys |
|-------|--------------------------------------|--------------|
| `fee_structures` | id (PK), class_id (FK->classes), category (varchar), amount (decimal(10,2)), academic_year (varchar), timestamps | fk_class, idx_year |
| `fee_records` | id (PK), student_id (FK->students), fee_structure_id (FK->fee_structures), amount (decimal(10,2)), status (enum: paid/due/partial, default: due), paid_date (date, null), remarks (text, null), timestamps | fk_student, fk_fee_structure |

### Scheduling & Communication
| Table | Columns (Type, Constraints, Default) | Indexes/Keys |
|-------|--------------------------------------|--------------|
| `class_routines` | id (PK), class_id (FK->classes), day_of_week (tinyint), subject_id (FK->subjects), start_time (time), end_time (time), teacher_id (FK->users), timestamps | fk_class, fk_subject, fk_teacher |
| `events` | id (PK), title (varchar), description (text, null), event_date (date), event_type (enum: event/holiday), created_by (FK->users), timestamps | idx_date |
| `announcements` | id (PK), title (varchar), body (text), type (enum: school_wide/class_level), class_id (FK->classes, null), created_by (FK->users), is_published (boolean, default: false), published_at (timestamp, null), timestamps | fk_class, idx_type |
| `messages` | id (PK), sender_id (FK->users), receiver_id (FK->users), subject (varchar, null), body (text), is_read (boolean, default: false), read_at (timestamp, null), timestamps | fk_sender, fk_receiver |
| `applications` | id (PK), user_id (FK->users), type (varchar), subject (varchar), body (text), status (enum: pending/reviewed/resolved, default: pending), reviewed_by (FK->users, null), timestamps | fk_user |
| `notifications` | id (UUID), type (varchar), notifiable_type (varchar), notifiable_id (unsigned bigint), data (text), read_at (timestamp, null), timestamps | Morph index |

### Profiles & Health
| Table | Columns (Type, Constraints, Default) | Indexes/Keys |
|-------|--------------------------------------|--------------|
| `emergency_contacts` | id (PK), student_id (FK->students), name (varchar), relationship (varchar), phone (varchar), is_primary (boolean, default: false), timestamps | fk_student |
| `medical_records` | id (PK), student_id (FK->students), allergies (text, null), conditions (text, null), medications (text, null), blood_group (varchar, null), notes (text, null), updated_by (FK->users), timestamps | fk_student |
| `pickup_persons` | id (PK), student_id (FK->students), name (varchar), relationship (varchar), phone (varchar), photo (varchar, null), is_authorized (boolean, default: true), timestamps | fk_student |

### System & Documents
| Table | Columns (Type, Constraints, Default) | Indexes/Keys |
|-------|--------------------------------------|--------------|
| `documents` | id (PK), documentable_type (varchar), documentable_id (unsigned bigint), title (varchar), file_path (varchar), type (varchar), uploaded_by (FK->users), timestamps | Morph index |
| `activity_logs` | id (PK), user_id (FK->users, null), action (varchar), model_type (varchar, null), model_id (unsigned bigint, null), changes (json, null), ip_address (varchar, null), timestamps | fk_user, idx_action |

---

## 3. API Design

All endpoints are prefixed with `/api/v1/`. Responses return standardized JSON:
```json
{
  "status": "success|error",
  "message": "...",
  "data": { ... }
}
```

### Authentication
- `POST /login`: `email`, `password` -> Token & User Object
- `POST /logout`: (Auth) -> Success
- `GET /me`: (Auth) -> User Profile
- `POST /change-password`: (Auth) `current_password`, `new_password` -> Success
- `POST /register`: `name`, `email`, `password`, `phone` -> Creates pending Guardian

### Admin Group (`/admin`)
Middleware: `auth:sanctum`, `role:admin`
- `GET /users`: List users (filters: role, status)
- `PUT /users/{id}/approve`: Approve pending guardian registrations
- `GET /students`: List students
- `POST /students`: Create student (handles guardian assignment)
- `GET /classes`: List classes
- `POST /classes/{id}/assign-teacher`: `teacher_id`, `is_primary` -> Assigns teacher
- `GET /attendance/summary`: Aggregated attendance stats
- `GET /fees/records`: List fee records

### Teacher Group (`/teacher`)
Middleware: `auth:sanctum`, `role:teacher`
- `GET /classes`: Get classes assigned to this teacher
- `GET /classes/{class_id}/students`: List students in class
- `POST /attendance/bulk`: `class_id`, `date`, `records[{student_id, status}]` -> Mark daily attendance
- `GET /diary/{class_id}/{date}`: Get class diary for date
- `POST /diary`: Create/Update diary entry
- `POST /evaluations/bulk`: Upload marks for an exam routine
- `GET /routines`: Get teacher's weekly schedule

### Guardian Group (`/guardian`)
Middleware: `auth:sanctum`, `role:guardian`
- `GET /students`: List guardian's own children
- `GET /students/{id}/attendance`: Child's attendance history
- `GET /students/{id}/diary`: Child's daily diary
- `POST /diary/{id}/comments`: `comment` -> Add comment to entry
- `GET /students/{id}/fees`: Child's fee status
- `POST /leave`: `student_id`, `date`, `reason` -> Submit leave notification

---

## 4. Authentication & Authorization

### Flow
1. **SPA Authentication:** Vue.js 3.5 uses Axios to hit `/sanctum/csrf-cookie` first, then `/login` to establish a stateful session cookie. No plain text tokens are stored in `localStorage`.
2. **First Login Enforcement:** Middleware `EnsurePasswordChanged` checks `is_first_login`. If true, redirects all requests to a forced password change screen except the `/change-password` route.
3. **Registration:** Guardians self-register. Account is created with `status = pending`. They cannot login until an Admin approves the account.

### Authorization (Policies/Gates)
- **Admin:** Can access all data.
- **Teacher:** Can only read/write data for classes they are assigned to (via `class_teacher`), and read profiles of students in those classes.
- **Guardian:** Can only read data for students where `students.guardian_id === Auth::id()`.

---

## 5. Frontend Architecture

### State Management (Pinia 4)
- `useAuthStore`: Manages user session, role, permissions.
- `useAppStore`: Global loaders, toast notifications, UI state.
- Module stores (`useStudentStore`, `useClassStore`): Cache API data, handle pagination.

### Route Guards
```javascript
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login');
  } else if (to.meta.role && authStore.user.role !== to.meta.role) {
    next('/unauthorized'); // Role mismatch
  } else if (authStore.user?.is_first_login && to.name !== 'ChangePassword') {
    next('/force-change-password');
  } else {
    next();
  }
});
```

---

## 6. Key Technical Decisions

- **Monolith vs Microservices:** Chosen Monolith (Laravel 13) for speed of development, simpler deployment, and because the domain context is highly cohesive (one school).
- **SPA vs SSR:** Vue.js 3.5 SPA chosen to provide a native-like fluid experience on mobile devices (crucial for Guardians and Teachers). SEO is not a requirement for an internal dashboard.
- **File Storage:** Local disk configured for early stages, abstracting via Laravel 13 Storage facade to easily swap to AWS S3 in production.
- **Push Notifications:** Firebase Cloud Messaging (FCM) via Laravel 13 Notification Channels. Frontend registers service worker for FCM token and sends it to backend.
- **Audit Logging:** Implemented via Eloquent Model Observers (`created`, `updated`, `deleted` events) saving to `activity_logs`.

---

## 7. Security Considerations

- **CSRF:** Handled automatically by Laravel Sanctum 4 for first-party SPA.
- **XSS:** Vue.js 3.5 automatically escapes output. User-generated content (e.g., diary comments) is sanitized.
- **SQLi:** Laravel 13 Eloquent ORM utilizes PDO parameter binding.
- **Rate Limiting:** `ThrottleRequests` middleware applied to login attempts (5 per minute) and generic API endpoints (60 per minute).
- **IDOR Prevention:** Laravel 13 Policies ensure users can only access their authorized relational IDs.

---

## 8. Deployment Architecture

- **Server:** Ubuntu 26.04 LTS (Resolute Raccoon, includes Linux 7.0 kernel) VPS (e.g., DigitalOcean, AWS EC2).
- **Web Server:** Nginx 1.30 (Serves Vue.js 3.5 static `dist` on `/` and proxies `/api` to PHP 8.5-FPM).
- **Database:** MySQL 9.7 LTS (note: MySQL 8.0 is EOL as of April 2026) with automated daily backups (cron job + mysqldump to remote storage).
- **Queue/Cache:** Redis 8 (the current major version) for fast session management, queueing emails, SMS, and FCM notifications.
- **Process Manager:** Supervisor to keep `php artisan queue:work` running.
- **SSL:** Certbot (Let's Encrypt) with automated renewal via cron.

---

## 9. Performance Considerations

- **Database Indexing:** Foreign keys, enums (status, role), and date columns are indexed to speed up filtering and reporting.
- **Eager Loading:** Strict use of Eloquent's `with()` to prevent N+1 query problems (e.g., loading students with their attendances).
- **Queue Workers:** SMS gateway calls, email sending, and heavy Excel exports are dispatched to Redis queues so the HTTP response is instantaneous.
- **Frontend Optimization:** Vue router uses dynamic imports (`() => import('./Page.vue')`) for code-splitting. Images are lazy-loaded.
