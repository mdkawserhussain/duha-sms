OIKKO AI Software Requirements Specification
Creative & Development Agency · oikkoai.com — Cover —
PREPARED FOR DUHA INTERNATIONAL SCHOOL · JUNE 2026
Kindergarten
School
Management System
| CLIENT        | PREPARED BY  | DATE ISSUED   | VALID UNTIL   |
| ------------- | ------------ | ------------- | ------------- |
| Duha          | Shuvo Saha · | June 24, 2026 | July 14, 2026 |
| International | COO          |               |               |
School
| 3          | 60+      | 45           | 1.5L      |
| ---------- | -------- | ------------ | --------- |
| USER ROLES | FEATURES | WORKING DAYS | TOTAL BDT |
COVERED
C
Duha International School
House #2481/A, P.C Road, Nayabazar Bishwa Road, Halisahar, Chattogram
TNEILC
A B
01890703760 · info@duhais.com · duhais.com
CONFIDENTIAL · PREPARED EXCLUSIVELY FOR DUHA INTERNATIONAL SCHOOL · DO
NOT DISTRIBUTE
Oikko AI · oikkoai@gmail.com Page 1 of 14

OIKKO AI Table of Contents & Summary
KG-SMS · Duha International School — 2 —
TABLE OF CONTENTS
01 Executive Summary 02 Project Scope & Deliverables
03 Data Ownership Model 04 Functional Requirements — Admin
Functional Requirements — Functional Requirements —
05 06
Teacher Guardian
07 Technology Stack 08 Project Team
09 Financial Proposal 10 Maintenance & Support
11 Terms & Conditions 12 Acceptance & Signatures
Executive Summary
01
This document is the Software Requirements Specification (SRS) and Development
Proposal for a complete, production-grade Kindergarten School Management System
(KG-SMS) for Duha International School. The platform covers three fully role-separated
modules — Admin, Teacher, and Guardian — within a single total investment of BDT
1,50,000.
The system replaces manual diary logs, paper-based attendance, and fragmented parent
communication with a unified digital platform. It is built on a fixed data-ownership model
that prevents record conflicts across roles and provides a clean audit trail for every action
in the system.
All features listed in Sections 04–06 are fully included in the quoted price. No
features are deferred to a future phase. Post-delivery support is available at BDT
3,000 per service call.
Oikko AI · oikkoai@gmail.com Page 2 of 14

OIKKO AI Project Scope
KG-SMS · Duha International School — 3 —
Project Scope & Deliverables
02
The following table defines what is in scope, what is out of scope but available as a
future add-on, and what third-party services are managed separately.
Admin module — full feature set Included
Teacher module — full feature set Included
Guardian module — full feature set Included
Role-based authentication (JWT) Included
Push notifications (Firebase FCM) Included
Excel export for attendance Included
File upload for documents & work samples Included
VPS hosting setup (1 year) Included
SSL/HTTPS enforcement Included
Mobile app (iOS/Android) Future Add-on
Biometric/RFID attendance integration Future Add-on
Online fee payment gateway (bKash/SSL Commerce) Future Add-on
Third-party SMS charges (per message) Client-managed
Oikko AI · oikkoai@gmail.com Page 3 of 14

OIKKO AI Data Ownership & Admin
KG-SMS · Duha International School — 4 —
Data Ownership Model
03
Each record type has a single authoritative owner. Other roles may view but cannot
overwrite the owner's data without approval.
| DATA DOMAIN          | OWNER | ACCESS RULES                       |
| -------------------- | ----- | ---------------------------------- |
| Emergency contacts & |       | Teacher & Admin view · Admin edits |
Guardian
| medical info |     | corrections only |
| ------------ | --- | ---------------- |
School-wide attendance Admin Teacher views & applies — cannot
| policy              |     | modify                   |
| ------------------- | --- | ------------------------ |
| Master exam routine |     | Teacher may add/edit own |
Admin
class/subject only
| School-wide notices | Admin | Teacher & Guardian view only |
| ------------------- | ----- | ---------------------------- |
| Class-level notices |       | Guardian views only          |
Teacher
Guardian account & profile Guardian All changes require Admin approval
before effect
Guardian onboarding path: self-register by phone number → enters Admin verification
queue → Admin approves. No guardian account is active without Admin sign-off.
Functional Requirements — Admin
04
Login & Setup
ADMIN
• Auto-seeded credentials on first setup
• Update ID and password after initial login
• Change account password from admin profile at any time
Oikko AI · oikkoai@gmail.com Page 4 of 14

