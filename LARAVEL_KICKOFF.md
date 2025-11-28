# InkRockIt Admin Panel - Laravel 11 Migration

> **Repository:** `inkrockit-admin`
> **GitHub:** https://github.com/MY-AIGENCY/inkrockit-admin
> **Mission:** Migrate Kohana 3.3 admin panel to Laravel 11

---

## Your Assignment

You are the Development Agent responsible for building the new Laravel 11 admin panel for InkRockIt. The legacy Kohana 3.3 application will continue running alongside your new Laravel code until migration is complete.

### Immediate Next Steps

1. **Read `DEVELOPER.md`** - Contains current tech stack and database schema
2. **Read `backups/DATABASE_SCHEMA_20251127.md`** - Full database documentation
3. **Initialize Laravel 11 project** in a new `laravel/` subdirectory
4. **Connect to existing database** - The `preprod` MySQL database is your data source

---

## Project Context

### What Exists Today
- **Kohana 3.3** admin panel at `web/application/`
- **MySQL database** named `preprod` with ~150,000 records
- **Helcim payment integration** for credit card processing
- **~20,000 users** and **~18,000 requests**

### What You're Building
- **Laravel 11** admin panel with Breeze authentication
- **Eloquent models** for existing database tables
- **Modern admin UI** replacing legacy PHP views
- **Bcrypt password migration** from MD5 hashes

### Constraints
- **DO NOT modify the `web/` directory** - Kohana must keep running
- **DO NOT drop or alter database tables** - Read-only initially
- **DO NOT touch payment processing yet** - That comes in Phase 2

---

## Directory Structure

```
inkrockit-admin/
├── web/                      # LEGACY - Kohana 3.3 (DO NOT MODIFY)
│   └── application/
│       ├── classes/
│       │   ├── Controller/Admin/
│       │   └── Model/Admin/
│       └── views/admin/
├── laravel/                  # NEW - Create Laravel 11 here
│   ├── app/
│   │   ├── Models/           # Eloquent models
│   │   ├── Http/Controllers/
│   │   └── ...
│   ├── resources/views/
│   ├── routes/
│   └── ...
├── backups/                  # Database backups
│   ├── DATABASE_SCHEMA_20251127.md
│   └── preprod_backup_20251127.sql.gz
├── DEVELOPER.md              # Tech documentation
├── SOURCE_OF_TRUTH.md        # Dashboard spec
├── CLAUDE.md                 # Operational instructions
└── LARAVEL_KICKOFF.md        # This file
```

---

## Database Connection

### Credentials
The Laravel app should connect to the same database as Kohana:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=preprod
DB_USERNAME=root
DB_PASSWORD=
```

**Note:** On the production server (67.205.161.183), MySQL allows root access without password from localhost. For local development, you may need to configure credentials appropriately.

### Priority Eloquent Models

Create models for these tables first:

| Table | Model | Records | Priority |
|-------|-------|---------|----------|
| `users` | `User` | ~18,900 | HIGH - Authentication |
| `users_company` | `Company` | ~18,700 | HIGH - Relationships |
| `requests` | `Request` | ~18,300 | HIGH - Core feature |
| `request_notes` | `RequestNote` | ~31,500 | MEDIUM |
| `payment_history` | `PaymentHistory` | ~9,200 | MEDIUM |
| `user_jobs` | `Job` | ~4,400 | MEDIUM |
| `credit_card` | `CreditCard` | ~2,000 | LOW - PCI concerns |

---

## Authentication Migration Path

### Current State (Kohana)
- Passwords are **MD5 hashed** (insecure)
- Admin users have `group_id = 2` (staff) or `group_id = 6` (admin)
- Session-based authentication

### Target State (Laravel)
- **bcrypt** password hashing
- Laravel Breeze or Jetstream
- Role-based permissions

### Migration Strategy
```php
// During login, if user authenticates with MD5:
// 1. Verify MD5 hash
// 2. Rehash password with bcrypt
// 3. Update database
// 4. Log user in

if (md5($password) === $user->password) {
    $user->password = bcrypt($password);
    $user->save();
    Auth::login($user);
}
```

---

## Phase 1 Deliverables

1. **Laravel 11 project initialized** with Breeze
2. **Database connection verified** to `preprod`
3. **Eloquent models created** for User, Company, Request
4. **Admin authentication working** with MD5 to bcrypt migration
5. **Basic dashboard** showing request counts

### Definition of Done
- [ ] `laravel/` directory contains working Laravel 11 app
- [ ] Can log in as existing admin user
- [ ] Dashboard shows live data from `preprod` database
- [ ] No modifications to `web/` directory
- [ ] No database schema changes

---

## Git Workflow

### Branch Strategy
```
main              # Production-ready code
├── develop       # Integration branch
│   ├── feature/* # Feature branches
```

### Before Any Changes
```bash
git status                    # Check what changed
git log --oneline -3          # See recent commits
```

### Commit Message Format
```
feat: add User Eloquent model with relationships
fix: correct bcrypt migration logic
docs: update DEVELOPER.md with Laravel setup
```

---

## Server Information

| Item | Value |
|------|-------|
| **Server** | IMAGE-TEAM (DigitalOcean) |
| **IP** | 67.205.161.183 |
| **SSH** | `ssh root@67.205.161.183` |
| **Web Root** | `/var/www/staging.inkrockit.com` |
| **Database** | `preprod` on localhost |
| **PHP** | 8.3 |
| **MySQL** | 8.0 |

---

## Key Files to Reference

| File | Purpose |
|------|---------|
| `DEVELOPER.md` | Full tech stack documentation |
| `SOURCE_OF_TRUTH.md` | Dashboard UI specification |
| `backups/DATABASE_SCHEMA_20251127.md` | Complete database schema |
| `web/application/classes/Model/Admin/` | Legacy model logic to reference |
| `web/application/config/helcim.php` | Payment gateway config |

---

## Prohibitions

- Modify anything in `web/` directory
- Drop or alter database tables
- Delete or rename existing columns
- Touch payment processing (Phase 2)
- Deploy to production without review

---

## Questions?

If you need clarification on:
- **Infrastructure** - Ask the Infra-Ops agent
- **Database schema** - Reference `backups/DATABASE_SCHEMA_20251127.md`
- **Legacy code behavior** - Read `web/application/classes/`
- **Business requirements** - Reference `SOURCE_OF_TRUTH.md`

---

## Quick Start

```bash
# 1. Navigate to repo root
cd /path/to/inkrockit-admin

# 2. Create Laravel project
composer create-project laravel/laravel laravel

# 3. Install Breeze
cd laravel
composer require laravel/breeze --dev
php artisan breeze:install blade

# 4. Configure database in .env
# 5. Create Eloquent models
# 6. Build authentication with MD5 migration
```

---

## Estimated Effort

Based on prior analysis:
- **Total migration:** ~720 developer hours
- **Phase 1 (Foundation):** ~160 hours
- **Timeline:** 10-14 weeks across 4 phases

This kickoff document covers Phase 1. Subsequent phases will address:
- Phase 2: Payment processing migration
- Phase 3: Full feature parity
- Phase 4: Cutover and deprecation of Kohana
