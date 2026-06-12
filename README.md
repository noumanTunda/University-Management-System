<div align="center">
  <h1>🎓 OSUMS</h1>
  <p><strong>Open Source University Management System</strong></p>
  <p>
    <a href="https://github.com/noumanTunda/University-Management-System">
      <img src="https://img.shields.io/badge/Laravel-5.2-red.svg" alt="Laravel 5.2">
    </a>
    <a href="https://github.com/noumanTunda/University-Management-System">
      <img src="https://img.shields.io/badge/PHP-7.4-blue.svg" alt="PHP 7.4">
    </a>
    <a href="https://github.com/noumanTunda/University-Management-System">
      <img src="https://img.shields.io/badge/MySQL-8.0-green.svg" alt="MySQL 8.0">
    </a>
    <a href="https://github.com/noumanTunda/University-Management-System">
      <img src="https://img.shields.io/badge/License-MIT-brightgreen.svg" alt="License MIT">
    </a>
  </p>
</div>

---

## 🚀 Quick Start

```bash
# 1. Clone the repository
git clone https://github.com/noumanTunda/University-Management-System.git
cd University-Management-System

# 2. Start Docker containers
docker compose up -d

# 3. Install dependencies
docker compose exec app composer install

# 4. Configure environment
cp .env.example .env
docker compose exec app php artisan key:generate

# 5. Run migrations & seed data
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed

# 6. Open in browser
open http://localhost:8080
```

---

## 📋 About

**OSUMS** is a full-featured university management system built on Laravel 5.2. It follows **Tanzania TCU grading standards**, integrates with **GePG (Government e-Payment Gateway)** for fee collections, and includes a **double-entry accounting system** with full audit trail.

### Key Features

| Module | Description |
|--------|-------------|
| **Student Management** | Admission, bulk CSV import, auto-user creation, course assignment |
| **Semester Registration** | Batch-based registration with academic year validation |
| **CA/UE Assessment** | TCU-compliant grading with user-definable components; templates merged into plans |
| **GePG Payments** | 12-digit control numbers, partial payments, student self-service, accountant delete/edit |
| **Enterprise Accounting** | Chart of Accounts, double-entry journal, trial balance, fee invoices |
| **Teacher Subject Filtering** | Teachers see only their assigned subjects per academic year |
| **Student Portal** | Login with student ID, view results/attendance/fees/library |
| **RBAC** | Admin, HOD, Teacher, Accountant, Student roles with middleware gates |
| **Curriculum Builder** | Year × Semester subject matrix with credit tracking |
| **Library** | Book catalog, issue/return, borrowing history |
| **Email** | MailHog integration for development email testing |

---

## 🐳 Docker Services

| Service | Container | Port |
|---------|-----------|------|
| **App** | PHP 7.4-FPM (Laravel 5.2) | `:8080` |
| **Database** | MySQL 8.0 | `:3306` |
| **MailHog** | Email testing | SMTP `:1025`, UI `:8025` |

---

## 🔑 Default Logins

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `!Password` |
| Teacher | `teacher` | `!Password` |
| Accountant | `accountant` | `!Password` |
| HeadOfDepartment | `hodcse` | `!Password` |
| Student | Student ID (e.g. `T21-03-12111`) | Last name (e.g. `Doe`) |

---

## 🧩 System Structure

```
app/
├── Http/
│   ├── Controllers/     # All controllers
│   ├── Middleware/       # admin, hod, teacher, account, student middleware
│   └── routes.php        # Web routes
├── Models/               # Eloquent models
├── Providers/            # AuthServiceProvider (gates)
database/
├── migrations/           # ~20 migration files
└── seeds/                # Database seeders
resources/
├── views/                # Blade templates (Gentelella theme)
└── assets/               # CSS, JS, images
docker-compose.yml        # Docker configuration
```

---

## 📚 Documentation

Full documentation is available in [`SUMMARY.md`](SUMMARY.md).

---

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.

---

## 📬 Contact

Project Link: [https://github.com/noumanTunda/University-Management-System](https://github.com/noumanTunda/University-Management-System)

---

<p align="center">Built with ❤️ using Laravel 5.2</p>
