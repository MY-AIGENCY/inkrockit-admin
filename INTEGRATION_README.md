# InkRockit Integrated Setup

This setup runs both the main PHP application and the Analytics Dashboard together, sharing the same MySQL database.

## Architecture

```
http://localhost:8080/
├── /                      → PHP Application (Kohana)
├── /admin                 → PHP Admin Panel
├── /reports               → Next.js Analytics Dashboard
└── /api                   → Express API (for analytics)
```

### Services

1. **MySQL Database** (`mysql`) - Port 3306
   - Shared by both PHP app and Analytics API
   - Database: `preprod`
   - Contains: users, requests, jobs, payments, etc.

2. **PHP-FPM** (`php`) - Port 9000
   - Kohana framework application
   - Connects to MySQL

3. **Analytics API** (`analytics_api`) - Port 4000
   - Express.js TypeScript API
   - Reads from MySQL preprod database
   - Provides REST endpoints for analytics dashboard

4. **Analytics Web** (`analytics_web`) - Port 3000
   - Next.js 14 dashboard
   - Charts and business intelligence
   - Fetches data from Analytics API

5. **Nginx** (`nginx`) - Port 8080
   - Main gateway
   - Routes `/` to PHP app
   - Proxies `/reports` to Next.js
   - Proxies `/api` to Express API

## Quick Start

```bash
# Build and start all services
docker-compose up -d --build

# View logs
docker-compose logs -f

# Access the applications
open http://localhost:8080          # PHP App
open http://localhost:8080/reports  # Analytics Dashboard
```

## URLs

- **Main App**: http://localhost:8080
- **Analytics Dashboard**: http://localhost:8080/reports
- **Analytics API**: http://localhost:8080/api
- **Direct Next.js** (dev): http://localhost:3000
- **Direct API** (dev): http://localhost:4000

## Database Connection

Both applications connect to the same MySQL database:

**Connection Details:**
- Host: `mysql` (within Docker network)
- Database: `preprod`
- User: `preprod_user`
- Password: `!1q2w3eZ`
- Port: 3306

**External Access:**
```bash
# Connect from host machine
mysql -h 127.0.0.1 -P 3306 -u preprod_user -p preprod
# Password: !1q2w3eZ
```

## Important Notes

### Analytics API & Database Schema

The Analytics API was originally designed for a PostgreSQL schema with Prisma ORM. It's now configured to connect to MySQL, but may need code updates to:

1. **Replace Prisma** with direct MySQL queries or a MySQL-compatible ORM
2. **Map to existing schema** - The preprod database has different table structures than the original Prisma schema
3. **Update queries** to work with existing tables: `users`, `requests`, `request_notes`, `user_jobs`, `payment_history`, etc.

### Next Steps for Analytics Integration

To fully integrate the analytics dashboard:

1. **Update API Code** - Modify `apps/api/src/` to query the actual MySQL tables
2. **Create Views/Adapters** - Map existing data to the analytics dashboard format
3. **Test Data Flow** - Ensure charts and metrics display real data

### Development Workflow

```bash
# Stop all services
docker-compose down

# Rebuild after code changes
docker-compose up -d --build

# View specific service logs
docker-compose logs -f analytics_api
docker-compose logs -f analytics_web
docker-compose logs -f php

# Restart specific service
docker-compose restart analytics_api
```

## Deployment to Staging

When ready to deploy to `staging.inkrockit.com/reports`:

1. **Update Nginx config** on server to proxy `/reports` to Next.js app
2. **Deploy Analytics services** alongside existing PHP app
3. **Configure environment variables** for production database connection
4. **Set up process manager** (PM2 or similar) for Node.js services

### Production Nginx Config Example

```nginx
# Add to existing staging.inkrockit.com config
location /reports {
    proxy_pass http://localhost:3000;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection 'upgrade';
    proxy_set_header Host $host;
    proxy_cache_bypass $http_upgrade;
}

location /api {
    proxy_pass http://localhost:4000;
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
}
```

## Troubleshooting

### Analytics Dashboard Not Loading

```bash
# Check if Next.js is running
docker-compose ps analytics_web

# View Next.js logs
docker-compose logs analytics_web

# Rebuild Next.js container
docker-compose up -d --build analytics_web
```

### API Errors

```bash
# Check API logs
docker-compose logs analytics_api

# Verify MySQL connection
docker-compose exec analytics_api sh
> npm run db:test  # If available
```

### Database Connection Issues

```bash
# Check MySQL is running
docker-compose ps mysql

# Test connection from PHP container
docker-compose exec php php -r "new PDO('mysql:host=mysql;dbname=preprod', 'preprod_user', '!1q2w3eZ');"

# Test connection from Analytics API container
docker-compose exec analytics_api sh
> mysql -h mysql -u preprod_user -p preprod
```

## File Structure

```
.
├── web/                           # PHP Application
│   ├── application/
│   └── index.php
├── apps/                          # Analytics Apps
│   ├── api/                       # Express API
│   └── web/                       # Next.js Dashboard
├── packages/                      # Shared TypeScript packages
│   ├── types/
│   └── ui/
├── config/
│   ├── nginx-integrated.conf      # Unified Nginx config
│   ├── nginx-local.conf           # PHP-only config
│   └── php-custom.ini
├── docker-compose.yml             # Unified services
├── Dockerfile.php                 # PHP container
└── infra/
    ├── Dockerfile.api             # Analytics API container
    └── Dockerfile.web             # Next.js container
```

## Current Status

- ✅ PHP App running
- ✅ MySQL database shared
- ✅ Next.js dashboard built
- ✅ Express API configured
- ⚠️  API needs code updates to query MySQL directly
- ⚠️  Dashboard may show placeholder data until API is updated

---

**Created**: 2025-11-15
**Last Updated**: 2025-11-15
