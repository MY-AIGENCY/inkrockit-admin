# staging.inkrockit.com - Operations Guide

## Domain Information
- **Primary Domain:** staging.inkrockit.com
- **Aliases:** www.staging.inkrockit.com
- **Server:** IMAGE-TEAM droplet (67.205.161.183)
- **Webroot:** `/var/www/staging.inkrockit.com`
- **Environment:** Staging

## Current Status
- **SSL:** ✓ Configured (HTTPS)
- **Database:** Unknown (likely external or shared)
- **Estimated Size:** ~12KB code

## Stack
- PHP web application (Kohana Framework)
- MySQL database (configuration TBD)
- FedEx shipping API integration

## Deployment Instructions

### From This Repository

#### 1. Deploy Web Files
```bash
# On target server
rsync -avz sites/staging.inkrockit.com/web/ /var/www/staging.inkrockit.com/

# Set permissions
sudo chown -R www-data:www-data /var/www/staging.inkrockit.com
sudo chmod -R 755 /var/www/staging.inkrockit.com

# Make upload directories writable
sudo chmod -R 775 /var/www/staging.inkrockit.com/application/files/
```

#### 2. Configure Application
```bash
# Update database credentials in application/config/database.php
# Update API keys in application/config/ukieapi.php
```

#### 3. Apply Nginx Configuration
```bash
sudo cp sites/staging.inkrockit.com/config/nginx.conf /etc/nginx/sites-available/staging.inkrockit.com
sudo ln -s /etc/nginx/sites-available/staging.inkrockit.com /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## Maintenance

### Log Files
- Application logs: `application/logs/`
- Nginx access: `/var/log/nginx/access.log`
- Nginx errors: `/var/log/nginx/error.log`

### File Storage Monitoring
- FedEx labels accumulate over time
- Monitor `application/files/` directory size

## Notes
- Backup code files exist (Admin.php.backup_20251014_2152, etc.)
- Consider establishing regular database backup schedule
- Verify database connection configuration

## Contact
- Admin: don@imageteam.com
