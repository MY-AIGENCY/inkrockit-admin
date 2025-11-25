# staging.inkrockit.com Developer Guide

> **Living Document** - Last updated: 2025-11-25
> 
> This document contains everything needed to develop on this codebase.

---

## Quick Reference

| Item | Value |
|------|-------|
| **Live URL** | https://staging.inkrockit.com |
| **Admin Panel** | https://staging.inkrockit.com/admin |
| **Also accessible at** | https://inkrockit.com/admin (proxied) |
| **Server IP** | 67.205.161.183 (IMAGE-TEAM droplet) |
| **GitHub Repo** | https://github.com/MY-AIGENCY/staging.inkrockit.com |
| **Server Path** | `/var/www/staging.inkrockit.com` |

---

## Tech Stack

### Backend
- **Framework**: Kohana 3.3 (PHP MVC)
- **Runtime**: PHP 8.3
- **Web Server**: Nginx 1.18

### Database
- **Engine**: MySQL 8.0
- **Database Name**: `preprod`
- **Host**: `localhost`
- **Note**: SHARED with inkrockit.com and new.inkrockit.com

### Frontend (Admin)
- **Templating**: PHP views
- **JavaScript**: jQuery, custom admin scripts
- **CSS**: Custom stylesheets

---

## Project Structure

```
staging.inkrockit.com/
├── web/
│   └── application/
│       ├── classes/
│       │   ├── Controller/
│       │   │   └── Admin/           # Admin controllers
│       │   │       ├── Sales.php    # Sales/requests management
│       │   │       └── ...
│       │   └── Model/
│       │       └── Admin/           # Admin models
│       │           ├── Sales.php    # Sales data model
│       │           ├── Helcim.php   # Payment processing
│       │           └── ...
│       ├── views/
│       │   ├── admin/               # Admin panel views
│       │   │   └── sales/           # Sales views
│       │   ├── css/                 # Stylesheets
│       │   └── js/
│       │       └── admin/           # Admin JavaScript
│       │           └── sales.init.js
│       └── config/
│           └── helcim.php           # Helcim configuration
├── SOURCE_OF_TRUTH.md               # Dashboard build spec
├── CLAUDE.md                        # Agent instructions
└── DEVELOPER.md                     # This file
```

---

## Database Schema

### Record Counts (as of 2025-11-25)
| Table | Records |
|-------|---------|
| `users` | 19,502 |
| `users_company` | 19,261 |
| `requests` | 17,939 |
| `request_notes` | 34,461 |

### Known Data Quality Issues
| Issue | Count | Notes |
|-------|-------|-------|
| Duplicate emails (sample@email.tst) | 229 | Test data from "Acunetix" |
| Duplicate internal emails | ~150 | mwolpert, dtraub, ctucker, don@ |
| Users with company_id = 0 | 16 | Orphaned records |
| Requests with company_id = 0 | 11 | Orphaned records |
| Duplicate company names | Many | "test", "Self", "None", "n/a" |

### Critical Tables

#### `users` - Customer/Lead Records
```sql
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `login` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `email_alt` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `group_id` int NOT NULL,
  `user_abbr` varchar(10) NOT NULL,
  `company_id` varchar(255) NOT NULL,
  `country` varchar(100) NOT NULL,
  `street` varchar(255) NOT NULL,
  `street2` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `zipcode` varchar(20) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `phone_ext` varchar(10) NOT NULL,
  `phone_type` varchar(10) NOT NULL,
  `position` varchar(255) NOT NULL,
  `industry` varchar(255) NOT NULL,
  `fax` varchar(100) NOT NULL,
  `admin_comment` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);
```

#### `users_company` - Company Records
```sql
CREATE TABLE `users_company` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company` varchar(255) NOT NULL,
  `main_uid` int NOT NULL,
  `abbr` varchar(10) NOT NULL,
  `duplicate` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
);
```

#### `requests` - Sample Pack Requests
```sql
CREATE TABLE `requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `job_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `company_id` int NOT NULL,
  `request_date` date DEFAULT NULL,
  `industry` text,
  `industry_send` text NOT NULL,
  `complete_address` text,
  `user_ip` text,
  `status` int NOT NULL,
  PRIMARY KEY (`id`)
);
```

---

## Admin Panel Features

### Sales/Requests Management
- **URL**: `/admin/sales`
- **Controller**: `Controller/Admin/Sales.php`
- **Model**: `Model/Admin/Sales.php`
- **View**: `views/admin/sales/`

### Key Functionality
- View all sample pack requests
- Search and filter requests
- Add transactions (card, cash, check, misc)
- Process payments via Helcim
- Manage request status

---

## Payment Processing (Helcim)

### Configuration
File: `web/application/config/helcim.php`

### API Wrapper
File: `web/application/classes/Model/Admin/Helcim.php`

### Endpoints
| Action | Method |
|--------|--------|
| Process Purchase | `Helcim::purchase()` |
| Process Refund | `Helcim::refund()` |
| Process Preauth | `Helcim::preauth()` |
| Capture Preauth | `Helcim::capture()` |

### Environment Variable
```
HELCIM_API_TOKEN=your_token_here
```

---

## Server Access

### SSH
```bash
ssh root@67.205.161.183
cd /var/www/staging.inkrockit.com
```

### Nginx Config
```
/etc/nginx/sites-available/staging.inkrockit.com
```

### PHP Logs
```bash
tail -f /var/log/nginx/staging.inkrockit.com-error.log
```

---

## Common Tasks

### Viewing Admin Logs
```bash
ssh root@67.205.161.183
tail -f /var/log/nginx/staging.inkrockit.com-error.log
```

### Database Access
```bash
ssh root@67.205.161.183
mysql -u root preprod
```

### Backup Database
```bash
ssh root@67.205.161.183
mysqldump -u root preprod > /tmp/preprod_backup_$(date +%Y%m%d).sql
```

---

## Related Systems

### new.inkrockit.com (DO NOT MODIFY HERE)
- **Purpose**: Customer-facing React site
- **Repo**: https://github.com/dtraub1/new.inkrockit.com
- **Shares**: Same `preprod` database

### inkrockit.com/admin
- Proxied to this site's `/admin`
- Same codebase, different entry point

---

## Git Safety Protocol

Before making any code changes:
1. Run `git status` to check for uncommitted changes
2. Run `git log --oneline -3` to see recent commits
3. If uncommitted changes exist, STOP and ask before proceeding

See `CLAUDE.md` for full agent instructions.

