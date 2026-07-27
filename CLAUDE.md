# CLAUDE.md — Duha SMS Project Memory

## Project Identity
**Duha SMS** — Full-featured school management ERP for Duha International School.
Client: Duha International School (Bangladesh). Built by Oikko AI.

## Tech Stack
- **Backend**: Laravel 13.8+, PHP ^8.3
- **Frontend**: Vue.js 3.5 SPA (Vapor Mode), Pinia 2.3, Vue Router 4.6
- **Styling**: Tailwind CSS 4.0
- **Build**: Vite 8.0 (Rolldown)
- **Auth**: Laravel Sanctum 4.3 (token-based)
- **HTTP**: Axios 1.18
- **Excel**: Maatwebsite/Excel 3.1
- **Testing**: PHPUnit 12.5, Playwright 1.61
- **Database**: SQLite (dev), MySQL 9.7 LTS (prod)

## Architecture Quick Reference
- **3 roles**: Admin (27 controllers), Teacher (9 controllers), Guardian (10 controllers)
- **32 Eloquent models**, **39 migrations**, **10 policies**
- **RESTful API** with Sanctum auth + role middleware (`role:admin`, `role:teacher`, `role:guardian`)
- **SPA**: Single catch-all route in `web.php` serves Vue app

## Key File Locations
```
app/Http/Controllers/Admin/     — 27 admin controllers
app/Http/Controllers/Teacher/   — 9 teacher controllers
app/Http/Controllers/Guardian/  — 10 guardian controllers
app/Http/Controllers/Auth/      — Login, Register, Password
app/Models/                     — 32 Eloquent models
app/Policies/                   — 10 authorization policies
resources/js/pages/admin/       — 23 Vue admin pages
resources/js/pages/teacher/     — 10 Vue teacher pages
resources/js/pages/guardian/    — 11 Vue guardian pages
resources/js/stores/            — Pinia auth store
resources/js/services/          — Axios API wrapper
resources/js/layouts/           — AppLayout.vue (shared)
resources/js/router.js          — Vue Router config
routes/api.php                  — API routes
routes/web.php                  — SPA catch-all
database/seeders/               — DemoDataSeeder, AdminSeeder
.claude/prds/                   — Product requirements
.claude/plans/                  — Implementation plans
TASKS.md                        — Master task list (519 lines)
```

## Current Status (2026-07-27)
- **Milestone 1** (Core CRUD + Auth): **COMPLETE**
- **Milestones 2-12**: PENDING
- **Current phase**: Phase 1 — Fix & Foundation (Week 1-2)
- **Active work**: Module 2 mismatches, Subject CRUD, Room management, Academic Year/Term

## Pending Work (Phase 1 Priority)
1. Fix Module 2 mismatches (schema, model fillables, Vue form fields)
2. Subject CRUD (controller + route + Vue page)
3. Room management (migration + model + controller + Vue page)
4. Academic Year & Term management
5. Wire RBAC policies into AuthServiceProvider
6. Demo data seeding

## Conventions
- Controllers use `index`, `store`, `show`, `update`, `destroy` pattern
- Vue pages use `<script setup>` with Composition API
- All API responses return JSON via Axios
- Tailwind CSS for all styling (no Bootstrap)
- Models use `$fillable`, `$casts`, relationship methods
- Routes use `apiResource` for RESTful endpoints
- Role-based access enforced via `role:admin`, `role:teacher`, `role:guardian` middleware

## Client Context
- Duha International School, Bangladesh
- bKash integration planned for payments
- Bangla localization may be needed
- SMS gateway integration pending provider selection

## Companion Project
A separate Playwright-based scraper (`duha-playwright`) extracts attendance and financial data from eduexpert24 school management portals. Completed features: historical financial extraction, result extractor.

## Hive Memory
- `.hive/context/overview.md` — Project context for agents
- `.hive/sessions.json` — Session tracking (34 sessions)
- `.hive/features/` — Feature-specific context and plans
- `.claude/prds/` — Product requirements documents
- `.claude/plans/` — Implementation plans

---
*Last updated: 2026-07-27*
