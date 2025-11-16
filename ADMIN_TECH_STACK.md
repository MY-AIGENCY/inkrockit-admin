# InkRockit Admin Tech Stack

## Overview
The InkRockit admin interface (`staging.inkrockit.com/admin`) is a legacy PHP application built with the Kohana MVC framework for managing print orders, customer requests, and business operations.

**Note:** This document describes the production tech stack. Docker is used only for local development and was added recently alongside the new reports section.

## Production Environment

### Server
- **DigitalOcean Droplet**: IMAGE-TEAM (67.205.161.183)
- **OS**: Linux
- **Webroot**: `/var/www/staging.inkrockit.com`
- **Domain**: https://staging.inkrockit.com

## Core Technologies

### Backend
- **PHP 7.4**
  - Server API: FPM/FastCGI
  - Custom configuration: 128MB upload limit, 256MB memory limit, 240s execution timeout
  - Extensions: PDO, PDO MySQL, MySQLi, GD, OPcache

- **Kohana Framework** (v3.x)
  - Lightweight PHP MVC framework
  - Modular architecture with custom controllers, models, and views
  - Location: `application/`, `kernel/`

### Database
- **MySQL** (on same DigitalOcean droplet)
  - Database: `preprod`
  - User: `preprod_user`
  - Shared with preprod.inkrockit.com
  - Key tables:
    - `users` - Customer accounts (19,496 records)
    - `users_company` - Company profiles (19,257 records)
    - `requests` - Sample/print requests (17,922 records)
    - `request_notes` - Activity notes (31,553 records)
    - `user_jobs` - Production jobs (4,414 records)
    - `payment_history` - Payment transactions (9,559 records)
    - `credit_card_billing` / `credit_card_shipping` - Payment details
    - `print_*` tables - Print specifications and pricing

### Web Server
- **Nginx**
  - Configuration: `/etc/nginx/sites-available/staging.inkrockit.com`
  - SSL enabled (HTTPS)
  - PHP-FPM integration via FastCGI
  - Static assets: CSS, JS, images from `application/views/`
  - Client max body size: 128MB
  - Serves both staging.inkrockit.com and the legacy admin interface

## Application Architecture

### Production Structure
```
/var/www/staging.inkrockit.com/
├── index.php           # Entry point
├── application/        # Application code
│   ├── classes/
│   │   └── Controller/
│   │       └── Admin/  # Admin controllers
│   │           ├── Index.php
│   │           ├── Sales.php
│   │           ├── Users.php
│   │           ├── Print.php
│   │           └── ...
│   ├── config/         # Configuration files
│   │   ├── database.php
│   │   └── ...
│   ├── views/          # Templates & UI
│   │   ├── css/
│   │   ├── js/
│   │   └── images/
│   └── files/          # User uploads
└── kernel/             # Kohana framework
    ├── system/
    ├── modules/
    └── vendor/
```

### Key Admin Features
- **Sales Management** (`Controller/Admin/Sales.php`)
  - Customer request processing
  - Order note tracking
  - Payment history management

- **User Management** (`Controller/Admin/Users.php`)
  - Customer account administration
  - Company profile management

- **Print Operations** (`Controller/Admin/Print.php`)
  - Print category management
  - Product specifications
  - Pricing configuration

## Production Deployment

### Server Configuration
- **PHP-FPM**: Running on the DigitalOcean droplet
- **Nginx**: Configured at `/etc/nginx/sites-available/staging.inkrockit.com`
- **MySQL**: Local database server on the same droplet
- **File Storage**: Uploads in `application/files/`

### Database Connection
- **Host**: 127.0.0.1 (localhost)
- **Database**: preprod
- **User**: preprod_user
- **Connection**: via PHP PDO/MySQLi

### Access Points
- **Production**: https://staging.inkrockit.com/admin
- **SSH Access**: `ssh root@67.205.161.183` (requires SSH key)

## Local Development (Docker)

**Note:** Docker setup is for local development only and was added recently.

### Docker Configuration
- **Container**: `inkrockit_php` (PHP 7.4-FPM)
- **Services**: MySQL, Nginx, PHP
- **Compose File**: `docker-compose.yml` or `docker-compose.php.yml`
- **Local URL**: http://localhost:8080/admin

### Local Setup
```bash
# Start Docker stack
docker-compose up -d

# Access local admin
open http://localhost:8080/admin
```

## Dependencies

### Production System Requirements
- PHP 7.4+ with extensions: PDO, MySQLi, GD, OPcache
- MySQL (any recent version)
- Nginx with FastCGI support
- Minimum 256MB PHP memory limit
- 128MB upload limit for customer files

### PHP Libraries
- Kohana Framework (bundled in `kernel/`)
- Custom classes and helpers in `application/classes/`

### Production File Permissions
- Web root: `/var/www/staging.inkrockit.com`
- User/Group: Configured via Nginx/PHP-FPM
- Upload directory: `application/files/`
- Must be writable by PHP-FPM process

## Data Flow (Production)

1. **HTTPS Request** → Nginx receives request at staging.inkrockit.com/admin
2. **SSL Termination** → Nginx handles SSL certificate
3. **FastCGI** → Nginx forwards PHP requests to PHP-FPM socket/port
4. **Kohana Routing** → index.php routes to appropriate Admin controller
5. **Business Logic** → Controller processes request, queries MySQL (127.0.0.1)
6. **View Rendering** → Kohana view templates generate HTML
7. **Response** → Nginx serves response back through HTTPS

## Security (Production)
- **SSL/TLS**: HTTPS enabled via Nginx
- **Authentication**: MD5 password hashing (legacy - should be upgraded)
- **Session Management**: PHP sessions
- **Database**: Local connection to MySQL on same server
- **File Uploads**: Stored in `application/files/` with appropriate permissions

## Performance (Production)
- **OPcache**: Enabled for PHP bytecode caching
- **FastCGI**: Optimized buffers and timeouts
- **Read Timeout**: 240 seconds for long-running operations
- **Static Assets**: Served directly by Nginx (not through PHP)

## Integration Points
- **Shared Database**: `preprod` database used by multiple InkRockit sites
  - staging.inkrockit.com
  - preprod.inkrockit.com
- **File Storage**: Customer uploads in `application/files/`
- **Static Assets**: CSS, JS, images in `application/views/`

## Repository Structure
```
staging.inkrockit.com/           # Git repository
├── web/                          # Production code (maps to /var/www/staging.inkrockit.com)
│   ├── index.php
│   ├── application/
│   └── kernel/
├── docker-compose.yml            # Local development only
├── Dockerfile.php                # Local development only
└── apps/                         # New reports section (separate stack)
```
