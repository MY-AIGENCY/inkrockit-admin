# API Reference

Base URL defaults to `http://localhost:4000` in development. All responses are JSON. Pagination query params: `page`, `pageSize`, `sortBy`, `sortDir`.

## Health
- `GET /healthz`

## Prospects
- `GET /api/prospects` — Filter by `stage`, `region`, `search`
- `GET /api/prospects/:id`

## Companies
- `GET /api/companies`
- `GET /api/companies/:id`

## Sample Requests
- `GET /api/sample-requests` — Filter by `status`
- `GET /api/sample-requests/:id`

## Jobs
- `GET /api/jobs` — Filter by `status`
- `GET /api/jobs/:id`

## Payments
- `GET /api/payments`

## Activities
- `GET /api/activities`

## Tasks
- `GET /api/tasks` — Filter by `status`, `owner`
- `GET /api/tasks/:id`
- `PATCH /api/tasks/:id` — Payload `{ status?, priority?, dueDate? }`

## Saved Filters
- `GET /api/saved-filters?view=prospects`
- `POST /api/saved-filters`
- `PUT /api/saved-filters/:id`
- `DELETE /api/saved-filters/:id`

## Metrics
- `GET /api/metrics/overview`
- `GET /api/metrics/sample-requests`
- `GET /api/metrics/conversion`
- `GET /api/metrics/revenue`
