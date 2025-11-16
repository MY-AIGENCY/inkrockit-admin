# InkRockit Intelligence Hub — Architecture

## Overview
The platform follows a polyrepo-inspired monorepo layout with dedicated workspaces for the API, Next.js web client, and shared packages. Type sharing happens through `@inkrockit/types` while shadcn-inspired primitives live in `@inkrockit/ui`.

```
/
├─ apps/
│  ├─ api          # Express + TypeScript service exposing REST endpoints
│  └─ web          # Next.js 14 App Router dashboard
├─ packages/
│  ├─ types        # Zod schemas + DTO contracts
│  └─ ui           # Shared design primitives
├─ db/prisma       # Postgres schema + seeds
├─ infra           # Docker + orchestration assets
└─ docs            # Architecture, API, runbook
```

## Backend
- **Runtime:** Node.js 20, Express 4
- **Schema:** Prisma models for companies, prospects, samples, jobs, tasks, payments, saved filters
- **Validation:** Zod schemas from `@inkrockit/types`
- **Observability:** Morgan request logs, structured JSON responses, health endpoint `/healthz`
- **Auth Placeholder:** Ready for header-based auth middleware; role guards stubbed for future work

## Frontend
- **Framework:** Next.js 14 App Router (server components + React Query hydration)
- **Styling:** Tailwind CSS + shared `@inkrockit/ui` primitives
- **State:** React Query for client-side mutations, URL search params for filters
- **Routing:** Dashboards plus entity list/detail pages exactly as defined in the source-of-truth
- **Charts:** Recharts line + bar chart components with graceful fallbacks when the API is offline

## Data Flow
1. Next.js server components call the API via `fetch`. When offline, curated fallback data keeps pages interactive.
2. API receives requests, applies filtering/pagination helpers, and responds with typed payloads.
3. Shared schemas ensure parity between backend responses and frontend expectations.
4. Saved filters persist on the API layer and are surfaced in the UI for quick rehydration.

## Extensibility
- Prisma schema already maps to the conceptual model. Future work can swap the mock data store for a real database without touching consumers.
- Docker targets (`infra/Dockerfile.*`) build discrete API/web images that compose together via `docker-compose.yml`.
- React Query + server components make it easy to enhance pages with live revalidation once the API supports incremental cache tags.
