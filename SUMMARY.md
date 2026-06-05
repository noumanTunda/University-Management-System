# OSUMS – Open Source University Management System (SRS Guide)

---

## Table of Contents
1. [Introduction](#introduction)
2. [Prerequisites](#prerequisites)
3. [System Architecture Overview](#system-architecture-overview)
4. [Installation & Setup](#installation--setup)
   - 4.1 [Clone the Repository](#clone-the-repository)
   - 4.2 [Environment Configuration](#environment-configuration)
   - 4.3 [Docker based Development Environment](#docker-based-development-environment)
   - 4.4 [Running the Application](#running-the-application)
   - 4.5 [Database Migration & Seeding](#database-migration--seeding)
5. [Functionalities & Detailed Descriptions](#functionalities--detailed-descriptions)
   - 5.1 [User Management & Authentication](#user-management--authentication)
   - 5.2 [Dashboard & Analytics](#dashboard--analytics)
   - 5.3 [Student Registration & Profile](#student-registration--profile)
   - 5.4 [Attendance Management](#attendance-management)
   - 5.5 [Fee Collection & Accounting](#fee-collection--accounting)
   - 5.6 [Library Management](#library-management)
   - 5.7 [Dormitory & Hostel Management](#dormitory--hostel-management)
   - 5.8 [Examination & Result Processing](#examination--result-processing)
   - 5.9 [Reporting & Export](#reporting--export)
6. [Functional Requirements (FR)](#functional-requirements-fr)
7. [Non‑Functional Requirements (NFR)](#non‑functional-requirements-nfr)
8. [System Requirements](#system-requirements)
9. [Deployment Options](#deployment-options)
10. [Testing Strategy](#testing-strategy)
11. [Maintenance & Extensibility](#maintenance--extensibility)
12. [FAQ & Troubleshooting](#faq--troubleshooting)
13. [References & Further Reading](#references--further-reading)
---

## Introduction
OSUMS (Open Source University Management System) is a **Laravel‑based web application** that provides a complete suite of tools for managing academic institutions. It covers core processes such as **student registration, attendance tracking, fee collection, library services, dormitory allocation, examinations, and reporting**. The system is designed to be **lightweight, extensible, and Docker‑friendly**, making it suitable for small colleges, training institutes, or as a learning platform for developers.

The repository you are looking at contains the full source code, Docker configuration, database migrations, seeders, and a set of **PHPUnit tests**. The application follows **MVC architecture**, uses **Eloquent ORM**, and ships with **Blade templates** for the UI.

---

## Prerequisites
| Category | Requirement | Reason |
|----------|-------------|--------|
| **Operating System** | Linux (any modern distro) or macOS. Windows users should use WSL2. | Docker and PHP CLI work best on Unix‑like environments. |
| **Docker Engine** | Docker >= 24.0, Docker Compose v2 (the `docker compose` command). | Provides isolated services (PHP‑FPM, MySQL, Nginx). |
| **Git** | >= 2.40 | To clone the repository. |
| **PHP** | 8.2 (only required for local development without Docker). | Laravel 10 requires PHP 8.1+. |
| **Composer** | >= 2.6 | Dependency management for PHP packages. |
| **Node.js & npm** | Node 20.x, npm 10.x (optional – only for front‑end asset compilation). | Required if you want to run `npm run dev` for hot‑reloading. |
| **Make (optional)** | GNU Make | Provides convenient shortcuts via the `Makefile`. |

---

## System Architecture Overview
```
+-------------------+        +-------------------+        +-------------------+
|   Web Browser     | <----> |   Nginx (proxy)   | <----> |   PHP‑FPM (Laravel) |
+-------------------+        +-------------------+        +-------------------+
                                   ^                         |
                                   |                         |
                                   v                         v
                           +-------------------+   +-------------------+
                           |   MySQL 8.0       |   |   Redis (cache)   |
                           +-------------------+   +-------------------+
```
* **Nginx** – Serves static assets and forwards PHP requests to the PHP‑FPM container.
* **PHP‑FPM** – Runs the Laravel application.
* **MySQL** – Primary relational database for all entities (students, fees, etc.).
* **Redis** – Optional cache and queue driver (used by Laravel’s cache and queue systems).

---

## Installation & Setup
### 4.1 Clone the Repository
```bash
git clone https://github.com/your‑org/osums.git
cd osums
```
> Replace the URL with the actual remote if you forked the project.

### 4.2 Environment Configuration
1. Copy the example environment file:
   ```bash
   cp .env.example .env
   ```
2. Edit `.env` to match your Docker service names (the defaults work out‑of‑the‑box):
   ```dotenv
   APP_NAME="OSUMS"
   APP_ENV=local
   APP_KEY=base64:$(php -r "echo base64_encode(random_bytes(32));")
   APP_DEBUG=true
   APP_URL=http://localhost

   LOG_CHANNEL=stack

   DB_CONNECTION=mysql
   DB_HOST=db          # Docker service name for MySQL
   DB_PORT=3306
   DB_DATABASE=osums
   DB_USERNAME=root
   DB_PASSWORD=secret

   BROADCAST_DRIVER=log
   CACHE_DRIVER=file
   QUEUE_CONNECTION=sync
   SESSION_DRIVER=file
   SESSION_LIFETIME=120
   ```
3. Generate the application key (Laravel requirement):
   ```bash
   docker compose exec app php artisan key:generate
   ```

### 4.3 Docker based Development Environment
The repository ships a `docker-compose.yml` that defines three services:
* `app` – PHP‑FPM with Composer and the source code mounted.
* `db` – MySQL 8.0.
* `nginx` – Nginx reverse‑proxy serving the Laravel public folder.

Start the stack:
```bash
docker compose up -d
```
> The `-d` flag runs containers in detached mode.

### 4.4 Running the Application
After the containers are up, run the following Artisan commands **inside the `app` container**:
```bash
# Install PHP dependencies
docker compose exec app composer install

# Install front‑end assets (optional, only if you need to compile CSS/JS)
docker compose exec app npm install

# Compile assets (development mode)
docker compose exec app npm run dev
```
Now you can access the application at **http://localhost** (or the host you configured in `.env`).

### 4.5 Database Migration & Seeding
```bash
# Run migrations (creates tables)
 docker compose exec app php artisan migrate

# Seed initial data (admin user, sample departments, etc.)
 docker compose exec app php artisan db:seed
```
The default admin credentials (created by `AdminUserSeeder`) are:
```
Email: admin@example.com
Password: password
```
> **Important:** Change the password after first login.

---

## Functionalities & Detailed Descriptions
### 5.1 User Management & Authentication
* **Roles:** `Admin`, `Account`, `Teacher`, `Student`.
* **Authentication:** Laravel Breeze (email/password) with session guard.
* **Authorization:** Gates defined in `AuthServiceProvider` restrict access to routes based on role.
* **Password Reset:** Uses Laravel’s built‑in token system.

### 5.2 Dashboard & Analytics
* **Charts:** Attendance trends, fee collection status, library usage.
* **Widgets:** Quick stats – total students, active fees, overdue payments.
* **Customizable:** Admin can add new widgets by extending the `DashboardController`.

### 5.3 Student Registration & Profile
* **CRUD** for student records (personal info, academic details, photo upload).
* **Bulk import** via CSV (handled by `StudentImport` service).
* **Profile page** shows attendance, fees, library loans, and exam results.

### 5.4 Attendance Management
* **Mark attendance** per class/section.
* **Automatic validation** – prevents duplicate entries for the same day.
* **Reports** – daily, monthly, and per‑subject attendance percentages.

### 5.5 Fee Collection & Accounting
* **Fee structures** – defined per department/semester.
* **Payment recording** – cash, bank transfer, or online gateway (stubbed).
* **Due reminders** – email notifications (queue‑based, uses Laravel Mail).
* **Financial reports** – total collected, pending, and overdue amounts.

### 5.6 Library Management
* **Catalog** – books, journals, digital media.
* **Issue/Return workflow** with automatic fine calculation.
* **Search** – by title, author, ISBN, or subject.
* **Inventory alerts** – low‑stock notifications.

### 5.7 Dormitory & Hostel Management
* **Room allocation** – based on gender, department, and availability.
* **Fee tracking** – monthly hostel fees integrated with the fee module.
* **Visitor log** – optional feature for security staff.

### 5.8 Examination & Result Processing
* **Exam creation** – define subjects, max marks, and weightage.
* **Result entry** – bulk entry via spreadsheet or manual UI.
* **Grade calculation** – configurable grading schema.
* **Transcript generation** – PDF export using `dompdf`.

### 5.9 Reporting & Export
* **CSV/Excel export** for any list view (students, fees, attendance, etc.).
* **PDF reports** – built with `barryvdh/laravel-dompdf`.
* **Scheduled jobs** – nightly backup and report generation (Laravel Scheduler).

---

## Functional Requirements (FR)
| FR‑ID | Description |
|-------|-------------|
| FR‑1 | The system shall allow users to **register**, **login**, and **logout** securely. |
| FR‑2 | Admin users shall be able to **create, read, update, delete** (CRUD) any entity (students, fees, books, etc.). |
| FR‑3 | Teachers shall have read‑only access to their assigned classes and be able to **record attendance**. |
| FR‑4 | The system shall generate **monthly fee statements** and allow payments to be recorded. |
| FR‑5 | Library staff shall manage **catalog entries**, **issue**, **return**, and **track fines**. |
| FR‑6 | Dormitory staff shall allocate rooms and track **hostel fee payments**. |
| FR‑7 | The examination module shall support **exam creation**, **result entry**, and **grade calculation**. |
| FR‑8 | All list views shall support **search**, **filter**, **pagination**, and **export** to CSV/Excel. |
| FR‑9 | The dashboard shall display **real‑time statistics** using charts. |
| FR‑10 | The system shall send **email notifications** for overdue fees and upcoming exams. |

---

## Non‑Functional Requirements (NFR)
| NFR‑ID | Category | Requirement |
|--------|----------|-------------|
| NFR‑1 | **Performance** | Page load time < 2 seconds for dashboards under 10 k records. |
| NFR‑2 | **Scalability** | Able to handle up to 5 000 concurrent users with horizontal scaling of the `app` container. |
| NFR‑3 | **Security** | Passwords stored with bcrypt, CSRF protection enabled, input validation on all forms. |
| NFR‑4 | **Reliability** | Automatic DB backups (daily) and graceful degradation if Redis is unavailable. |
| NFR‑5 | **Usability** | UI follows Bootstrap 5 guidelines; all forms have client‑side validation. |
| NFR‑6 | **Maintainability** | Code follows PSR‑12, unit tests ≥ 80 % coverage, CI pipeline (GitHub Actions) runs lint & tests on push. |
| NFR‑7 | **Portability** | Application runs on any Docker‑compatible host (Linux, macOS, Windows‑WSL). |
| NFR‑8 | **Documentation** | This SRS (`SUMMARY.md`) and inline code comments must be kept up‑to‑date. |

---

## System Requirements
### Hardware (Production)
* **CPU:** 2 vCPU (minimum), 4 vCPU recommended.
* **RAM:** 2 GB (minimum), 4 GB recommended.
* **Disk:** 20 GB SSD for OS, DB, and logs.
* **Network:** 1 Gbps Ethernet (or equivalent cloud bandwidth).

### Software
| Component | Minimum Version |
|-----------|-----------------|
| Docker Engine | 24.0 |
| Docker Compose | v2 |
| MySQL | 8.0 |
| PHP | 8.2 |
| Composer | 2.6 |
| Node.js | 20.x |
| Nginx | 1.24 |
| Redis (optional) | 7.0 |

---

## Deployment Options
1. **Docker Compose (development / small‑scale production)** – as described in the *Installation* section.
2. **Kubernetes** – create Helm chart from the `docker-compose.yml` (services → Deployments, ConfigMaps for `.env`).
3. **Traditional VM** – install PHP, Nginx, MySQL manually; copy the source code; run `php artisan migrate --seed`.

*For production, always enable HTTPS (use Let’s Encrypt) and set `APP_ENV=production` with `APP_DEBUG=false`.*

---

## Testing Strategy
* **Unit Tests** – located in `tests/` (run with `php artisan test`).
* **Feature Tests** – cover HTTP routes, authentication, and role‑based access.
* **Static Analysis** – `phpstan` and `laravel‑pint` for code style.
* **CI Pipeline** – GitHub Actions workflow runs lint, static analysis, and tests on every PR.

---

## Maintenance & Extensibility
* **Adding a new module** – create a new Laravel module (model, migration, controller, routes, Blade views) and register routes in `routes/web.php`.
* **Database versioning** – use Laravel migrations; never edit existing migration files after they are deployed.
* **Cache busting** – run `php artisan cache:clear` after any configuration change.
* **Backup strategy** – schedule `mysqldump` inside the `db` container and store backups on a mounted volume.

---

## FAQ & Troubleshooting
| Issue | Solution |
|-------|----------|
| **“SQLSTATE[HY000] [2002] Connection refused”** | Ensure the `db` container is running (`docker compose ps`). Verify `DB_HOST=db` in `.env`. |
| **“Class not found” after composer install** | Run `docker compose exec app composer dump‑autoload`. |
| **Views not updating after Blade changes** | Clear compiled views: `docker compose exec app php artisan view:clear`. |
| **Cache still showing old data** | Run `docker compose exec app php artisan cache:clear`. |
| **Permission denied on storage folder** | Inside container: `chown -R www-data:www-data storage bootstrap/cache`. |
| **Docker builds hanging** | Increase Docker memory allocation (Docker Desktop → Resources). |

---

## References & Further Reading
* **Laravel Documentation** – https://laravel.com/docs/10.x
* **Docker Compose Reference** – https://docs.docker.com/compose/
* **Bootstrap 5** – https://getbootstrap.com/docs/5.3/getting-started/introduction/
* **PHPUnit** – https://phpunit.de/
* **GitHub Actions** – https://docs.github.com/en/actions

---

*Prepared on **June 5, 2026**. This document is version‑controlled alongside the source code; any changes to the system architecture or requirements should be reflected here.*