OIKKO AI Functional Requirements — Admin
KG-SMS · Duha International School — 5 —
ADMIN User Management
• Add, edit, or remove guardian accounts
• Add, edit, or remove students under a guardian
• View guardian list with search and filter
• Verify guardian self-registration requests
• Review and approve guardian profile-change requests (approval queue)
ADMIN Teacher Management
• Add, edit, or remove teacher profiles
• Assign teachers to specific classes
• View teacher attendance; manually input and remove attendance records
ADMIN Diary, Attendance & Class Management
• View student-wise daily diary with search & filter
• Generate class-wise attendance reports
• Generate monthly attendance summaries
• Export monthly attendance data to Excel
• Set school-wide attendance policy (sole owner)
• Create, edit, or delete classes
• Assign teachers to classes
• Set and monitor class capacity limits
• Transfer students between classes
Oikko AI · oikkoai@gmail.com Page 5 of 14

OIKKO AI Functional Requirements — Admin
KG-SMS · Duha International School — 6 —
ADMIN Calendar, Routine & Fees
• Set fee structure per class; create fee categories
• Mark fee status (paid / due) per student
• Create master class routine and master exam routine
• Create school events and add holidays to calendar
• Create and publish school-wide announcements
ADMIN Documents, Settings & Reports
• Manage school policies and documents
• Configure system email and SMS settings
• API management for partner/data-delivery contacts
• Receive and manage guardian & teacher applications
• Total student count by class
• New admissions vs. withdrawals report
• Class-wise attendance percentage report
• Summarized student progress reports
• Class performance analysis dashboard
ADMIN Health & Safety
• View student health information
• View emergency contacts (edit for corrections only)
• View authorized pickup persons for each student
Oikko AI · oikkoai@gmail.com Page 6 of 14

OIKKO AI Functional Requirements — Teacher
KG-SMS · Duha International School — 7 —
Functional Requirements — Teacher
05
TEACHER Login & Setup
• Log in using Teacher ID with Date of Birth as initial password
• Update password immediately after first login
TEACHER Daily Routine & Scheduling
• View daily schedules, class routines, and exam routines
• View published notices and upcoming school events
TEACHER Diary Management
• View student-wise daily diaries with search and filters
• Record daily activities, meals, and behavior observations
• Edit and customize existing diary logs
• Bulk-add homework assignments for entire class
• Access and review full past diary history
TEACHER Attendance
• Mark daily attendance using Present/Absent (P/A)
• View historical student attendance records
• View, generate, and download Excel monthly summaries
• View and apply school-wide attendance policy
• See guardian-submitted absence/leave notifications when marking
Oikko AI · oikkoai@gmail.com Page 7 of 14

OIKKO AI Functional Requirements
KG-SMS · Duha International School — 8 —
TEACHER Academic & Student Management
• Input student evaluation marks
• Publish report cards (controlled — visible to guardians after publish)
• Upload individual student work samples
• Manage student profiles and full student directory
• Review student health information
• View emergency contacts and authorized pickup persons
TEACHER Communication, Exam & Applications
• Direct messaging with individual guardians
• Issue class-wide group notices (class-level only)
• View full conversation and message history
• Add or update exam routine for own class/subject
• Submit applications to Admin for any issue
• Push notifications for guardian messages and diary-comment activity
Functional Requirements — Guardian
06
GUARDIAN Login & Setup
• Self-register and log in using personal phone number
• Set a personalized password on first login
• Reset password using linked email or phone number
Oikko AI · oikkoai@gmail.com Page 8 of 14

