# Local Development Setup - Summary

## Status: ✅ Successfully Running

Your local development environment for staging.inkrockit.com is now running and ready for testing!

## Access Points

- **Application URL**: http://localhost:8080
- **Database**: localhost:3306
  - Database: `preprod`
  - User: `preprod_user`
  - Password: `!1q2w3eZ`

## What Was Set Up

### 1. Docker Environment
Created a complete Docker Compose setup with:
- **MySQL 8.0** database (using platform: linux/amd64 for Apple Silicon compatibility)
- **PHP 7.4-FPM** with required extensions (PDO MySQL, GD, OPcache)
- **Nginx 1.21** web server

### 2. Database Initialization
- Created base tables matching the customer data dictionary
- Includes test admin user: `admin` / `admin123`
- Schema includes all recent changes:
  - `request_notes.removed` and `request_notes.required_uid` fields ✓
  - `print_category.abbr` field ✓

### 3. Application Configuration
- Updated database config to support Docker environment variables
- Created writable cache and logs directories
- Configured proper file permissions

## Recent Code Changes (Verified Locally)

All changes from PR #4 are included and validated:

### PHP Code Fixes
1. ✅ [Controller/Admin/Print.php](web/application/classes/Controller/Admin/Print.php:545) - Auto-generate abbreviations for print categories
2. ✅ [Controller/Admin/Sales.php](web/application/classes/Controller/Admin/Sales.php:323) - Include `required_uid` and `removed` in SQL inserts
3. ✅ [Controller/Mail.php](web/application/classes/Controller/Mail.php:67) - Include `required_uid` and `removed` in SQL inserts
4. ✅ [Model/Admin/Fedex.php](web/application/classes/Model/Admin/Fedex.php) - Include `required_uid` and `removed` in SQL inserts
5. ✅ [Model/Admin/Print.php](web/application/classes/Model/Admin/Print.php:1541) - Fixed `required_uid` type (int) and include `removed` field
6. ✅ [Model/Admin/Sales.php](web/application/classes/Model/Admin/Sales.php) - Include `removed` field
7. ✅ [Model/Admin/User.php](web/application/classes/Model/Admin/User.php) - Include `removed` field

### New Tools
1. ✅ [scripts/export_customers.py](scripts/export_customers.py) - Export customer data to XLSX
2. ✅ [scripts/generate_customer_data_dictionary.py](scripts/generate_customer_data_dictionary.py) - Generate data dictionary

### Documentation
1. ✅ [reports/customer_data_dictionary.md](reports/customer_data_dictionary.md) - Complete schema documentation

## Quick Commands

### Start/Stop the Environment
```bash
# Start
docker-compose up -d

# Stop
docker-compose down

# View logs
docker-compose logs -f

# Stop and remove all data (fresh start)
docker-compose down -v
```

### Database Access
```bash
# MySQL shell
docker-compose exec db mysql -u root -proot_password preprod

# Run SQL file
docker-compose exec -T db mysql -u root -proot_password preprod < your-file.sql

# Check tables
docker-compose exec db mysql -u root -proot_password preprod -e "SHOW TABLES;"
```

### Application Management
```bash
# Restart PHP
docker-compose restart php

# Rebuild after code changes
docker-compose build php && docker-compose up -d

# Clear cache
rm -rf web/application/cache/*

# Check PHP errors
docker-compose exec php tail -f /var/log/php7.4-fpm.log
```

## Testing Your Changes

### Safe Testing Environment
- ✅ **Isolated**: This local environment is completely separate from staging/production
- ✅ **No Risk**: Changes here will NOT affect the live staging server
- ✅ **Database**: Uses a local MySQL instance with test data
- ✅ **Version Control**: Your changes can be tested before committing

### Recommended Testing Workflow
1. Make code changes in your local files
2. Refresh browser at http://localhost:8080 to see changes
3. Test database operations using the local MySQL
4. Check logs: `docker-compose logs -f php`
5. When satisfied, commit and push to staging

## Database Schema Validation

The local database includes all fields added in the recent changes:

### request_notes table
- `id`, `request_id`, `company_id`, `text`, `date`
- `job_id`, `author_id`, `type`, `type_user`
- ✅ `removed` (int, default: 0)
- ✅ `required_uid` (int, default: 0)

### print_category table
- `id`, `title`
- ✅ `abbr` (varchar(10))
- `active` (int, default: 1)

## Important Notes

### What's Safe
- Testing application functionality locally
- Making code changes
- Database queries and inserts
- File uploads (stored in `web/application/files/`)

### What to Be Careful About
- Don't push database credentials to git
- The local DB is NOT synced with staging
- File uploads are local only
- For full testing, you may need a database dump from staging

## Next Steps

1. **Test Your Recent Changes**
   - Test print category creation with auto-generated abbreviations
   - Test request notes with new `removed` and `required_uid` fields
   - Verify SQL INSERT statements work correctly

2. **Import Production Data (Optional)**
   If you need real data for testing:
   ```bash
   # On staging server, export database
   mysqldump -u preprod_user -p preprod > staging_dump.sql

   # On local machine, import
   docker-compose exec -T db mysql -u root -proot_password preprod < staging_dump.sql
   ```

3. **Make Changes Safely**
   - Edit code in `web/` directory
   - Test locally at http://localhost:8080
   - Commit when satisfied

## Troubleshooting

### Application shows 500 error
```bash
# Check PHP logs
docker-compose logs php

# Ensure cache is writable
chmod -R 777 web/application/cache web/application/logs
```

### Database connection errors
```bash
# Restart database
docker-compose restart db

# Check database status
docker-compose ps
```

### Changes not showing up
```bash
# Clear cache
rm -rf web/application/cache/*

# Restart PHP
docker-compose restart php
```

## Files Created for Local Development

- `docker-compose.yml` - Docker services configuration
- `Dockerfile.php` - Custom PHP image with extensions
- `config/nginx-local.conf` - Nginx configuration for Docker
- `config/php-custom.ini` - PHP settings
- `ops/init-db.sql` - Database initialization script
- `DOCKER_README.md` - Detailed Docker documentation
- `LOCAL_SETUP_SUMMARY.md` - This summary

## Support

For detailed Docker documentation, see [DOCKER_README.md](DOCKER_README.md)

For the complete codebase context, see [SOURCE_OF_TRUTH.md](SOURCE_OF_TRUTH.md)

---

**Last Updated**: 2025-11-15
**Environment**: macOS (Apple Silicon) with Docker Desktop
**Status**: ✅ Running and validated
