# Duha SMS — Comprehensive School Management ERP

## Problem

K-12 schools globally — starting with Duha International School — operate across fragmented tools (spreadsheets, WhatsApp, separate attendance/billing apps, manual report cards). This causes data silos, duplicated effort, missed parent communications, and billing errors. No affordable open-source Laravel ERP exists that covers the full school lifecycle with localization for South Asian markets (bKash, Bangla, SMS gateways).

## Evidence

- **Duha International School** has committed to using this system as their primary operations platform (client commitment confirmed).
- Existing codebase (Laravel 11 + Vue 3) already implements core student CRUD, attendance, evaluations, fees, routines, messaging, and role-based access across admin/teacher/guardian dashboards — validated by 111 passing E2E tests across 3 viewports.

## Users

- **Primary**: School administrators (admin/accountant) who manage students, billing, attendance, exams, and staff operations daily.
- **Secondary**: Teachers (attendance entry, evaluations, diary, messaging), Parents/Guardians (fee status, attendance monitoring, leave requests, report cards).
- **Not for**: University-level systems, corporate training platforms, or single-classroom tools.

## Hypothesis

We believe **a comprehensive open-source school ERP with bKash integration, Bangla support, and full admin/teacher/parent workflows** will **eliminate tool fragmentation for K-12 schools** for **school administrators, teachers, and parents globally**. We'll know we're right when **Duha International School runs all operations on it and 3+ external schools adopt within 6 months**.

## Success Metrics

| Metric | Target | How measured |
|---|---|---|
| Duha School operational adoption | All modules used daily | Active user logs per role |
| External school adoption | 3+ schools within 6 months | GitHub stars + deployment inquiries |
| Parent engagement | 50%+ parents log in monthly | Active guardian sessions |
| Billing accuracy | Zero manual reconciliation errors | Payment mismatch reports |
| Attendance completeness | 95%+ daily entries submitted | Teacher attendance submission rate |

## Scope

**MVP — Full Feature Set** (all 7 modules, nothing deferred)

### Module 1: Administrative Architecture & System Setup
- Multi-tenant instance control (subdomain/custom domain mapping, database isolation, institutional branding, timezone/language/currency settings)
- Public portal integration (CMS for school website, management board publishing, public event calendar, online admission form processing)
- Role-Based Access Control (RBAC) — pre-configured roles (Super Admin, School Admin, Teacher, Student, Parent, Accountant) + custom roles with granular module-level permission toggles + IP-restricted admin login

### Module 2: Academic Infrastructure & Class Scheduling
- Academic hierarchy (academic year, term/semester setup; class, section, shift management)
- Subject catalog (core, elective, practical, optional; bulk import/export via Excel)
- Physical space allocation (building, floor, room cataloging with seat capacity tracking)
- Routine & schedule generator (conflict detection, break/lunch time slots, teacher period allocation, absent-teacher substitution mapping)

### Module 3: Student Lifecycle & Profile Management
- Admissions & registration workflow (public online portal with document/photo upload, admin verification dashboard, auto student ID/registration/roll number assignment, birth-date default passwords)
- Multi-guardian mapping & parent portal (multiple guardians per student, multi-child switching, real-time monitoring of attendance/grades/routines/fees across siblings)
- Academic progression (automated year-end promotion/retention, section transfer, historical record tracking)

### Module 4: Attendance Monitoring & Policy Automation
- Attendance execution (daily morning check-ins, subject-wise period tracking, teacher self-attendance logs)
- Policy automation (SMS gateway API config, automated parent alerts for consecutive absences, configurable mark deduction/fine penalty policies)
- Digital leave request system (online application with document upload, class teacher approval dashboard)

### Module 5: Examination, Grading & Seat Planning
- Exam setup & scheduling (quiz/midterm/final/custom types, schedule generation with venue/date/timing, automated admit card generation/printing)
- Grading engine & result processing (GPA/CGPA calculations, custom grade boundaries, weighted scores, bulk Excel mark uploads, admin result publishing pipeline with missing-subject validation, PDF report cards and transcripts)
- Automated exam seat planning (room capacity-based seat allocation, payment verification gate blocking students with outstanding dues)

