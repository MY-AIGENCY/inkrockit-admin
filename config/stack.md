# Stack Configuration: staging.inkrockit.com

## Server Details
- **Server:** IMAGE-TEAM (67.205.161.183)
- **Webroot:** `/var/www/staging.inkrockit.com`
- **Web Server:** Nginx
- **SSL:** Configured (HTTPS)

## Stack
- **Type:** PHP Web Application
- **Framework:** Kohana PHP Framework (MVC pattern)
- **Database:** None detected (likely shares with production or preprod)

## Runtime
- **PHP** (version TBD - check server)
- **MySQL/MariaDB** (if database configured)

## Framework Structure (Kohana)
- `application/bootstrap.php` - Application initialization
- `application/classes/Controller/` - MVC Controllers
- `application/classes/Model/` - Database models
- `application/config/` - Configuration files
- `index.php` - Front controller

## Features (Same as preprod/production)
- Admin panel (Account, Sales, Print management, User management)
- FedEx shipping integration
- Print/order management system
- User accounts and authentication
- File uploads for print orders

## Database
- **Name:** Not configured or shares external database
- **Note:** No database exported for this environment

## File Storage
- `application/files/fedex/` - Shipping labels (PNG format)
- `application/files/invoice/` - Invoice documents
- `application/files/upload/` - User uploaded designs
- `application/files/print/` - Print job files

## Dependencies
- Kohana PHP Framework
- FedEx Web Services
- MySQL database (external or shared)

## Notes
- Staging environment for inkrockit.com
- Same codebase as preprod.inkrockit.com
- Contains FedEx shipping label archive
- Backup code files present (.backup_YYYYMMDD_HHMM)