OIKKO AI Functional Requirements — Guardian
KG-SMS · Duha International School — 9 —
GUARDIAN Profile Management
• View and update personal profile (changes require Admin approval)
• View profile status and verification state
• Add or update emergency contact persons
• Update child's medical information logs
• Add or update authorized pickup persons
• Electronically submit required documents (DOB cert, academic records)
GUARDIAN Student Diary
• View active daily diary log for their child
• Receive push notifications for new diary entries
• Browse past diary history by date, week, or month
• Leave optional comments on diary entries
GUARDIAN Academic, Attendance & Fees
• Monitor child's daily attendance status
• Submit advance absence/leave notification
• View teacher remarks and per-subject evaluation breakdowns
• View child's uploaded work samples
• View yearly report cards (once published by Teacher)
• Review monthly fee breakdown and paid/due status
GUARDIAN Communication, Schedule & Notices
• Direct messaging with the school
Oikko AI · oikkoai@gmail.com Page 9 of 14
• Submit applications to administration
• Push notifications for messages and announcements

OIKKO AI Tech Stack & Team
KG-SMS · Duha International School — 10 —
Technology Stack
07
| FRONTEND            | BACKEND           | DATABASE   |
| ------------------- | ----------------- | ---------- |
| React.js + Tailwind | Node.js / Nest.js | PostgreSQL |
CSS
| AUTHENTICATION   | PUSH NOTIFICATIONS | FILE STORAGE        |
| ---------------- | ------------------ | ------------------- |
| JWT + Role-Based | Firebase FCM       | Cloudinary / AWS S3 |
Access
| HOSTING     | SSL / SECURITY      | EXPORT       |
| ----------- | ------------------- | ------------ |
| VPS + Nginx | Let's Encrypt HTTPS | Excel (xlsx) |
Project Team
08
| AR       | AD        | SS         |
| -------- | --------- | ---------- |
| Apon Roy | Arnob Dey | Shuvo Saha |
CTO · Frontend Engineer CIO · Full-Stack Engineer COO · Backend Engineer
React.js UI, responsive Full-stack architecture, Robust APIs, database
design, UX implementation, backend API development, design, production
wireframing and prototype IT systems and reliability and client
| validation | infrastructure | communication |
| ---------- | -------------- | ------------- |
Oikko AI · oikkoai@gmail.com Page 10 of 14

| OIKKO AI                           |     | Financials |
| ---------------------------------- | --- | ---------- |
| KG-SMS · Duha International School |     | — 11 —     |
Financial Proposal
09
| # ITEM                                   | DETAILS                                | PRICE (BDT) |
| ---------------------------------------- | -------------------------------------- | ----------- |
| 01 System                                | Full scope — Admin, Teacher & Guardian | 1,25,000    |
| Development                              | modules                                |             |
| 02 Server / Hosting                      | VPS — 1 Year                           | 25,000      |
| Total Investment — All features included |                                        | 1,50,000    |
BDT
All prices in Bangladeshi Taka (BDT). Accepted: Bank Transfer, bKash (1.85% cashout
surcharge applies). Post-delivery support at BDT 3,000 per call.
PAYMENT MILESTONES
Project Kickoff BDT 75,000
1
Upon signing agreement or work order 50%
Development Complete BDT 45,000
2
Upon staging delivery & client approval 30%
Final Delivery BDT 30,000
3
Upon live deployment & sign-off 20%
Refund: Full refund if cancelled before development begins. Prorated if cancelled mid-
project based on work completed. No refund after successful delivery and client
acceptance.
| Oikko AI · oikkoai@gmail.com |     | Page 11 of 14 |
| ---------------------------- | --- | ------------- |

