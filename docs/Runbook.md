# Runbook

## Prerequisites
- Node.js 20+
- npm 10+
- Docker (for local orchestration)

## Environment Variables
Copy `.env.example` to `.env` and update as needed.
- `DATABASE_URL` — Postgres connection string
- `API_PORT` — Express port (default 4000)
- `NEXT_PUBLIC_API_BASE_URL` — Base URL used by Next.js fetchers

## Development
```bash
npm install
npm run dev:api   # starts Express API on :4000
npm run dev:web   # starts Next.js dashboard on :3000
```
Use `npm run dev` to run both concurrently.

## Database
Prisma schema lives in `db/prisma/schema.prisma`.
```bash
npx prisma migrate dev
npx prisma db seed
```

## Testing & Linting
```bash
npm run lint
npm run test
```
(Each workspace currently ships with placeholder Vitest configs.)

## Docker Compose
A `docker-compose.yml` file ties Postgres, API, and Web together. To run:
```bash
docker compose up --build
```

## Deployment
1. Build images:
   ```bash
   docker build -f infra/Dockerfile.api -t inkrockit-api .
   docker build -f infra/Dockerfile.web -t inkrockit-web .
   ```
2. Push images to the registry of choice.
3. Run migrations + seeds.
4. Apply the compose stack or deploy each image independently.
