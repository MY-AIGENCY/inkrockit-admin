# Changelog

## [0.2.0] - 2025-11-15
### Added
- Enabled `/reports` base path for the Next.js dashboard, tightened typed routes, and wrapped filter controls with suspense to satisfy the `useSearchParams` requirement.
- Replaced the Express API's Prisma/Postgres mock with mysql2-powered queries hitting `users`, `requests`, `request_notes`, `user_jobs`, and `payment_history`, including new `/api/requests/recent`, `/api/jobs/summary`, and `/api/payments/recent` endpoints.
- Provisioned container env handling via `.env` (with `.env.example` for placeholders), composed services for PHP, API, Next.js, MySQL, and Nginx, and wired Docker/Nginx so `/`, `/api`, and `/reports` flow through the correct upstreams.
- Added .next standalone runtime, MySQL credentials, and changelog/docs updates to keep Docker builds reproducible and secrets externalized.

## [0.1.0] - 2025-11-15
### Added
- Bootstrap monorepo with Next.js dashboard, Express API, shared UI + type packages.
- Implemented REST endpoints for prospects, companies, sample requests, jobs, tasks, payments, activities, saved filters, and metrics.
- Added Tailwind-powered layouts, dashboards, entity list/detail pages, and fallback data for offline resilience.
- Authored Prisma schema + seed stub, Docker assets, runbook, and architecture documentation.
