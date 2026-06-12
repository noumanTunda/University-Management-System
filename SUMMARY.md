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
- Select2 searchable dropdowns for both teachers and subjects
- Subject suggestions filter by teacher's department

### 5.6 Attendance Management
- Mark attendance per subject, session, and semester
- Prevent duplicate entries for same student/day
- View and filter attendance records

### 5.7 Examination & Assessment System

#### Assessment Plans
- **Plans** link a subject + semester with components
- **Components** – User-definable items with type (CA/UE), max_score, weight
- **Default Plan** – Auto-created with Course Work (CA 40%) + University Exam (UE 60%)

#### Assessment Templates (NEW)
- HOD/Admin create **reusable templates** with predefined components
- **Default template:** "Standard Course" – Test 1 (20%) + Test 2 (20%) + University Exam (60%)
- Teachers can use a template when creating a new plan → components pre-filled

#### Mark Entry
- **New CA/UE Entry** – Dynamic table with columns from assessment plan
- **Legacy Entry** – Simple CA (max 40) + UE (max 60) input
- **Bulk Upload** – CSV upload with student list per subject+semester
- **Downloadable Template** – CSV with enrolled students pre-filled

#### Grade Computation
- Uses `CourseRegistration::computeGrade()` static method
- TCU scale: A=5.0, B+=4.0, B=3.0, C=2.0, D=1.0, F=0.0
- Grades stored in `course_registrations` with ca_score, ue_score, grade_letter, grade_point, status

### 5.8 Fee Collection & GePG Integration

#### Fee Management
- Define fee types with amounts
- Record payments (add payment form)
- View fee collection history

#### GePG Payment Gateway
- **Student Pay Page** – Select fee type → generate 12-digit control number
- **Accountant Bills** – View all bills, mark as paid, edit details
- **Auto-link** – Marking a bill as paid creates a `fee_collections` record
- **Webhook** – XML callback endpoint for GePG treasury

### 5.9 Library Management
- Book catalog with search (title, author, code)
- Issue/Return workflow
- Track borrowed books per student

### 5.10 Dormitory Management
- Room allocation per student
- Track dormitory assignments

### 5.11 Student Portal
Login with student ID + last name → redirected to personal dashboard:
- **Dashboard** – Stats cards (registrations, attendance, books, bills)
- **My Results** – Collapsible panels per academic year + semester
- **My Attendance** – Date, subject, present/absent
- **Pay Fees** – Generate GePG control numbers
- **Library** – Search books, view borrowed books

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
| `assessment_plans` / `assessment_components` / `assessment_marks` | CA/UE assessment system |
| `assessment_templates` / `assessment_template_components` | Reusable templates |
| `course_registrations` | Final grades (ca_score, ue_score, grade) |
| `registrations` | Semester registrations (batch-based) |
| `fees` / `fee_collections` | Fee types and payments |
| `gepg_bills` / `gepg_payment_receipts` | GePG integration |
| `books` / `borrow_books` | Library |
| `dormitory` / `dormitory_rooms` | Hostel management |
| `academic_years` / `semesters` | Calendar structure |

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

*Prepared on **June 12, 2026**. This document is version-controlled alongside the source code.*