OIKKO AI Maintenance & Support
| KG-SMS · Duha International School |     | — 12 — |
| ---------------------------------- | --- | ------ |
Maintenance & Support
10
| SERVICE | COVERAGE | RATE |
| ------- | -------- | ---- |
Post-delivery support Bug fixes, minor adjustments, queries BDT 3,000 / call
New feature development Any scope beyond this SRS Quoted separately
| Hosting continuation | VPS renewal after year 1 | Market rate |
| -------------------- | ------------------------ | ----------- |
Support hours: Sunday–Thursday, 10:00 AM – 6:00 PM BST (UTC+6). Maintenance does not
include new feature development.
| Oikko AI · oikkoai@gmail.com |     | Page 12 of 14 |
| ---------------------------- | --- | ------------- |

OIKKO AI Terms & Conditions
KG-SMS · Duha International School — 13 —
Terms & Conditions
11
Proposal Validity Intellectual Property
1. All prices and terms are valid for 20 days. 1. Upon full payment, Client owns source
This proposal expires on July 14, 2026. code as perpetual, royalty-free,
worldwide license.
2. Scope changes after acceptance require
a new addendum. 2. Oikkoai may display project type
(anonymised) in portfolio materials.
Development Timeline
Confidentiality
1. Development begins 5 working days after
signed work order. 1. Both parties keep all proprietary
information confidential for 2 years after
2. Estimated 45 working days after final
termination.
UI/UX design approval.
2. This document must not be shared
3. Change requests require 24 business
without written permission from Oikkoai.
hours to confirm.
4. Maximum 3 design review rounds
Governing Law & Liability
included; additional rounds = new scope.
1. Governed by the laws of Bangladesh.
Payment & Late Fees Disputes resolved by good-faith
negotiation first, then binding arbitration
1. 50% of total cost due upon signing
in Dhaka.
contract or work order.
2. Oikkoai's total liability shall not exceed
2. 5 working-day grace period after any
total fees paid by the Client.
missed payment date.
3. If Client cancels after signing, the
3. Services may be suspended if payment is
advance paid is forfeited by Oikkoai.
not received within grace period.
4. Contract may be terminated after 10 Force Majeure
working days of suspension.
1. Neither party is liable for delays caused
by events beyond reasonable control.
Affected party notifies the other within 5
business days.
Oikko AI · oikkoai@gmail.com Page 13 of 14

OIKKO AI Acceptance & Signatures
KG-SMS · Duha International School — 14 —
Acceptance & Next Steps
12
Review this proposal and raise any questions with Shuvo Saha — oikkoai@gmail.com.
1
Confirm all feature requirements, additions, or exclusions in writing.
2
Sign below, or reply "Offer Accepted" by email to oikkoai@gmail.com to activate this
3
agreement.
Make initial 50% payment — BDT 75,000 — via Bank Transfer or bKash.
4
Schedule a 30-minute kickoff call to finalise parameters, timeline, and UI direction.
5
Development begins 5 working days after work order receipt.
6
An email reply of "Offer Accepted" to oikkoai@gmail.com is legally sufficient to activate
this agreement — a physical signature is not required for online agreements.
CLIENT — DUHA INTERNATIONAL DEVELOPER — OIKKO AI
SCHOOL
Full Name: Shuvo Saha
Full Name:
Designation: COO · Backend Engineer
___________________________________
Date: June 24, 2026
Designation:
________________________________
Date:
________________________________________
OIKKO AI CREATIVE & DEVELOPMENT · OIKKOAI.COM · OIKKOAI@GMAIL.COM
CONFIDENTIAL — PREPARED FOR DUHA INTERNATIONAL SCHOOL — NOT FOR
DISTRIBUTION
Oikko AI · oikkoai@gmail.com Page 14 of 14