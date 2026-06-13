# OSUMS — Full Documentation

> **Last Updated:** June 2026

---

## Recent Updates & Changes

### Installation & Setup
- **Installation Wizard** (`public/setup.php`): Interactive form for first-time setup — creates Super Admin, sends welcome email via MailHog, auto-locks after completion via `storage/installed.lock`
- **403 Forbidden Page**: Clean error card for already-installed systems, styled with system colors
- **404 Handling**: All invalid URLs redirect to `/login`
- **Database Backup**: If migrations fail, import `database/db_backup.sql` (see README for instructions)

### Academic Year Management
- New **CRUD UI** at `/academic-years` with DataTable, Create/Edit/Delete forms
- Only one academic year can be active at a time (auto-toggle)
- Accessible to Admin, HOD, and Teacher roles

### Exam Sitting System (Tanzanian University Standards)
- Repurposed `exam_types` table: **Regular** (1), **Special** (2), **Supplementary** (3), **Retake** (4)
- Added `exam_type_id` FK to `assessment_components` and `assessment_marks`
- New unique constraint: `(component_id, student_id, exam_type_id)` allows multiple sittings per student
- Updated `course_registrations.status` ENUM: added `Special`, `Supp`, `Retake`
- **Grading rules**: Regular/Special = no cap; Supplementary/Retake = capped at C (2.0)
- Mark entry UI includes Exam Sitting dropdown
- Assessment plans table shows Sitting column

### Teacher Subject Assignment
- Fixed cascading dropdowns (department → academic year → semester → subject)
- Added `no-select2` class to prevent Select2 from hiding native options
- Switched from `sync()` to explicit delete + insert for per-year assignments
- Added `academic_year_id` FK (replaced string column)
- Teachers now see **all assigned subjects** across all years (not just current year heuristic)

### Student Management
- Auto-creation: changed from `firstOrCreate` to `updateOrCreate` to ensure fields are always updated
- **Create Missing Student Accounts**: Admin UI to bulk-create user accounts for students without them
- Registration deletion now uses registration ID (not student ID)
- Registration cancel returns redirect instead of JSON

### Mark Entry & Assessments
- Fixed `firstWhere` → `where()->first()` (Laravel 5.2 compatibility)
- Fixed empty `whereIn()` causing SQL errors
- Added `(float)` cast in `computeGrade` to handle non-numeric values
- Store method now handles both `scores[comp_id][student_id]` and legacy `ca[]`/`ue[]` formats
- Bulk upload template includes existing marks
- Missing marks detection during grade computation (redirects to mark entry with details)

### Student Dashboard
- Subjects grouped by **Academic Year + Semester** in collapsible panels
- Shows per-subject CA, UE, Grade, Grade Point

### UI/UX
- Assessment plans table: DataTable with search, sort, pagination, collapsible toggle
- Assessment plans: Components column shows all component names (not just count)
- Dashboard link hidden from students in sidebar
- User Management table: uses `all()` instead of `paginate()` for DataTable
- Department table: same fix

### Bug Fixes
- `SoftDeletingScope` PHP 7.4 count() fix (`count((array)$joins)`)
- Exam deletion: added missing `destroy` method
- Exam type deletion: changed from POST form to direct GET link
- Fee edit: removed `control_number` from updatable fields (read-only)
- User edit: added "Student" option to group dropdown
- Dormitory "Student List" sidebar link corrected
- Many AJAX cascading fixes across attendance, exams, assessments

---

# OSUMS – Open Source University Management System

---

