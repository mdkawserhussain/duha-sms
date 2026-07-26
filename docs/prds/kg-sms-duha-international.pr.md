# KG-SMS — Kindergarten School Management System

## Problem

Duha International School manages daily diary logs, student attendance, parent communication, fee tracking, and academic records through fragmented manual processes — paper notebooks, WhatsApp messages, and spreadsheets. This creates data loss risk, duplicate entry work across staff, delayed parent visibility into their child's day, and no single source of truth for attendance or academic records.

## Evidence

- Assumption — needs validation via school staff interviews and process observation
- SRS proposal documents a 3-role system (Admin, Teacher, Guardian) with 60+ features across all modules, suggesting the school has already scoped requirements through prior consultation
- Budget of BDT 1,50,000 is pre-approved, indicating organizational commitment

## Users

- **Primary (Admin)**: School administrator managing the entire system — teacher assignments, guardian approvals, attendance policies, fee structures, reports. Single administrative user or small admin team at one school location.
- **Secondary (Teacher)**: Classroom teachers marking attendance, writing daily diary entries, uploading student work, publishing report cards, communicating with guardians. 3 teachers across kindergarten classes.
- **Secondary (Guardian)**: Parents/guardians of students viewing daily diary, attendance, fees, report cards, and messaging teachers. ~45 guardians.
- **Not for**: Students directly (no student-facing interface), other schools (single-tenant for now), government/regulatory bodies.

## Hypothesis

We believe **a unified Laravel-based web platform covering Admin, Teacher, and Guardian modules** will **replace manual diary logs, paper attendance, and fragmented parent communication** for **Duha International School's staff and parents**.
We'll know we're right when **teachers spend less than 5 minutes daily on attendance + diary entries, guardians receive same-day diary updates, and admin can generate monthly attendance reports in under 2 minutes**.

## Success Metrics

| Metric | Target | How measured |
|---|---|---|
| Teacher daily entry time (diary + attendance) | < 5 min | Self-reported + system timestamps |
| Guardian same-day diary visibility | 100% | Diary entries published before end of school day |
| Monthly attendance report generation | < 2 min | Admin time from request to Excel export |
| Guardian self-registration success rate | > 90% | Registration completions / attempts |
| System uptime | 99.5% | Server monitoring over 30-day windows |
| Support tickets post-launch | < 5 / month for first 3 months | Ticket log |

## Scope

**MVP** — Full SRS scope: all three modules (Admin, Teacher, Guardian) with all listed features, deployed on a VPS with SSL. The SRS explicitly states "All features listed in Sections 04–06 are fully included in the quoted price. No features are deferred to a future phase."

**Out of scope**

- Mobile app (iOS/Android) — deferred to future add-on per SRS
- Biometric/RFID attendance integration — future add-on
- Online fee payment gateway (bKash/SSL Commerce) — future add-on
- Multi-school / multi-tenancy — single school only
- Third-party SMS charges — client-managed separately

## Delivery Milestones

| # | Milestone | Outcome | Status | Plan |
|---|---|---|---|---|
| 1 | Laravel project scaffold + auth system | Admin/Teacher/Guardian can log in with role-based access | pending | — |
| 2 | Admin module — user & teacher management | Admin can manage guardian/teacher accounts and assignments | pending | — |
| 3 | Admin module — class, attendance & diary | Admin can view attendance reports, diary logs, export to Excel | pending | — |
| 4 | Admin module — calendar, fees & notices | Admin can set fees, manage calendar, publish announcements | pending | — |
| 5 | Teacher module — diary & attendance | Teachers can mark attendance, write diary entries, upload work | pending | — |
| 6 | Teacher module — communication & reports | Teachers can message guardians, publish report cards, manage exams | pending | — |
| 7 | Guardian module — full feature set | Guardians can view diary, attendance, fees, message teachers | pending | — |
| 8 | Notifications, file storage & export | Firebase FCM push, Cloudinary/S3 file uploads, Excel export | pending | — |
| 9 | Deployment & handoff | VPS setup, Nginx, SSL, seeded data, admin training | pending | — |

## Open Questions

- [ ] Is the full SRS scope confirmed as v1, or should we cut to a smaller MVP (e.g., Admin + Teacher only first)?
- [ ] Who are the 3 teachers and 45 guardians? Do they need a registration/onboarding flow, or will admin create all accounts?
- [ ] What is the current process today? (paper diary? WhatsApp group? spreadsheet?) This affects migration approach.
- [ ] Is the Laravel stack confirmed for all modules? (SRS says React + Node.js/Nest.js — we're changing backend to Laravel, but frontend is still React + Tailwind?)
- [ ] Who handles VPS provisioning — Oikko AI or the school's IT?
- [ ] Are there existing student/guardian records to import, or is this a greenfield start?

## Risks

| Risk | Likelihood | Impact | Mitigation |
|---|---|---|---|
| Scope creep from client adding features mid-build | High | High | SRS states change requests require 24h confirmation + new addendum |
| Guardian onboarding adoption (self-registration may confuse non-technical parents) | Medium | High | Provide simple phone-number flow + admin can create accounts as fallback |
| Single developer bottleneck (3-person team) | Medium | Medium | Clear milestone gates, staged delivery, parallel work on independent modules |
| VPS reliability for production (single server) | Medium | Medium | Include monitoring, backups, and a documented restore process |
| Guardian notification fatigue | Low | Low | Configurable notification preferences per guardian |

---
*Status: DRAFT — requirements only. Implementation planning pending via /plan.*
*Tech stack: Laravel (backend) + React.js + Tailwind CSS (frontend) + PostgreSQL + Firebase FCM + Cloudinary/S3*
