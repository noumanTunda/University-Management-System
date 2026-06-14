# Software Requirements Specification (SRS)

## for

# Open Source University Management System (OSUMS)

**Version:** 2.0  
**Prepared:** June 2026  
**Project Repository:** [https://github.com/noumanTunda/University-Management-System](https://github.com/noumanTunda/University-Management-System)

---

## Table of Contents

1. [Introduction](#1-introduction)
   - 1.1 [Purpose](#11-purpose)
   - 1.2 [Document Conventions](#12-document-conventions)
   - 1.3 [Intended Audience and Reading Suggestions](#13-intended-audience-and-reading-suggestions)
   - 1.4 [Product Scope](#14-product-scope)
   - 1.5 [References](#15-references)
2. [Overall Description](#2-overall-description)
   - 2.1 [Product Perspective](#21-product-perspective)
   - 2.2 [Product Functions](#22-product-functions)
   - 2.3 [User Classes and Characteristics](#23-user-classes-and-characteristics)
   - 2.4 [Operating Environment](#24-operating-environment)
   - 2.5 [Design and Implementation Constraints](#25-design-and-implementation-constraints)
   - 2.6 [Assumptions and Dependencies](#26-assumptions-and-dependencies)
3. [System Features](#3-system-features)
   - 3.1 [Installation & Setup Wizard](#31-installation--setup-wizard)
   - 3.2 [Authentication & Authorization (RBAC)](#32-authentication--authorization-rbac)
   - 3.3 [User Management](#33-user-management)
   - 3.4 [Department Management](#34-department-management)
   - 3.5 [Student Management](#35-student-management)
   - 3.6 [Course & Subject Management](#36-course--subject-management)
   - 3.7 [Academic Year Management](#37-academic-year-management)
   - 3.8 [Semester Management](#38-semester-management)
   - 3.9 [Student Registration](#39-student-registration)
   - 3.10 [Teacher & Subject Assignment](#310-teacher--subject-assignment)
   - 3.11 [Attendance Management](#311-attendance-management)
   - 3.12 [Assessment & Examination System](#312-assessment--examination-system)
   - 3.13 [Exam Sitting Types (Tanzanian Standards)](#313-exam-sitting-types-tanzanian-standards)
   - 3.14 [Mark Entry & Grading](#314-mark-entry--grading)
   - 3.15 [Results & Reporting](#315-results--reporting)
   - 3.16 [Fee Collection & GePG Integration](#316-fee-collection--gepg-integration)
   - 3.17 [Enterprise Accounting](#317-enterprise-accounting)
   - 3.18 [Library Management](#318-library-management)
   - 3.19 [Dormitory Management](#319-dormitory-management)
   - 3.20 [Student Portal](#320-student-portal)
   - 3.21 [Dashboard & Analytics](#321-dashboard--analytics)
   - 3.22 [Email Notifications](#322-email-notifications)
4. [External Interface Requirements](#4-external-interface-requirements)
   - 4.1 [User Interfaces](#41-user-interfaces)
   - 4.2 [Hardware Interfaces](#42-hardware-interfaces)
   - 4.3 [Software Interfaces](#43-software-interfaces)
   - 4.4 [Communication Interfaces](#44-communication-interfaces)
5. [Non-Functional Requirements](#5-non-functional-requirements)
   - 5.1 [Performance Requirements](#51-performance-requirements)
   - 5.2 [Security Requirements](#52-security-requirements)
   - 5.3 [Reliability Requirements](#53-reliability-requirements)
   - 5.4 [Availability Requirements](#54-availability-requirements)
   - 5.5 [Maintainability Requirements](#55-maintainability-requirements)
   - 5.6 [Portability Requirements](#56-portability-requirements)
   - 5.7 [Scalability Requirements](#57-scalability-requirements)
   - 5.8 [Usability Requirements](#58-usability-requirements)
   - 5.9 [Compliance Requirements](#59-compliance-requirements)
6. [Database Schema](#6-database-schema)
   - 6.1 [Core Tables](#61-core-tables)
   - 6.2 [Assessment Tables](#62-assessment-tables)
   - 6.3 [Financial Tables](#63-financial-tables)
   - 6.4 [Academic Tables](#64-academic-tables)
   - 6.5 [Supporting Tables](#65-supporting-tables)
7. [Appendix A: Glossary](#7-appendix-a-glossary)
8. [Appendix B: Issue Tracking & Future Enhancements](#8-appendix-b-issue-tracking--future-enhancements)

---

## 1. Introduction

### 1.1 Purpose

This Software Requirements Specification (SRS) document provides a comprehensive description of the **Open Source University Management System (OSUMS)**. It defines the functional and non-functional requirements, system architecture, user interactions, and external interfaces for the software system. The document is intended to serve as a complete reference for developers, testers, system administrators, and stakeholders involved in the development, deployment, and maintenance of the system.

### 1.2 Document Conventions

- **Must / Shall**: Indicates a mandatory requirement
- **Should**: Indicates a recommended requirement
- **May**: Indicates an optional requirement
- **Bold text**: Highlights key terms and feature names
- `Code blocks`: Indicate file names, commands, or technical identifiers

### 1.3 Intended Audience and Reading Suggestions

| Audience | Sections of Interest |
|----------|---------------------|
| **Developers** | Sections 3, 5, 6 |
| **System Administrators** | Sections 2.4, 3.1, 4, 5.2 |
| **Project Managers** | Sections 1, 2, 8 |
| **Quality Assurance** | Sections 3, 5 |
| **End Users (Faculty)** | Sections 3.4–3.15 |
| **End Users (Students)** | Section 3.20 |

### 1.4 Product Scope

OSUMS is a web-based university management system designed for Tanzanian higher learning institutions. It manages the entire academic lifecycle including student admissions, semester registration, course and subject management, TCU-compliant assessment and grading, fee collection with GePG (Government e-Payment Gateway) integration, library and dormitory operations, enterprise accounting, and role-based access control. The system is built on Laravel 5.2 with a MySQL/MariaDB backend and runs in a Docker containerized environment.

**Key objectives:**
- Automate university administrative workflows
- Enforce TCU (Tanzania Commission for Universities) grading standards
- Provide secure role-based access for all user types
- Integrate with government e-payment systems for fee collection
- Deliver a student self-service portal
- Maintain comprehensive audit trails for financial transactions

### 1.5 References

| Document | Source |
|----------|--------|
| Laravel 5.2 Documentation | https://laravel.com/docs/5.2 |
| MySQL 8.0 Documentation | https://dev.mysql.com/doc/ |
| TCU Grading Guidelines | Tanzania Commission for Universities |
| GePG Integration Standards | Government e-Payment Gateway |
| IEEE Std 830-1998 | Recommended Practice for SRS |

---

## 2. Overall Description

### 2.1 Product Perspective

OSUMS is a **new, custom-built system** that replaces manual/paper-based university management processes. It is a self-contained web application with the following high-level architecture:

```
┌──────────────────────────────────────────────────────────┐
│                    Client Browser                         │
├──────────────────────────────────────────────────────────┤
│                  Apache / Nginx                           │
├──────────────────────────────────────────────────────────┤
│               Laravel 5.2 (PHP 7.4)                      │
│  ┌──────────┬──────────┬──────────┬──────────────────┐   │
│  │  Routes  │  Middle- │  Cont-   │    Views (Blade) │   │
│  │          │  ware    │  rollers │  (Gentelella UI) │   │
│  └──────────┴──────────┴──────────┴──────────────────┘   │
│  ┌──────────────────────────────────────────────────┐    │
│  │              Eloquent ORM / Models                │   │
│  └──────────────────────────────────────────────────┘    │
├──────────────────────────────────────────────────────────┤
│                  MySQL 8.0 / MariaDB                      │
├──────────────────────────────────────────────────────────┤
│              MailHog (SMTP Email Testing)                 │
└──────────────────────────────────────────────────────────┘
```

### 2.2 Product Functions

The system provides the following major functions:

1. **System Installation**: Interactive wizard for first-time setup, creates Super Admin, locks after install
2. **User & Role Management**: Create, edit, delete users with role-based access (Admin, HOD, Teacher, Accountant, Student)
3. **Department Management**: CRUD operations for academic departments
4. **Student Lifecycle**: Admission, document upload, course assignment, semester registration, graduation
5. **Course & Subject Management**: Define courses, subjects, credit hours, curriculum mapping per year/semester
6. **Academic Year Management**: CRUD for academic years with single-active toggle
7. **Teacher Assignment**: Assign subjects to teachers with academic year context
8. **Attendance Tracking**: Record and manage student attendance per subject
9. **Assessment & Grading**: Define assessment plans, components (CA/UE), compute grades with TCU standards
10. **Exam Sitting Types**: Regular, Special, Supplementary, Retake with differentiated grading rules
11. **Mark Entry**: Single and bulk CSV upload of marks with pre-existing data support
12. **Fee Collection**: GePG bill generation, control number management, payment reconciliation
13. **Enterprise Accounting**: Double-entry bookkeeping, chart of accounts, journal entries, trial balance
14. **Library Management**: Book catalog, borrowing, returns, stock tracking
15. **Dormitory Management**: Room assignment, sign in/out with approval workflow
16. **Student Portal**: Self-service dashboard for results, attendance, fees, library, room
17. **Reporting**: Subject-wise and student-wise results, financial reports, attendance reports
18. **Dashboard Analytics**: Statistics, charts for students, courses, fees, attendance

### 2.3 User Classes and Characteristics

| User Class | Description | Privileges |
|------------|-------------|------------|
| **Admin** | System administrator with full access | All modules, user management, system configuration |
| **HeadOfDepartment (HOD)** | Department head | Department-specific data, teacher assignments, assessment plans, reports |
| **Teacher** | Faculty member | Mark entry, attendance, assessment plans (own subjects), student results view |
| **Accountant** | Finance staff | Fee collection, GePG management, accounting module, invoicing |
| **Student** | Enrolled student | View results, attendance, pay fees, borrow books, view room |

### 2.4 Operating Environment

| Component | Specification |
|-----------|---------------|
| **Web Server** | Apache 2.4 or Nginx |
| **Backend Language** | PHP 7.4 |
| **Framework** | Laravel 5.2 |
| **Database** | MySQL 8.0 / MariaDB 11.8.6 |
| **Frontend** | Bootstrap 3, jQuery, Gentelella Admin Theme |
| **Containerization** | Docker & Docker Compose |
| **Email** | MailHog (development), SMTP (production) |
| **Operating System** | Linux (Debian/Ubuntu/Kali), macOS, Windows (WSL) |

### 2.5 Design and Implementation Constraints

- **Framework version locked**: Laravel 5.2 — must use `lists()` instead of `pluck()`, no `now()` helper, `render()` instead of `links()`
- **PHP 7.4 compatibility**: Must avoid PHP 8+ features; `count()` must receive array or Countable
- **Docker-first deployment**: All services run in containers via `docker-compose.yml`
- **Database engine**: InnoDB with foreign key constraints and soft deletes
- **Frontend**: Must maintain Gentelella admin theme consistency
- **Cascading selects**: Department → Year → Semester → Subject dependency chain

### 2.6 Assumptions and Dependencies

- **Assumptions:**
  - The institution uses the Tanzania TCU grading system (CA 40% + UE 60%)
  - Academic years follow the "YYYY-YYYY" naming convention
  - Each academic year has exactly 2 semesters
  - Students have unique identification numbers (RegNo)
  - Teachers are assigned to subjects per academic year

- **Dependencies:**
  - PHP 7.4 or compatible runtime
  - MySQL 8.0 / MariaDB 10.3+ for JSON column support
  - Composer for dependency management
  - Docker & Docker Compose for containerized deployment
  - `doctrine/dbal` for migration column modifications
  - Select2, DataTables, Chart.js, SweetAlert, Switchery JS libraries

---

## 3. System Features

### 3.1 Installation & Setup Wizard

**ID:** F-001  
**Priority:** Critical  
**Actor:** System Installer (first-time)

**Description:** A self-contained `setup.php` script in the public directory handles initial system initialization.

**Functional Requirements:**
- FR-001.1: Script must check for `storage/installed.lock` before executing
- FR-001.2: Script must check `users` table for existing records
- FR-001.3: If lock file exists OR users exist → display 403 Forbidden page, terminate
- FR-001.4: Display form with fields: First Name, Last Name, Description, Username, Email, Password, Confirm Password
- FR-001.5: Validate all inputs (email format, password min 8 chars, passwords match, unique username)
- FR-001.6: Hash password using `password_hash()` with `PASSWORD_BCRYPT`
- FR-001.7: Create admin user record with group='Admin'
- FR-001.8: Write `storage/installed.lock` with timestamp on success
- FR-001.9: Send welcome email via Laravel `Mail::raw()` (MailHog compatible)
- FR-001.10: UI must match system Gentelella theme (dark sidebar, light form panel)
- FR-001.11: Redirect to `/login` on completion

### 3.2 Authentication & Authorization (RBAC)

**ID:** F-002  
**Priority:** Critical  
**Actor:** All users

**Description:** Role-based access control with middleware gates for each user role.

**Functional Requirements:**
- FR-002.1: Login using username/login and password
- FR-002.2: Password verification against bcrypt hash
- FR-002.3: Session-based authentication with cookie
- FR-002.4: Five user groups: `Admin`, `HeadOfDepartment`, `Teacher`, `Account`, `Student`
- FR-002.5: Middleware per role: `admin`, `hod`, `teacher`, `account`, `student`
- FR-002.6: Login route has throttle middleware (10 attempts per minute)
- FR-002.7: `/setup` (no .php) → 404 → redirect to `/login`
- FR-002.8: Invalid URLs → redirect to `/login`
- FR-002.9: Logout destroys session
- FR-002.10: Dashboard link hidden from students in sidebar

### 3.3 User Management

**ID:** F-003  
**Priority:** High  
**Actor:** Admin

**Description:** Full CRUD for system users with role assignment.

**Functional Requirements:**
- FR-003.1: List users in DataTable with search, sort, pagination
- FR-003.2: Create user with fields: firstname, lastname, login, password, group, email, description
- FR-003.3: Edit user details including group assignment
- FR-003.4: Group options: Admin, Teacher, HeadOfDepartment, Account, Student
- FR-003.5: Delete user with SweetAlert confirmation
- FR-003.6: **Create Missing Student Accounts**: Button to list students without user accounts, bulk-create with login=idNo, password=lastName
- FR-003.7: Email notification sent to newly created student accounts

### 3.4 Department Management

**ID:** F-004  
**Priority:** High  
**Actor:** Admin, HOD

**Description:** Manage academic departments.

**Functional Requirements:**
- FR-004.1: List departments with name, code, credits, years, description
- FR-004.2: Create department with name, code, credit hours, duration years
- FR-004.3: Edit department details
- FR-004.4: Soft-delete department (SoftDeletes trait)
- FR-004.5: DataTable with search, sort, pagination

### 3.5 Student Management

**ID:** F-005  
**Priority:** High  
**Actor:** Admin, HOD

**Description:** Student admission, profile management, and user account auto-creation.

**Functional Requirements:**
- FR-005.1: Admit new student with fields: firstName, lastName, idNo, email, mobile, department, session (admission year), guardian details, photo
- FR-005.2: Auto-create User account using `updateOrCreate` with login=idNo, password=lastName, group=Student
- FR-005.3: Bulk CSV import with BOM byte stripping
- FR-005.4: Upload student photos
- FR-005.5: View student profile with photo, department, course, registration history
- FR-005.6: Edit student details
- FR-005.7: Soft-delete student
- FR-005.8: Assign course to student (must be registered first)
- FR-005.9: NECTA index numbers (Form 4 and Form 6) for Tanzanian verification

### 3.6 Course & Subject Management

**ID:** F-006  
**Priority:** High  
**Actor:** Admin, HOD

**Description:** Define courses (programs of study) and subjects with curriculum mapping.

**Functional Requirements:**
- FR-006.1: Create course with name, code, department, duration years, credit hours
- FR-006.2: Subject builder grid: Years × Semesters matrix with searchable subject selector
- FR-006.3: Assign subjects to course with semester context
- FR-006.4: Credit tracking per year and semester with counters
- FR-006.5: Dynamic year visibility based on course duration
- FR-006.6: Edit course subject mapping preserving year/semester assignment
- FR-006.7: Create, edit, delete subjects with name, code, department, credit value

### 3.7 Academic Year Management

**ID:** F-007  
**Priority:** High  
**Actor:** Admin, HOD, Teacher

**Description:** CRUD for academic years with active-state management.

**Functional Requirements:**
- FR-007.1: List academic years in DataTable with name, active status, created date
- FR-007.2: Create academic year with name (format: YYYY-YYYY) and active toggle
- FR-007.3: Edit academic year name and active status
- FR-007.4: Delete academic year with confirmation
- FR-007.5: **Single-active constraint**: Setting one year active deactivates all others
- FR-007.6: Visual indicator (label) for active/inactive status

### 3.8 Semester Management

**ID:** F-008  
**Priority:** High  
**Actor:** System (automated)

**Description:** Each academic year has exactly 2 semesters (Semester 1, Semester 2).

**Functional Requirements:**
- FR-008.1: Semesters auto-created per academic year via migration
- FR-008.2: Semester 1 and Semester 2 per year (no L1T1-L4T2 system)
- FR-008.3: Semesters linked to academic years via FK
- FR-008.4: Semesters referenced by course registrations, assessment plans, mark entries

### 3.9 Student Registration

**ID:** F-009  
**Priority:** High  
**Actor:** Admin, HOD

**Description:** Register students for a specific academic year and semester.

**Functional Requirements:**
- FR-009.1: Select department (with "All Departments" option), batch (admission year), academic year, semester
- FR-009.2: Load students by batch (admission year), filterable by department
- FR-009.3: Year validation: registration year ≥ admission year AND ≤ admission year + course duration
- FR-009.4: Prevent duplicate registration per student per year+semester
- FR-009.5: Retain Department and Batch selections after form submission
- FR-009.6: View registered students list with DataTable
- FR-009.7: Cancel registration with confirmation (SweetAlert popup)
- FR-009.8: Registration records linked to academic year via session field

### 3.10 Teacher & Subject Assignment

**ID:** F-010  
**Priority:** High  
**Actor:** Admin, HOD

**Description:** Assign subjects to teachers with academic year context.

**Functional Requirements:**
- FR-010.1: Select teacher (searchable Select2), academic years (multi-select), subjects (multi-select)
- FR-010.2: "Select All Years" and "Clear" buttons for year selection
- FR-010.3: Assign subjects across multiple academic years simultaneously
- FR-010.4: View current assignments table: Teacher, Subject, Code, Department, Academic Year
- FR-010.5: DataTable with search, sort, pagination on assignments
- FR-010.6: Edit teacher's subject assignment per year
- FR-010.7: **Per-assignment deletion**: Delete one subject-year combination, not all
- FR-010.8: Teachers see only their own subjects in assessments, attendance, exam mark entry
- FR-010.9: Pivot table `teacher_subject` with `academic_year_id` FK (normalized)

### 3.11 Attendance Management

**ID:** F-011  
**Priority:** Medium  
**Actor:** Teacher

**Description:** Record and manage student attendance per subject.

**Functional Requirements:**
- FR-011.1: Select department, academic year, semester, subject, date
- FR-011.2: Load registered students for that subject/year/semester
- FR-011.3: Mark present/absent via toggle (Yes/No select)
- FR-011.4: Edit attendance after submission
- FR-011.5: Optional SMS notification toggle
- FR-011.6: View attendance history
- FR-011.7: Teachers filter subjects by their assignments

### 3.12 Assessment & Examination System

**ID:** F-012  
**Priority:** Critical  
**Actor:** Admin, HOD, Teacher

**Description:** Define assessment plans with components (CA/UE) for each subject and semester.

**Functional Requirements:**
- FR-012.1: Create assessment plan linked to subject + semester
- FR-012.2: Define components: name, type (CA/UE), max score, weight percentage
- FR-012.3: CA total weight = 40%, UE total weight = 60% (TCU standard)
- FR-012.4: Templates: reusable component structures with `is_template` flag (merged into plans table)
- FR-012.5: Auto-create plan with default components (Course Work CA + University Exam UE)
- FR-012.6: Components column in plan list shows all component names (e.g., Test 1, Test 2, Quiz, Lab)
- FR-012.7: DataTable with search, sort, pagination, collapsible toggle
- FR-012.8: Sitting type column (Regular / Special / Supplementary / Retake)
- FR-012.9: Compute grades from marks button with missing marks detection
- FR-012.10: Teachers see only plans for their assigned subjects

### 3.13 Exam Sitting Types (Tanzanian Standards)

**ID:** F-013  
**Priority:** High  
**Actor:** Admin, HOD, Teacher

**Description:** Support for different exam sittings with differentiated grading rules.

**Functional Requirements:**
- FR-013.1: Four sitting types in `exam_types` table: Regular (1), Special (2), Supplementary (3), Retake (4)
- FR-013.2: **Regular**: Standard CA + UE, no grade cap
- FR-013.3: **Special**: Carries over original CA, uses new UE score, no grade cap
- FR-013.4: **Supplementary**: Uses original CA + new UE, **capped at grade C (2.0)**
- FR-013.5: **Retake**: Full re-assessment, **capped at grade C (2.0)**
- FR-013.6: `exam_type_id` FK in `assessment_components` and `assessment_marks`
- FR-013.7: Unique constraint `(component_id, student_id, exam_type_id)` allows multiple sittings per student
- FR-013.8: Course registration status updated to reflect sitting type (Special, Supp, Retake)

### 3.14 Mark Entry & Grading

**ID:** F-014  
**Priority:** Critical  
**Actor:** Teacher

**Description:** Enter student marks for assessment components with automatic grade computation.

**Functional Requirements:**
- FR-014.1: Cascading selectors: Department → Academic Year → Semester → Subject
- FR-014.2: "Refresh Data" button as manual fallback (auto-loads on selection)
- FR-014.3: Select exam sitting type (Regular/Special/Supplementary/Retake) via dropdown
- FR-014.4: Load registered students with pre-filled existing marks
- FR-014.5: Input fields per component per student (number, min=0, max=component max)
- FR-014.6: Save marks with `exam_type_id` context
- FR-014.7: Auto-create assessment plan with default components if none exists
- FR-014.8: Compute final grade using `computeGrade($ca, $ue, $examTypeId)`
- FR-014.9: **Bulk upload**: Download CSV template with enrolled students and existing marks
- FR-014.10: Upload CSV with marks mapped to component columns
- FR-014.11: Error handling for missing marks, invalid values
- FR-014.12: Grade saved to `course_registrations` with letter, point, status

### 3.15 Results & Reporting

**ID:** F-015  
**Priority:** Medium  
**Actor:** Admin, HOD, Teacher, Student

**Description:** View subject-wise and student-wise results.

**Functional Requirements:**
- FR-015.1: Subject-wise results: select department, year, semester, subject → display marks and grades
- FR-015.2: Student-wise results: select student → display all subjects with grades per semester
- FR-015.3: Fallback to assessment system (`course_registrations`) if legacy `exams` table has no data
- FR-015.4: Student portal shows results grouped by academic year and semester in collapsible panels
- FR-015.5: Grade point, grade letter, CA score, UE score displayed

### 3.16 Fee Collection & GePG Integration

**ID:** F-016  
**Priority:** High  
**Actor:** Accountant, Student

**Description:** Government e-Payment Gateway integration for fee management.

**Functional Requirements:**
- FR-016.1: **Fee types**: Create fee types per department with amount and description
- FR-016.2: **Fee allocation**: Select academic year, course, students (multi-select with Select All), fee types (with Add/Add All, optgroup by department, hide used options)
- FR-016.3: Generate 12-digit unique control numbers per fee × student × year
- FR-016.4: **Student payment portal**: View bills with Paid/Due amounts, request control numbers, make partial payments
- FR-016.5: **Accountant view**: All bills with DataTable, filter by academic year, search, delete unpaid bills
- FR-016.6: **Penalties**: Separate penalty allocation page with academic year, student Select2, description, amount
- FR-016.7: Payment receipts linked to bills
- FR-016.8: `control_number` field is read-only after generation
- FR-016.9: Duplicate check prevents same fee × student × year combination
- FR-016.10: GePG callback/webhook endpoint excluded from CSRF verification

### 3.17 Enterprise Accounting

**ID:** F-017  
**Priority:** Medium  
**Actor:** Accountant, Admin

**Description:** Double-entry bookkeeping system with full audit trail.

**Functional Requirements:**
- FR-017.1: **Chart of Accounts**: Create account types (Asset, Liability, Equity, Income, Expense) with codes and names
- FR-017.2: **Journal Entries**: Record double-entry transactions with debit/credit items
- FR-017.3: **Trial Balance**: View all accounts with debits and credits, ensure balance
- FR-017.4: **Fee Invoices**: Generate invoices with line items linked to chart of accounts
- FR-017.5: **Payment Allocations**: Allocate payments to specific invoices
- FR-017.6: Seeded default accounts (12 standard accounts)
- FR-017.7: Journal entries show account names (not just codes)

### 3.18 Library Management

**ID:** F-018  
**Priority:** Medium  
**Actor:** Admin, Teacher, Librarian

**Description:** Book catalog and borrowing management.

**Functional Requirements:**
- FR-018.1: **Book catalog**: Add books with title, author, ISBN, publisher, department, quantity, rack number
- FR-018.2: **Issue books**: Select student and book, record issue date, expected return date
- FR-018.3: **Return books**: Mark as returned, calculate fines for late returns
- FR-018.4: **Borrowing history**: View all transactions with status
- FR-018.5: **Stock tracking**: Monitor available copies
- FR-018.6: Students can view their borrowed books in the student portal

### 3.19 Dormitory Management

**ID:** F-019  
**Priority:** Medium  
**Actor:** Admin, HOD, Teacher, Student

**Description:** Dormitory room assignment, sign in/out with approval workflow.

**Functional Requirements:**
- FR-019.1: **Dormitory CRUD**: Create dormitories with name, address, capacity
- FR-019.2: **Assign students**: Assign student to room with room number, monthly fee, join date
- FR-019.3: **Student list**: View students assigned to each dormitory with room details
- FR-019.4: **Fee collection**: Track dormitory fee payments
- FR-019.5: **Sign out**: Student signs out of room → sets `isActive=0`, records timestamp and reason, alerts key submission
- FR-019.6: **Sign in request**: Student requests sign-in → creates pending request in `dormitory_requests` table
- FR-019.7: **Approval**: Teacher/HOD approves/rejects sign-in request → sets `isActive=1`, clears sign-out data
- FR-019.8: Pending requests visible in Teacher/HOD sidebar

### 3.20 Student Portal

**ID:** F-020  
**Priority:** High  
**Actor:** Student

**Description:** Self-service dashboard for students to view academic information.

**Functional Requirements:**
- FR-020.1: **Dashboard**: Stats cards (registrations, attendance days, borrowed books, pending bills)
- FR-020.2: **Course registrations**: View registration history (year, semester, department)
- FR-020.3: **Enrolled subjects & results**: Grouped by Academic Year → Semester in collapsible panels, showing subject name, code, CA score, UE score, grade letter, grade point
- FR-020.4: **Assessments page**: Detailed per-subject component breakdown with marks
- FR-020.5: **Attendance**: View attendance records by subject
- FR-020.6: **Fee payment**: View bills, request control numbers, make payments
- FR-020.7: **Library**: View borrowed books
- FR-020.8: **My Room**: View dormitory assignment, sign out, request sign-in
- FR-020.9: Login using student ID (idNo) as username and last name as password

### 3.21 Dashboard & Analytics

**ID:** F-021  
**Priority:** Medium  
**Actor:** Admin, HOD, Teacher, Accountant

**Description:** Home page with statistics and charts.

**Functional Requirements:**
- FR-021.1: Colored stat cards: total students, courses, users, books, departments, dormitories
- FR-021.2: Bar chart comparing students per department
- FR-021.3: Exam performance chart (A, B+, B, C, D, E, F distribution)
- FR-021.4: Fee collection overview with collected vs pending amounts
- FR-021.5: Quick links to major modules
- FR-021.6: Compact layout, no hover effects, paler background colors

### 3.22 Email Notifications

**ID:** F-022  
**Priority:** Low  
**Actor:** System

**Description:** Automated email notifications for key events.

**Functional Requirements:**
- FR-022.1: **Welcome email**: Sent to new admin created via setup wizard with login credentials
- FR-022.2: **Student account creation**: Email sent when accounts are bulk-created for missing students
- FR-022.3: MailHog for development email testing (SMTP port 1025, UI port 8025)
- FR-022.4: Configurable mail driver via `.env` (SMTP, Mailgun, etc.)
- FR-022.5: Default from address: `noreply@osums.edu`

---

## 4. External Interface Requirements

### 4.1 User Interfaces

- **Theme**: Gentelella Admin Bootstrap 3 theme
- **Responsive**: Adapts to desktop and tablet viewports
- **Navigation**: Left sidebar with collapsible menu sections, top navigation bar
- **Data display**: DataTables with search, sort, pagination, export (CSV, Excel, PDF, Print)
- **Forms**: Consistent form styling with 50px input height, blue focus ring, uppercase labels
- **Notifications**: PNotify for flash messages, SweetAlert for confirmations
- **Selects**: Select2 for searchable dropdowns (except native multi-selects)
- **Login page**: Split layout with carousel images (left 62%) and login form (right 38%)
- **Setup page**: Same split layout as login with scrollable form on right

### 4.2 Hardware Interfaces

- No direct hardware interfaces required
- System runs on commodity server hardware
- Client machines require only a modern web browser

### 4.3 Software Interfaces

| Interface | Technology | Purpose |
|-----------|------------|---------|
| Database | MySQL 8.0 / MariaDB 11.8 | Data persistence |
| Web Server | Apache / Nginx | HTTP serving |
| PHP | 7.4 CLI + FPM | Application runtime |
| MailHog | SMTP server | Development email testing |
| GePG | XML/SOAP | Payment gateway (simulated) |
| Docker | Container runtime | Environment isolation |

### 4.4 Communication Interfaces

- **HTTP/HTTPS**: All web traffic
- **SMTP**: Email delivery via MailHog (port 1025) or configured SMTP server
- **MySQL protocol**: Database connection (port 3306)
- **Internal Docker network**: Container-to-container communication via service names

---

## 5. Non-Functional Requirements

### 5.1 Performance Requirements

- NFR-001: Page load time must be under 3 seconds for standard operations
- NFR-002: DataTable rendering must handle 10,000+ rows without browser freeze
- NFR-003: AJAX queries must return within 2 seconds
- NFR-004: Concurrent user sessions: minimum 50 simultaneous users
- NFR-005: Bulk CSV import of 500+ students must complete within 10 seconds

### 5.2 Security Requirements

- NFR-006: Passwords must be hashed using `PASSWORD_BCRYPT`
- NFR-007: All authenticated routes require session-based authentication
- NFR-008: Role-based middleware gates on all sensitive operations
- NFR-009: CSRF protection on all POST/PUT/DELETE requests (except GePG callback)
- NFR-010: Login throttling: 10 attempts per minute
- NFR-011: Setup wizard auto-locks via `storage/installed.lock` after completion
- NFR-012: SQL injection prevention via Eloquent ORM parameter binding
- NFR-013: XSS prevention via Blade `{{ }}` escaping
- NFR-014: Soft deletes used for critical data (students, departments, users)

### 5.3 Reliability Requirements

- NFR-015: Database transactions must use `DB::transaction()` for multi-table writes
- NFR-016: Failed operations must roll back completely (no partial writes)
- NFR-017: Form validation must prevent invalid data submission
- NFR-018: Email sending failures must not block primary operations (try-catch)

### 5.4 Availability Requirements

- NFR-019: System availability target: 99.5% uptime during business hours
- NFR-020: Docker container auto-restart policy for resilience
- NFR-021: Database connection pooling for stability

### 5.5 Maintainability Requirements

- NFR-022: Modular controller structure following Laravel conventions
- NFR-023: Blade template inheritance via `layouts.master`
- NFR-024: Environment configuration via `.env` file
- NFR-025: Database migrations for schema version control
- NFR-026: Comments and documentation for complex business logic

### 5.6 Portability Requirements

- NFR-027: Docker Compose for consistent cross-platform deployment
- NFR-028: PHP 7.4 compatible (no PHP 8+ exclusive features)
- NFR-029: MySQL/MariaDB interchangeable via environment config

### 5.7 Scalability Requirements

- NFR-030: Horizontal scaling possible via additional app containers behind load balancer
- NFR-031: Database indexing on frequently queried columns
- NFR-032: Pagination on all list views (server-side for large datasets)

### 5.8 Usability Requirements

- NFR-033: Consistent UI theme across all pages (Gentelella)
- NFR-034: Searchable dropdowns (Select2) for all entity selectors
- NFR-035: Clear error messages with field-level validation
- NFR-036: Breadcrumb navigation on key pages
- NFR-037: Confirmation dialogs (SweetAlert) for destructive actions
- NFR-038: Loading indicators during AJAX operations

### 5.9 Compliance Requirements

- NFR-039: TCU grading standards: CA 40% maximum, UE 60% maximum
- NFR-040: Supplementary exams capped at grade C (2.0) per Tanzanian university bylaws
- NFR-041: NECTA index numbers for student verification
- NFR-042: GePG control number format: 12 digits

---

## 6. Database Schema

### 6.1 Core Tables

| Table | Purpose | Key Columns |
|-------|---------|-------------|
| `users` | System users | id, login, password, firstname, lastname, email, group, description |
| `students` | Student profiles | id, idNo, firstName, lastName, email, department_id, course_id, session, necta_f4_index, necta_f6_index |
| `department` | Academic departments | id, name, code, credit, years, description |
| `subject` | Subjects/courses | id, name, code, credit, department_id |
| `courses` | Programs of study | id, name, code, department_id, duration_years, credit_hours |
| `course_subject` | Course-subject mapping | id, course_id, subject_id, semester (absolute 1-8) |

### 6.2 Assessment Tables

| Table | Purpose | Key Columns |
|-------|---------|-------------|
| `assessment_plans` | Assessment plan per subject+semester | id, subject_id, semester_id, ca_weight, ue_weight, is_template, template_name |
| `assessment_components` | Component within a plan | id, assessment_plan_id, exam_type_id, name, type (CA/UE), max_score, weight |
| `assessment_marks` | Student marks per component | id, assessment_component_id, student_id, exam_type_id, score |
| `course_registrations` | Student course enrollment with grade | id, student_id, subject_id, semester_id, ca_score, ue_score, grade_letter, grade_point, status, exam_type_id |
| `exam_types` | Sitting types (academic) | id, name (Regular/Special/Supplementary/Retake), description |
| `exams` | Legacy exam records | id, department_id, subject_id, session, levelTerm, exam, student_id, raw_score |

### 6.3 Financial Tables

| Table | Purpose | Key Columns |
|-------|---------|-------------|
| `fees` | Fee types | id, name, amount, department_id, academic_year |
| `fee_collections` | Collected fees | id, fee_id, student_id, amount, paid_amount, due_amount, balance, gepg_bill_id |
| `gepg_bills` | GePG payment bills | id, student_id, academic_year, control_number, amount, paid_amount, status, expires_at |
| `gepg_payment_receipts` | Payment receipts | id, gepg_bill_id, amount, phone, receipt_number, paid_at |
| `chart_of_accounts` | Accounting accounts | id, code, name, type, description |
| `journal_entries` | Double-entry journals | id, entry_date, description, reference, reference_type |
| `journal_entry_items` | Journal lines | id, journal_entry_id, account_id, debit, credit |
| `fee_invoices` | Student invoices | id, student_id, invoice_number, total_amount, status |
| `payment_allocations` | Payment-invoice links | id, fee_invoice_id, gepg_bill_id, amount |

### 6.4 Academic Tables

| Table | Purpose | Key Columns |
|-------|---------|-------------|
| `academic_years` | Academic year definitions | id, name, is_active |
| `semesters` | Semester per academic year | id, academic_year_id, semester_number |
| `registrations` | Student semester registration | id, students_id, session, levelTerm, department_id |
| `teacher_subject` | Teacher-subject assignment | id, user_id, subject_id, academic_year_id |
| `attendances` | Student attendance | id, students_id, subject_id, date, present |

### 6.5 Supporting Tables

| Table | Purpose | Key Columns |
|-------|---------|-------------|
| `dormitories` | Dormitory definitions | id, name, address |
| `dormitory_students` | Student room assignments | id, students_id, dormitories_id, roomNo, joinDate, isActive, signed_out_at |
| `dormitory_requests` | Sign-in approval requests | id, dormitory_student_id, student_id, type, status, approved_by |
| `books` | Library book catalog | id, name, authors, ISBN, quantity, department_id |
| `borrow_books` | Book borrowing records | id, book_id, students_id, BorrowDate, ReturnDate, Status |
| `guardians` | Student guardian info | id, name, relationship, phone, email |
| `guardian_student` | Guardian-student pivot | guardian_id, student_id |

---

## 7. Appendix A: Glossary

| Term | Definition |
|------|------------|
| **CA** | Continuous Assessment — coursework component (max 40% of total) |
| **UE** | University Examination — final exam component (max 60% of total) |
| **TCU** | Tanzania Commission for Universities |
| **GePG** | Government e-Payment Gateway — Tanzanian government payment system |
| **NECTA** | National Examinations Council of Tanzania |
| **HOD** | Head of Department |
| **RBAC** | Role-Based Access Control |
| **Control Number** | 12-digit unique bill identifier for GePG payments |
| **Supplementary** | Re-sit exam for failed courses, capped at grade C |
| **Special Exam** | Exam for students who missed regular sitting due to valid reasons |
| **Retake** | Full course repeat including both CA and UE |
| **Gentelella** | Bootstrap 3 admin theme used for the UI |
| **Select2** | jQuery-based searchable select dropdown library |
| **DataTable** | jQuery plugin for advanced table features (search, sort, paginate) |
| **SweetAlert** | JavaScript library for styled modal dialogs |
| **MailHog** | SMTP email testing tool for development environments |

---

## 8. Appendix B: Issue Tracking & Future Enhancements

### Known Issues

1. **Legacy `exams` table**: Decoupled from assessment system; results views fall back to `course_registrations`
2. **PHP 8+ compatibility**: Laravel 5.2 uses deprecated `ReflectionParameter::getClass()` — requires PHP 7.4
3. **`doctrine/dbal` required**: Needed for migration column changes
4. **Marital status overflow**: `student.maritalStatus` column uses `varchar(6)` — insufficient for longer values

### Planned Enhancements (Tier 4-5)

| Area | Enhancement |
|------|-------------|
| **API** | RESTful API endpoints for mobile app integration |
| **Multi-tenancy** | Support for multiple institutions on single instance |
| **SMS Notifications** | Attendance alerts, payment reminders via SMS gateway |
| **Localization** | Swahili language support |
| **Online Payments** | Direct mobile money (M-Pesa, Tigo Pesa) integration |
| **PDF Transcripts** | Automated transcript generation with official format |
| **ID Card Generation** | Printable student/staff ID cards |
| **Online Registration** | Student self-registration for courses each semester |
| **CI/CD Pipeline** | Automated testing and deployment |
| **Test Suite** | Comprehensive PHPUnit and BrowserKit tests |
| **Production Docker** | Multi-stage builds, SSL, reverse proxy configuration |
| **Automated Backups** | Scheduled database and file backups |
| **Monitoring** | Application health checks, error tracking (Sentry) |

---

*End of SRS Document*