## Table of Contents
1. [Introduction](#introduction)
2. [Prerequisites](#prerequisites)
3. [System Architecture Overview](#system-architecture-overview)
4. [Installation & Setup](#installation--setup)
   - 4.1 [Clone the Repository](#clone-the-repository)
   - 4.2 [Environment Configuration](#environment-configuration)
   - 4.3 [Docker Development Environment](#docker-development-environment)
   - 4.4 [Running the Application](#running-the-application)
   - 4.5 [Database Migration & Seeding](#database-migration--seeding)
   - 4.6 [Default Credentials](#default-credentials)
5. [Modules & Features](#modules--features)
   - 5.1 [User Management & RBAC](#user-management--rbac)
   - 5.2 [Student Management](#student-management)
   - 5.3 [Course & Subject Management](#course--subject-management)
   - 5.4 [Student Registration (Semester-based)](#student-registration-semester-based)
   - 5.5 [Teacher & Subject Assignment](#teacher--subject-assignment)
   - 5.6 [Attendance Management](#attendance-management)
   - 5.7 [Examination & Assessment System](#examination--assessment-system)
   - 5.8 [Fee Collection & GePG Integration](#fee-collection--gepg-integration)
   - 5.9 [Library Management](#library-management)
   - 5.10 [Dormitory Management](#dormitory-management)
   - 5.11 [Student Portal](#student-portal)
   - 5.12 [Reporting](#reporting)
   - 5.13 [Dashboard & Analytics](#dashboard--analytics)
6. [Database Schema Overview](#database-schema-overview)
7. [API Endpoints](#api-endpoints)
8. [Testing Strategy](#testing-strategy)
9. [Deployment](#deployment)
10. [Troubleshooting](#troubleshooting)

---

## Introduction

**OSUMS** (Open Source University Management System) is a **Laravel 5.2** web application that provides a complete suite of tools for managing academic institutions. It follows **Tanzania TCU (Tanzania Commission for Universities)** grading standards and integrates with **GePG (Government e-Payment Gateway)** for fee collections.

The system covers:
- Student lifecycle (admission → registration → assessment → graduation)
- Course/curriculum management with year-semester structure
- TCU-compliant CA/UE assessment with user-definable components
- RBAC with roles: Admin, Head of Department, Teacher, Accountant, Student
- Government payment gateway integration (GePG)
- Student self-service portal
- Email notifications via MailHog (development)

**Tech Stack:** Laravel 5.2, PHP 7.4 (Docker), MySQL 8.0, Bootstrap 3 (Gentelella theme), jQuery, Chart.js, Select2, PNotify.

---

## Prerequisites

| Category | Requirement | Reason |
|----------|-------------|--------|
| **Operating System** | Linux / macOS / Windows (WSL2) | Docker and PHP CLI |
| **Docker Engine** | Docker >= 24.0 + Compose v2 | Containerized services |
| **Git** | >= 2.40 | Clone repository |
| **Memory** | 4 GB RAM minimum | Docker containers |

---

## System Architecture Overview

```
+-------------------+       +-------------------+       +-------------------+
|   Web Browser     | <---> |   PHP-FPM (Laravel)| <---> |   MySQL 8.0       |
+-------------------+       +-------------------+       +-------------------+
                                    |
                                    v
                            +-------------------+
                            |   MailHog (email)  |
                            +-------------------+
```

- **app** – PHP 7.4-FPM running Laravel 5.2
- **db** – MySQL 8.0 (or MariaDB 11.8)
- **mailhog** – Email testing interface (http://localhost:8025)

---

## Installation & Setup

### 4.1 Clone the Repository
```bash
git clone https://github.com/noumanTunda/University-Management-System.git
cd University-Management-System
```

### 4.2 Environment Configuration
```bash
cp .env.example .env
```
Edit `.env` — the defaults work out of the box:
```dotenv
APP_NAME=OSUMS
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=172.17.0.1
DB_PORT=3306
DB_DATABASE=homestead
DB_USERNAME=root
DB_PASSWORD=secure-password

MAIL_DRIVER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=admin@osums.edu
MAIL_FROM_NAME=OSUMS
```

Generate application key:
```bash
docker compose exec app php artisan key:generate
```

### 4.3 Docker Development Environment
```bash
docker compose up -d
```
This starts three containers:
- `app` – PHP 7.4-FPM with Laravel
- `db` – MySQL 8.0
- `mailhog` – SMTP testing (web UI at http://localhost:8025)

### 4.4 Running the Application
```bash
# Install dependencies
docker compose exec app composer install

# Generate app key (if not done above)
docker compose exec app php artisan key:generate
```

Access the app at **http://localhost:8080**

### 4.5 Database Migration & Seeding
```bash
# Run all migrations
docker compose exec app php artisan migrate

# Seed initial data
docker compose exec app php artisan db:seed
```

### 4.6 Default Credentials
| Role | Login | Password |
|------|-------|----------|
| **Admin** | `admin` | `admin` |
| **Teacher** | `teacher` | `teacher` |
| **Accountant** | `account` | `account` |
| **Student** | Student ID (e.g. `T24-03-00000`) | Last name (e.g. `Tunda`) |

---

## Modules & Features

### 5.1 User Management & RBAC

**Roles:** Admin, Head of Department, Teacher, Accountant, Student

| Role | Permissions |
|------|-------------|
| **Admin** | Full system access |
| **Head of Department (HOD)** | Department-level management, assessment templates, teacher assignments |
| **Teacher** | Mark entry, attendance, own subjects only |
| **Accountant** | Fee collection, GePG bills management |
| **Student** | Self-service portal (results, attendance, fees, library) |

- Custom `Role`/`Permission` models with polymorphic `user_role` pivot
- Middleware: `admin`, `hod`, `teacher`, `account`, `student`
- Gates defined in `AuthServiceProvider` for Blade directives (`@can`, `@if(Gate::check(...))`)

### 5.2 Student Management
- **Admission** – Create students with photo upload, auto-assign department from course
- **Bulk Import** – CSV upload with BOM stripping, auto-creates user accounts
- **Course Assignment** – Students assigned to courses linked to their department
- **Auto User Creation** – Each student gets a `users` account (login = idNo, password = lastName)
- **Profile View** – Personal info, photo, course, department

### 5.3 Course & Subject Management
- **Courses** – Defined per department with duration_years (1-4)
- **Subjects** – Assigned to courses via year + semester matrix (curriculum builder)
- **Curriculum Matrix** – Visual grid: Year 1-4 × Semester 1-2 with searchable checkboxes
- **Credit Tracking** – Total credits per year and semester

### 5.4 Student Registration (Semester-based)
- **Batch + Academic Year System:**
  - **Batch** = Admission year (e.g., 2022-2023 batch)
  - **Academic Year** = Year being registered FOR
- **Validation:**
  - Registration year must be >= admission year
  - Registration year must be <= admission year + course duration
- **Semesters** – Only Semester 1 or Semester 2 (no L1T1-L4T2 system)
- **Bulk Registration** – Select multiple students from a batch and register them

### 5.5 Teacher & Subject Assignment
- Assign teachers to subjects via `teacher_subject` pivot table
- Academic year support - each subject can have a different teacher per year
- Teachers only see their current year subjects by teacher's department

### 5.6 Attendance Management
- Mark attendance per subject, session, and semester
- Prevent duplicate entries for same student/day
- View and filter attendance records

### 5.7 Examination & Assessment System

#### Assessment Plans
- **Plans** link a subject + semester with components
- **Components** – User-definable items with type (CA/UE), max_score, weight
- **Default Plan** – Auto-created with Course Work (CA 40%) + University Exam (UE 60%)

#### Assessment Plans & Templates (Merged)
- Templates are now **plans with is_template = true** — no separate tables
- HOD/Admin create reusable templates as assessment plans with is_template flag
- **Default template:** "Standard Course" – Test 1 (20%) + Test 2 (20%) + University Exam (60%)
- Teachers select a template when creating a new plan → components pre-filled
- Templates displayed inline on the Assessment Plans page
- HOD/Admin create **reusable templates** with predefined components
- **Default template:** "Standard Course" – Test 1 (20%) + Test 2 (20%) + University Exam (60%)
- Teachers can use a template when creating a new plan → components pre-filled

#### Mark Entry
- **New CA/UE Entry** – Dynamic table with columns from assessment plan
- **Legacy Entry** – Simple CA (max 40) + UE (max 60) input
- **Bulk Upload - CSV with columns matching assessment plan components
- **Downloadable Template - CSV with enrolled students + component columns (Quiz, Lab, Test 1, Test 2, UE, etc.)

#### Grade Computation
- Uses `CourseRegistration::computeGrade()` static method
- TCU scale: A=5.0, B+=4.0, B=3.0, C=2.0, D=1.0, F=0.0
- Grades stored in `course_registrations` with ca_score, ue_score, grade_letter, grade_point, status

### 5.8 Fee Collection & GePG Integration

#### Fee Management
- Define fee types with amounts
- Record payments (add payment form)
- View fee collection history

#### GePG Payment System
- **Fee Allocation (Accountant)** — Unified form: select Academic Year + Course → students load by registration → add fee types to list (Add/Remove/Select All) → generate 12-digit control numbers for each student × fee type
- **Duplicate Prevention** — Same fee type + student + academic year → skipped
- **Student Pay Page** — View bills (Amount, Paid, Due, Status), click Pay for partial/full payment with phone capture
- **Partial Payments** — Pay less than due → status = "Partial" until fully paid
- **Student Self-Service** — Request missing control number: pick fee type from department list → system auto-generates 12-digit control number instantly (no accountant approval)
- **Accountant Bills** — DataTable with search/sort/pagination, mark as paid, edit (control number read-only), delete unpaid bills
- **Academic Year Filter** — Bills page filterable by academic year
- **Penalties & Special Fees** — Separate simple page for one-off fees (late registration, library fines)
- **Phone Recording** — Every student payment records payer mobile number
- **Control Numbers** — 12-digit, unique, immutable after issuance

### 5.9 Library Management
- Book catalog with search (title, author, code) — accessible to all authenticated users
- Issue/Return workflow (teachers + students)
- Track borrowed books per student
- Students can browse books, borrow, view their borrowed list

### 5.10 Dormitory Management
- Room allocation per student
- Track dormitory assignments
- **Student My Room** — Students view their own dormitory + room number + address

### 5.11 Student Portal
Login with student ID + last name → redirected to personal dashboard:
- **Dashboard** – Stats cards (registrations, attendance, books, bills)
- **My Results** – Collapsible panels per academic year + semester
- **My Attendance** – Date, subject, present/absent
- **Pay Fees** – View bills (Amount, Paid, Due, Status), pay with simulated GePG, request missing control numbers
- **Library** – Search books, borrow, view borrowed books
- **My Room** – View dormitory assignment (dormitory name, address, room number)

### 5.12 Reporting
- Result spreadsheets per subject
- Transcript generation
- Attendance reports

### 5.13 Dashboard & Analytics
- Colored stat cards (students, courses, books, fees)
- Chart.js graphs (exam performance)
- Quick links to common actions

---

## Database Schema Overview

| Table | Purpose |
|-------|---------|
| `users` | System login accounts (all roles) |
| `roles` / `permissions` / `user_role` | RBAC |
| `students` | Student profiles (admission, personal info) |
| `courses` | Programs of study with duration_years |
| `subject` | Subjects with code, credit, levelTerm |
| `course_subject` | Pivot: courses ↔ subjects with semester |
| `department` | Academic departments |
| `attendances` | Daily attendance records |
| `exams` / `exam_types` | Legacy exam marks |
| `assessment_plans` / `assessment_components` / `assessment_marks` | CA/UE assessment system (is_template flag for reusable templates) |
| `teacher_subject` | Teacher-subject assignment with academic_year pivot |
| `course_registrations` | Final grades (ca_score, ue_score, grade) |
| `registrations` | Semester registrations (batch-based) |
| `fees` | Fee types per department |
| `fee_collections` / `fee_collection_items` | Legacy fee payments |
| `gepg_bills` / `gepg_payment_receipts` | GePG integration (control numbers, receipts) |
| `chart_of_accounts` / `journal_entries` / `journal_entry_items` | Double-entry accounting system |
| `fee_invoices` / `invoice_items` | Student fee invoices with line items |
| `payment_allocations` | Links payments to invoices |
| `academic_years` / `semesters` | Calendar structure (max 2 semesters per year) |
| `books` / `borrow_books` | Library |
| `dormitory` / `dormitory_rooms` | Hostel management |

---

## API Endpoints

All routes are web-based (no REST API). Key AJAX endpoints:

| Method | URL | Purpose |
|--------|-----|---------|
| GET | `/students/{dept}/{session}` | Student list by dept + session |
| GET | `/students-batch/{dept}/{batch}` | Students by batch for registration |
| GET | `/subject/{dept}/{semester}` | Subjects by dept + semester |
| GET | `/exam-marks/subjects/{deptId}` | Subjects for cascading dropdown |
| GET | `/exam-marks/semesters/{yearId}` | Semesters for cascading dropdown |
| GET | `/exam-marks/entry/{subjectId}/{semesterId}` | Marks entry table HTML |
| POST | `/gepg/callback` | GePG payment webhook |
| GET | `/gepg/students/{courseId}/{yearId}` | Students by course + academic year |
| GET | `/gepg/fees-course/{courseId}` | Fees filtered by course department |
| GET | `/gepg/allstudents` | All students for penalties dropdown |
| GET | `/exam-marks/components/{subjectId}/{semesterId}` | Assessment components for CSV template |

---

## Testing Strategy

The system does not include automated test suites in the current build. Manual testing is done via the browser interface.

For future development:
- PHPUnit for unit/feature tests
- Browser tests with Laravel Dusk (if upgraded to Laravel 8+)

---

## Deployment

### Production Requirements
- **PHP:** 7.4+ (compatible), 8.x recommended for performance
- **Database:** MySQL 8.0 or MariaDB 10.5+
- **Web Server:** Nginx or Apache with PHP-FPM
- **SSL:** Let's Encrypt for HTTPS

### Production Checklist
```bash
# Set production mode
APP_ENV=production
APP_DEBUG=false

# Optimize Laravel
php artisan route:cache
php artisan config:cache
php artisan view:clear

# Set proper file permissions
chmod -R 775 storage bootstrap/cache
```

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| **"Class 'App\Fees' not found"** | Class is `Fee` (singular), not `Fees` |
| **"Undefined variable" in compact()** | Check for empty strings in `compact('',...)` |
| **Migration error: duplicate column** | Check `migrations` table for already-applied entries |
| **CSV upload returns 0 students** | BOM bytes in header — stripping added |
| **"continue 2" error** | Only one loop level — use `continue` not `continue 2` |
| **Select2 not working** | Ensure `select2.min.css` and `select2.full.min.js` are loaded in master layout |
| **Student can't login** | Ensure user account exists in `users` table with `login = idNo`, `group = 'Student'` |
| **Mail not sending** | MailHog runs on port 1025 (SMTP), UI at port 8025 |

---

## Future Roadmap (Tier 4-5 Enhancements)

### Tier 4: Advanced Features
| # | Enhancement | Status |
|---|-------------|--------|
| E16 | **RESTful API** — Mobile app integration endpoints with token auth | 📋 TODO |
| E17 | **Multi-tenancy** — Support multiple institutions with isolated data | 📋 TODO |
| E18 | **SMS notifications** — Fee reminders, result alerts via SMS gateway | 📋 TODO |
| E19 | **Localization** — Swahili language pack, RTL support | 📋 TODO |
| E20 | **Live payment gateway** — GePG, NMB, M-Pesa production integration | 📋 TODO |
| E21 | **PDF transcript** — Official TCU transcript generation | 📋 TODO |
| E22 | **Student ID card** — Printable ID with barcode/QR | 📋 TODO |
| E23 | **Online course registration** — Students self-register for subjects | 📋 TODO |
| E24 | **Academic calendar** — Event management (exams, holidays, deadlines) | 📋 TODO |

### Tier 5: DevOps & Quality
| # | Enhancement | Status |
|---|-------------|--------|
| E25 | **CI/CD pipeline** — GitHub Actions: lint → test → build → deploy | 📋 TODO |
| E26 | **PHPUnit test suite** — Unit + feature tests, minimum 80% coverage | 📋 TODO |
| E27 | **Production Docker** — Nginx, Redis, supervisor, Horizon config | 📋 TODO |
| E28 | **Automated backups** — Daily DB dump to S3/cloud storage | 📋 TODO |
| E29 | **Monitoring** — Sentry error tracking, uptime monitoring | 📋 TODO |
| E30 | **Laravel upgrade** — Migrate from 5.2 to 10.x (LTS) | 📋 TODO |

---

## Appendix A: Accounting & Billing Flow — Complete Audit

This section documents the complete logical flow of the billing, payment, receipt, and accounting system.

### 1. Chart of Accounts (Static Structure)

```
Assets                        Liabilities                   Income                     Expense
──────────────────────────────────────────────────────────────────────────────────────────────
1001 Cash & Bank              2001 Deferred Revenue         4001 Tuition Fees           5001 Salaries
1002 Student Receivables                                      4002 Laboratory Fees        5002 Utilities
                                                              4003 Library Fees           5003 General Expenses
                                                              4004 Registration Fees
                                                              4005 Penalties & Fines
                                                              4006 Other Income
```

### 2. Complete Billing → Payment → Receipt → Accounting Flow

```
┌─────────────────────────────────────────────────────────────────────────┐
│                        FEE ALLOCATION (Accountant)                      │
│                                                                         │
│  1. Select Academic Year + Course + Students + Fee Type                 │
│  2. System generates 12-digit Control Number for each student           │
│  3. Bill created in gepg_bills (status: Issued)                        │
│  4. Bill stored with: student_id, control_number, amount,               │
│     paid_amount=0, bill_description, academic_year, status='Issued'     │
└─────────────────────────────────────────────────────────────────────────┘
                                   │
                                   ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                     STUDENT PAYMENT (Simulated GePG)                    │
│                                                                         │
│  1. Student views bills in portal (Amount | Paid | Due | Status)        │
│  2. Clicks "Pay" on a bill with Due > 0                                 │
│  3. Enters payment amount (can be partial, ≤ Due)                       │
│  4. Enters phone number for receipt                                     │
│  5. Confirms amount (JS: re-enter to match)                             │
│  6. System processes payment:                                           │
│     a. Creates gepg_payment_receipt (transaction_id, amount_paid,       │
│        payment_provider='Simulated GePG', payer_mobile, paid_at)        │
│     b. Updates gepg_bills.paid_amount += payment                        │
│     c. Status logic:                                                    │
│        - If new paid_amount >= amount  → status = 'Paid'                │
│        - If new paid_amount > 0 && < amount → status = 'Partial'        │
│     d. If fully paid: creates fee_collections record                    │
└─────────────────────────────────────────────────────────────────────────┘
                                   │
                                   ▼
┌─────────────────────────────────────────────────────────────────────────┐
│               ACCOUNTANT MANUAL PAYMENT (Mark as Paid)                  │
│                                                                         │
│  1. Accountant clicks "Mark as Paid" on any unpaid bill                 │
│  2. System:                                                             │
│     a. Sets paid_amount = full amount                                   │
│     b. Changes status to 'Paid'                                         │
│     c. Creates gepg_payment_receipt (provider='Manual')                 │
│     d. Creates fee_collections record                                   │
└─────────────────────────────────────────────────────────────────────────┘
                                   │
                                   ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                   ACCOUNTANT (Edit Bill - Limited)                      │
│                                                                         │
│  Can edit:     amount, bill_description, status                         │
│  Cannot edit:  control_number (read-only after issuance)                │
│                                                                         │
│  Status options: Issued, Partial, Paid, Expired, Pending                │
└─────────────────────────────────────────────────────────────────────────┘
                                   │
                                   ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                       ACCOUNTING REPORTS                                │
│                                                                         │
│  Chart of Accounts  →  Balance per account (debit/credit)               │
│  General Journal    →  All journal entries with line items              │
│  Trial Balance      →  Total debits = Total credits                     │
│  Fee Invoices       →  Student invoices with payment status             │
│  GePG Bills         →  All bills with amount, paid, due, status         │
└─────────────────────────────────────────────────────────────────────────┘
```

### 3. Key Business Rules

| Rule | Description |
|------|-------------|
| **One-time allocation** | Same fee type + same student + same academic year → skipped |
| **Partial payments** | Student can pay less than bill amount → status = "Partial" |
| **Full payment** | Only when paid_amount >= amount → status = "Paid" |
| **Control number immutability** | Once issued, control numbers cannot be edited |
| **Academic year binding** | Fees allocated per academic year only |
| **Fee-department binding** | Fees filtered by the course's department |
| **Receipt phone capture** | Every student payment records payer phone |

### 4. Table Relationships

```
gepg_bills (one)
    ├── student_id → students
    ├── control_number (unique, 12-digit)
    ├── amount, paid_amount, status, academic_year
    │
    └── gepg_payment_receipts (many)
        ├── control_number → gepg_bills.control_number
        ├── transaction_id, amount_paid, payer_mobile
        └── payment_provider ('Simulated GePG', 'Manual')

fee_collections (created on full payment for backward compatibility)
    ├── students_id → students
    ├── payableAmount, paidAmount, payDate
    └── lateFee

chart_of_accounts → journal_entry_items → journal_entries

fee_invoices → invoice_items (alternative billing path)
    ├── student_id, invoice_no, total_amount, paid_amount, status
    └── items: description, amount, account_id
```

---

*Prepared on **June 12, 2026**. This document is version-controlled alongside the source code.*
