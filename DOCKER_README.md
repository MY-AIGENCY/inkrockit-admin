# Local Development with Docker

This Docker setup allows you to run the InkRockit staging site locally without affecting the production/staging servers.

## Prerequisites

- Docker Desktop installed and running
- Git (for version control)

## Quick Start

### 1. Start the Development Environment

```bash
# Build and start all containers
docker-compose up -d

# View logs (optional)
docker-compose logs -f
```

### 2. Access the Application

- **Application URL**: http://localhost:8080
- **Database**: Available on port 3306 (localhost:3306)

### 3. Stop the Environment

```bash
# Stop containers
docker-compose down

# Stop and remove volumes (WARNING: This deletes database data)
docker-compose down -v
```

## What's Included

- **MySQL 5.7**: Database server with preprod database
  - User: `preprod_user`
  - Password: `!1q2w3eZ`
  - Database: `preprod`

- **PHP 7.4-FPM**: PHP runtime with extensions:
  - PDO MySQL
  - GD (image processing)
  - OPcache

- **Nginx**: Web server on port 8080

## Database

The database is initialized with a basic schema from `ops/init-db.sql`. This includes:

- Basic table structures for users, requests, notes, etc.
- Test admin user (login: `admin`, password: `admin123`)

**Note**: For full functionality, you may need to import a complete database dump from the staging server.

## File Structure

```
.
├── docker-compose.yml          # Docker services configuration
├── Dockerfile.php              # Custom PHP image with extensions
├── config/
│   ├── nginx-local.conf        # Local nginx configuration
│   ├── php-custom.ini          # PHP settings
│   └── stack.md                # Production server info
├── ops/
│   └── init-db.sql             # Database initialization
└── web/                        # Application code
    ├── index.php               # Front controller
    └── application/            # Kohana framework app
```

## Troubleshooting

### Containers won't start

```bash
# Check Docker is running
docker ps

# View error logs
docker-compose logs
```

### Database connection errors

```bash
# Check if DB container is running
docker-compose ps

# Access MySQL directly
docker-compose exec db mysql -u preprod_user -p preprod
# Password: !1q2w3eZ
```

### Permission errors with files

```bash
# Fix file permissions
docker-compose exec php chown -R www-data:www-data /var/www/html
```

### Rebuild after changes

```bash
# Rebuild PHP container after Dockerfile changes
docker-compose build php
docker-compose up -d
```

## Important Notes

- **Local Only**: This setup is for local development only
- **Database**: The database in Docker is separate from staging/production
- **Safe Testing**: Changes made here do NOT affect the live staging server
- **File Uploads**: Files uploaded will be stored in `web/application/files/`

## Useful Commands

```bash
# View running containers
docker-compose ps

# Access PHP container shell
docker-compose exec php bash

# Access MySQL shell
docker-compose exec db mysql -u preprod_user -p preprod

# View real-time logs
docker-compose logs -f

# Restart a specific service
docker-compose restart nginx

# Check PHP version
docker-compose exec php php -v
```

## Next Steps

1. Start the containers: `docker-compose up -d`
2. Visit http://localhost:8080
3. Test the application
4. Make changes safely
5. When done: `docker-compose down`