### Module 6: Fees, Billing & Financial Management
- Fee structure & billing (customizable categories, automated recurring invoice generation, late fee rules, fine setup, scholarship/waiver adjustments)
- Payment processing (bKash merchant API integration, full/partial online payments, counter-side offline manual collection, PDF receipts/invoices)
- Accounting & financial ledger (income/expense tracking, balance sheet, liability reports, staff payroll with allowances/deductions/payslips)

### Module 7: Communication, Auxiliary & System Security
- Communication suite (targeted notices by audience, one-to-one teacher-parent/student messaging, SMS gateway integration)
- Auxiliary modules (library cataloging with ISBN tracking/checkouts/fines, hostel/dormitory allocation, fleet/route mapping, office inventory)
- Security & maintenance (database backup scheduling, CSV/Excel/PDF exports, audit logging, MFA, active session management)

## Out of Scope

None — full feature set is in scope for this build.

## Delivery Milestones

| # | Milestone | Outcome | Status | Plan |
|---|---|---|---|---|
| 1 | Core CRUD + Auth foundation | Students, Teachers, Guardians, Classes with role-based dashboards and Sanctum auth | complete | — |
| 2 | Academic infrastructure | Academic years, terms, subjects, room catalog, routine generator with conflict detection | pending | — |
| 3 | Attendance & leave system | Daily/period attendance, SMS alerts, leave request workflow, attendance policy enforcement | pending | — |
| 4 | Examination & grading engine | Exam scheduling, mark entry (bulk Excel), GPA/CGPA calculation, report card PDF generation | pending | — |
| 5 | Fees, billing & payment processing | Fee structures, invoice generation, bKash integration, offline payment, PDF receipts | pending | — |
| 6 | Financial ledger & payroll | Income/expense tracking, balance sheet, staff payroll with payslips | pending | — |
| 7 | Communication & messaging | Targeted announcements, teacher-parent messaging, SMS gateway integration | pending | — |
| 8 | Public portal & CMS | School website CMS, online admission form, public event calendar | pending | — |
| 9 | Auxiliary modules | Library, hostel, fleet/transport, office inventory | pending | — |
| 10 | Multi-tenant & security | Subdomain isolation, custom roles/permissions, MFA, database backups, audit logging | pending | — |
| 11 | Academic progression | Automated promotion/retention, section transfer, historical records | pending | — |
| 12 | Exam seat planning | Room capacity allocation, payment verification gate for seat eligibility | pending | — |

## Open Questions

- [ ] bKash merchant API: Do we have sandbox credentials, or is this simulated until production onboarding?
- [ ] SMS gateway: Which provider (Twilio, local Bangladeshi gateway)? Pricing model for bulk SMS?
- [ ] Multi-tenant: Is database-level isolation required for MVP, or single-tenant with branding first?
- [ ] Bangla localization: Full UI translation, or just document/report generation in Bangla?
- [ ] Exam seat planning: Is this a hard blocker for Duha, or can it be a later addition?
- [ ] Payroll: Is this accountant-managed manually, or integrated with government tax calculations?

## Risks

| Risk | Likelihood | Impact | Mitigation |
|---|---|---|---|
| bKash API onboarding delays | Medium | Payment module incomplete | Simulate payment flow; decouple bKash integration behind payment interface |
| Scope creep from full feature set | High | Delivery timeline slip | Strict milestone gating; each module ships independently |
| Multi-tenant complexity | High | Architecture rework | Start single-tenant; abstract tenant logic behind interface |
| Bangla RTL/i18n scope | Medium | UI rework | Use Laravel's built-in localization from day 1 |
| SMS gateway cost for bulk notifications | Medium | Operational cost surprise | Configurable gateway; allow schools to plug in their own provider |
| Audit logging volume | Low | Storage/performance | Configurable retention policies; archive old logs |

---
*Status: DRAFT — requirements only. Implementation planning pending via /plan.*
