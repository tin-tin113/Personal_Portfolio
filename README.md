# Personal Portfolio Website & Private Admin CMS

A production-quality, responsive personal portfolio website and private Admin Content Management System (CMS) built with **Plain Core PHP 8.x**, **MySQL/MariaDB (PDO)**, **Bootstrap 5**, and **Vanilla JavaScript**. Designed specifically for local execution in **Laragon** and eventual deployment to shared hosting (e.g. Hostinger).

---

## 🌟 Identity & Positioning
- **Title**: AI-Assisted Full-Stack Developer | Quality Assurance | Administrative Support
- **Key Strengths**: Full-stack PHP/MySQL development, software QA and testing methodologies, AI-accelerated development workflows, database architecture, and technical administrative support.

---

## 🛠️ Technology Stack
- **Backend**: Plain/Core PHP 8.x (No frameworks, clean modular MVC-inspired organization)
- **Database**: MySQL / MariaDB via PDO (`ERRMODE_EXCEPTION`, `FETCH_ASSOC`, emulated prepares disabled)
- **Frontend**: HTML5, CSS3, Bootstrap 5, Bootstrap Icons, Vanilla JavaScript
- **Security**: `password_hash()` / `password_verify()`, session regeneration, CSRF tokens, `htmlspecialchars()` XSS escaping, `finfo_file` MIME validation, random file renaming, and `.htaccess` upload execution blocking.

---

## 🚀 Laragon Local Installation Guide

### Prerequisites
- [Laragon](https://laragon.org/) with PHP 8.x and MySQL enabled.

### Quick Setup Steps

1. **Place Project Files**:
   Copy the project directory to your Laragon `www` directory:
   ```text
   C:\laragon\www\Personal_Portfolio
   ```

2. **Start Laragon Services**:
   - Open Laragon Control Panel.
   - Click **Start All** (Apache & MySQL).

3. **Database Initialization**:
   - Option A (Automatic): Simply open the web app! The PDO connection engine automatically detects if the database `personal_portfolio` exists and creates it if needed.
   - Option B (Manual / phpMyAdmin):
     1. Open phpMyAdmin or HeidiSQL in Laragon.
     2. Import `database/schema.sql` to build the database tables.
     3. Import `database/seeds.sql` to populate sample portfolio content.

4. **Access the Application**:
   - **Public Portfolio**: [http://localhost/Personal_Portfolio/](http://localhost/Personal_Portfolio/)
   - **Admin CMS**: [http://localhost/Personal_Portfolio/admin/](http://localhost/Personal_Portfolio/admin/)

---

## 🔑 Default Admin Credentials

> [!IMPORTANT]
> Change the default admin password immediately after your first login via **Admin Panel &rarr; Change Password**.

- **Admin Login URL**: `/admin/login.php`
- **Username**: `admin` (or `admin@example.com`)
- **Password**: `AdminPassword123!`

---

## 📂 Project Architecture

```text
Personal_Portfolio/
├── .htaccess                   # Clean URL rewrites & security headers
├── index.php                   # Public Homepage (Hero, About, Skills, Projects, Certs, Timeline, Contact)
├── projects.php                # Public Projects Showcase Gallery
├── project.php                 # Public Detailed Project View (with Screenshots gallery)
├── certifications.php          # Public Certifications & Credentials Showcase
├── resume.php                  # Resume PDF Preview & Download Handler
├── contact.php                 # Contact Form Submission Handler with Honeypot anti-spam
├── config/
│   ├── app.php                 # Application environment constants & upload limits
│   └── database.php            # PDO Database Connection Manager
├── includes/
│   ├── auth.php                # Authentication & Session Throttle Manager
│   ├── csrf.php                # CSRF Token Generation & Verification
│   ├── functions.php           # Security Helpers (e(), slugify, flash messages)
│   ├── upload.php              # Multi-layer Secure File Upload Engine
│   ├── header.php              # Public Header Template
│   ├── navbar.php              # Public Navbar Component
│   └── footer.php              # Public Footer Component
├── admin/
│   ├── .htaccess               # Admin Security Rules
│   ├── index.php               # Dashboard Overview & System Statistics
│   ├── login.php               # Admin Login Form
│   ├── logout.php              # Admin Logout Handler
│   ├── profile.php             # Edit Bio, Avatar, and Resume PDF Upload
│   ├── projects.php            # Projects Library & Actions
│   ├── project-edit.php        # Create/Edit Project + Screenshot Uploads
│   ├── certifications.php      # Certifications List
│   ├── certification-edit.php  # Create/Edit Certification
│   ├── licenses.php            # Licenses & Credentials Manager
│   ├── license-edit.php        # Create/Edit License
│   ├── education.php           # Academic History Manager
│   ├── education-edit.php      # Create/Edit Education
│   ├── experience.php          # Work Timeline Manager
│   ├── experience-edit.php     # Create/Edit Work Experience
│   ├── skills.php              # Skills & Skill Categories Manager
│   ├── links.php               # Social & External Links Manager
│   ├── media.php               # Central Media Asset Library
│   ├── site-settings.php       # Global Site Title, Accent Color, SEO Settings
│   ├── change-password.php     # Secure Admin Password Changer
│   └── includes/               # Admin Templates (admin-header, admin-footer)
├── assets/
│   ├── css/                    # Custom Stylesheets (style.css & admin.css)
│   └── js/                     # Custom JavaScript (main.js & admin.js)
├── database/
│   ├── schema.sql              # Complete Database Schema SQL
│   └── seeds.sql               # Initial Seed Data with Placeholder Markers
├── uploads/
│   ├── .htaccess               # Security: Script execution disabled (`php_flag engine off`)
│   ├── avatars/
│   ├── projects/
│   ├── certs/
│   ├── licenses/
│   ├── resume/
│   └── media/
└── error/                      # Custom Error Pages (403.php, 404.php, 500.php)
```

---

## 🔒 Security Architecture Highlights

1. **SQL Injection Evasion**: All SQL queries execute via PDO prepared statements (`PDO::ATTR_EMULATE_PREPARES => false`).
2. **XSS Protection**: Dynamic output uses the `e()` helper wrapping `htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')`.
3. **CSRF Protection**: All state-changing POST forms require valid cryptographically random session tokens (`csrf_field()` / `require_csrf_token()`).
4. **File Upload Hardening**:
   - `finfo_file` server-side MIME type verification.
   - Discards original user filename; generates cryptographically random filenames (`bin2hex(random_bytes(16))`).
   - Root `/uploads/.htaccess` file explicitly disables PHP execution (`Engine Off`, denying `.php`, `.phtml`, `.exe`, etc.).
5. **Brute Force Lockout**: Throttles login attempts after 5 consecutive failures for 15 minutes.

---

## 🌐 Production Shared Hosting Deployment Notes (e.g. Hostinger)

1. Upload files to your host's `public_html` directory.
2. Update database credentials in `config/database.php` (`host`, `dbname`, `username`, `password`).
3. Set `define('APP_ENV', 'production');` in `config/app.php` to hide detailed PHP traces.
4. Ensure the `uploads/` directory has `0755` permissions.
