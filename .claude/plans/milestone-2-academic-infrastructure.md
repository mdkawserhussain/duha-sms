# Plan: Milestone 2 — Academic Infrastructure & Class Scheduling

## Context

The codebase already has Classes CRUD, Class Routines, Exam Routines, and Subjects (model+migration only). However, there are critical frontend-backend mismatches (field names don't match), missing database columns (room, exam_name), no Subject CRUD controller, no Room management, and no academic year/term management. This plan fixes all gaps and builds out the full academic infrastructure.

---

## Phase 1: Fix Existing Schema & Frontend-Backend Mismatches

**Goal**: Make existing code work correctly before adding new features.

### 1.1 Add missing columns to existing tables

**Files to modify:**
- `database/migrations/2024_01_01_000010_create_exam_routines_table.php` — add `exam_name` (string, nullable) and `room` (string, nullable)
- `database/migrations/2024_01_01_000016_create_class_routines_table.php` — add `room` (string, nullable)

**New migration:** `database/migrations/2024_07_26_000001_add_room_and_exam_name_columns.php`
- Add `room` column to `class_routines`
- Add `exam_name` and `room` columns to `exam_routines`

### 1.2 Update models to include new fields

**Files to modify:**
- `app/Models/ClassRoutine.php` — add `room` to `$fillable`
- `app/Models/ExamRoutine.php` — add `exam_name`, `room` to `$fillable`

### 1.3 Fix frontend field name mismatches

**Files to modify:**
- `resources/js/pages/admin/Routines.vue`
  - Change `form.subject` (free text) → `form.subject_id` (select dropdown from subjects API)
  - Fix teacher dropdown: `t.first_name`/`t.last_name` → `t.name` (User model has single `name` field)
  - Add `room` field to form and table display
  - Load subjects list from API on mount
- `resources/js/pages/admin/ExamRoutines.vue`
  - Change `form.subject` → `form.subject_id` (select dropdown)
  - Change `form.date` → `form.exam_date` (backend field name)
  - Add `exam_name` and `room` fields to form and table
  - Load subjects list from API on mount
- `resources/js/pages/teacher/Routine.vue`
  - Fix teacher name rendering (`t.name` not `first_name`/`last_name`)
  - Verify Room column displays correctly

### 1.4 Update controllers to accept new fields

**Files to modify:**
- `app/Http/Controllers/Admin/RoutineController.php` — add `room` to validation in `store()` and `update()`
- `app/Http/Controllers/Admin/ExamRoutineController.php` — add `exam_name`, `room` to validation; add `room` to validation in `store()` and `update()`

**Verify**: Run `php artisan migrate` and existing E2E tests still pass.

---

## Phase 2: Subject Management (Admin CRUD)

**Goal**: Admin can create, edit, delete, list subjects per class.

### 2.1 Create SubjectController

**New file:** `app/Http/Controllers/Admin/SubjectController.php`
- `index()` — List subjects with class relationship, filterable by `class_id`, paginated
- `store()` — Create subject (validate: name, code unique, class_id)
- `show()` — Single subject with class
- `update()` — Update subject
- `destroy()` — Delete subject (check no evaluations reference it)

### 2.2 Add API routes

**File to modify:** `routes/api.php`
```
apiResource('subjects', Admin\SubjectController::class)
```
Under the admin middleware group.

### 2.3 Create Subjects admin page

**New file:** `resources/js/pages/admin/Subjects.vue`
- Table: Name, Code, Class, Actions (Edit, Delete)
- Add/Edit modal with fields: Name, Code, Class (dropdown)
- Search by name/code
- Filter by class
- Pagination

### 2.4 Add Subjects to router and sidebar

**Files to modify:**
- `resources/js/router.js` — add `/admin/subjects` route
- `resources/js/layouts/AppLayout.vue` — add "Subjects" link under Academics section in admin sidebar

---

## Phase 3: Room Management (Admin CRUD)

**Goal**: Admin can manage physical rooms/buildings for scheduling.

### 3.1 Create rooms migration and model

**New migration:** `database/migrations/2024_07_26_000002_create_rooms_table.php`
- `id`, `name` (string), `building` (string, nullable), `floor` (integer, nullable), `capacity` (integer), `status` (enum: active/maintenance), timestamps

**New file:** `app/Models/Room.php`
- `$fillable`: name, building, floor, capacity, status
- `hasMany(ClassRoutine)`, `hasMany(ExamRoutine)`

### 3.2 Create RoomController

**New file:** `app/Http/Controllers/Admin/RoomController.php`
- `index()` — List rooms, filterable by building/status, paginated
- `store()` — Create room (validate: name, capacity)
- `show()` — Single room with routines
- `update()` — Update room
- `destroy()` — Delete room (check no active routines reference it)

### 3.3 Add API routes

**File to modify:** `routes/api.php`
```
apiResource('rooms', Admin\RoomController::class)
```

### 3.4 Update routine controllers to use room_id

**Files to modify:**
- `app/Http/Controllers/Admin/RoutineController.php` — add `room_id` to validation, eager-load room
- `app/Http/Controllers/Admin/ExamRoutineController.php` — add `room_id` to validation, eager-load room
- `app/Models/ClassRoutine.php` — add `belongsTo(Room)` relationship
- `app/Models/ExamRoutine.php` — add `belongsTo(Room)` relationship

### 3.5 Update routine frontends to use room dropdown

**Files to modify:**
- `resources/js/pages/admin/Routines.vue` — replace text `room` field with room dropdown (load rooms from API)
- `resources/js/pages/admin/ExamRoutines.vue` — replace text `room` field with room dropdown

### 3.6 Create Rooms admin page

**New file:** `resources/js/pages/admin/Rooms.vue`
- Table: Name, Building, Floor, Capacity, Status, Actions
- Add/Edit modal
- Filter by building, status

### 3.7 Add Rooms to router and sidebar

**Files to modify:**
- `resources/js/router.js` — add `/admin/rooms` route
- `resources/js/layouts/AppLayout.vue` — add "Rooms" link under Academics section

---

## Phase 4: Academic Year & Term Management

**Goal**: Admin can define academic years and terms that other modules reference.

### 4.1 Create academic_years and terms migrations

**New migration:** `database/migrations/2024_07_26_000003_create_academic_years_table.php`
- `id`, `name` (string, e.g. "2026"), `start_date`, `end_date`, `is_current` (boolean), timestamps

**New migration:** `database/migrations/2024_07_26_000004_create_terms_table.php`
- `id`, `academic_year_id` (FK), `name` (string, e.g. "Term 1"), `start_date`, `end_date`, `is_current` (boolean), timestamps

### 4.2 Create models

**New file:** `app/Models/AcademicYear.php`
- `$fillable`: name, start_date, end_date, is_current
- `hasMany(Term)`, `hasMany(ClassModel)`

**New file:** `app/Models/Term.php`
- `$fillable`: academic_year_id, name, start_date, end_date, is_current
- `belongsTo(AcademicYear)`, `hasMany(ReportCard)`

### 4.3 Create controllers

**New file:** `app/Http/Controllers/Admin/AcademicYearController.php`
- Full CRUD, `setCurrent()` action to mark one year as current

**New file:** `app/Http/Controllers/Admin/TermController.php`
- Full CRUD scoped to academic year, `setCurrent()` action

### 4.4 Add API routes

```
apiResource('academic-years', Admin\AcademicYearController::class)
apiResource('terms', Admin\TermController::class)
POST academic-years/{id}/set-current
POST terms/{id}/set-current
```

### 4.5 Create admin pages

**New files:**
- `resources/js/pages/admin/AcademicYears.vue`
- `resources/js/pages/admin/Terms.vue`

### 4.6 Add to router and sidebar

---

## Phase 5: Student Transfer & Capacity Guard

**Goal**: Admin can transfer students between classes; system enforces capacity limits.

### 5.1 Add transfer endpoint

**File to modify:** `app/Http/Controllers/Admin/StudentController.php`
- `transfer(Request $request, Student $student)` — validate `class_id`, check target class capacity, update student's `class_id`, log the transfer

### 5.2 Add transfer route

**File to modify:** `routes/api.php`
```
POST /api/admin/students/{student}/transfer
```

### 5.3 Add transfer UI

**File to modify:** `resources/js/pages/admin/Students.vue`
- Add "Transfer" button per row
- Transfer modal: select target class, confirm
- Show current class and target class info

### 5.4 Add capacity guard to student creation

**File to modify:** `app/Http/Controllers/Admin/StudentController.php`
- In `store()`: after validation, check `ClassModel::find(class_id)->students()->active()->count() < capacity`
- Return 422 with error if capacity exceeded

---

## Phase 6: Teacher Exam Routine View

**Goal**: Teachers can view and create exam routines for their assigned classes.

### 6.1 Create Teacher/ExamRoutineController

**New file:** `app/Http/Controllers/Teacher/ExamRoutineController.php`
- `index()` — List exam routines for teacher's assigned classes
- `store()` — Create exam routine (validate teacher owns the class+subject)
- `update()` — Update own exam routine
- `destroy()` — Delete own exam routine

### 6.2 Add teacher exam routine routes

```
apiResource('exam-routines', Teacher\ExamRoutineController::class)
```

### 6.3 Create teacher exam routine page

**New file:** `resources/js/pages/teacher/ExamRoutines.vue`
- Table view of own exam routines
- Add/Edit modal (class auto-selected from assigned classes)

### 6.4 Add to teacher router and sidebar

---

## Phase 7: Demo Data Seeding

**Goal**: Database has realistic academic data for testing.

### 7.1 Update DemoDataSeeder

**File to modify:** `database/seeders/DemoDataSeeder.php`
- Seed 2 academic years (2025, 2026) with current flag
- Seed 4 terms (2 per year)
- Seed 8 rooms across 2 buildings
- Seed 15+ subjects across 4 classes
- Seed 20+ class routines (covering all weekdays)
- Seed 5+ exam routines
- Link subjects to teachers via class_routines

---

## Verification

After each phase:
1. `php artisan migrate` succeeds
2. Existing E2E tests still pass (`node test-e2e-student-management.cjs`)
3. New manual verification via browser at localhost:8000

After all phases:
- Full E2E test suite passes (111+ assertions)
- All new CRUD pages load and function
- Routine/exam forms use dropdowns, not free text
- Room data appears in routine tables
- Academic years and terms are manageable
- Student transfer works end-to-end
- Teacher can manage own exam routines
- Demo data populates all new features

---

## Dependencies

- Phase 1 must complete first (fixes existing code)
- Phases 2-4 can proceed in parallel after Phase 1
- Phase 5 depends on Phase 1 (capacity guard uses existing class model)
- Phase 6 depends on Phase 2 (teacher needs subjects to exist)
- Phase 7 should be last (seeds all new tables)
