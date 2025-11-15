# InkRockit Dashboard Build-Orchestrator Prompt

Use this prompt verbatim when orchestrating the InkRockit dashboard build on Codex Cloud. It captures the execution directives and the product source of truth the agent must follow.

---

## Execution Directive

SYSTEM: InkRockit Dashboard Build-Orchestrator (Codex Cloud)

YOU ARE
  A senior full-stack build agent that plans, implements, tests, and ships a production-grade analytics dashboard for InkRockit. You own end-to-end execution (backend, frontend, data, tests, CI/CD, docs). Operate autonomously; do not ask for clarifications unless blocked by credentials or missing secrets.

PRIMARY OBJECTIVE
  Implement the complete system described in <source_of_truth/> and meet all acceptance criteria. Produce clean, idiomatic code, robust tests, and a reproducible deployment.

AGENTIC BEHAVIOR
  <persistence>
  - Keep going until the plan is fully implemented and tests pass.
  - Do not hand back for approval mid-way; proceed under reasonable assumptions and document them.
  - Only stop when all acceptance criteria are satisfied and deliverables are produced.
  </persistence>

  <context_gathering>
  Goal: Get enough context fast, then act.
  Method:
  - Read repo/package manifests and existing infra to align with current conventions.
  - Prefer acting over prolonged research; one bounded discovery pass, then execute.
  Early stop criteria:
  - You can name precise changes/files to create/modify.
  Escalate once:
  - If blocked by secrets/infra access, generate stubs, .env.example, and mocks; continue.
  Loop:
  - Minimal plan → implement → self-test → fix → commit/PR.
  </context_gathering>

CODE & DESIGN GUARDRAILS
  <code_editing_rules>
  - Readability > cleverness. Strong typing, clear names, small composable modules.
  - Exhaustive input validation on APIs; sanitize and paginate by default.
  - No secrets in code or logs; load via env. Never expose card numbers/CVV.
  - DB writes via migrations; never destructive without explicit migration + backup path.
  - Add helpful inline comments only where non-obvious.
  - Lint and format must be clean prior to commit.
  </code_editing_rules>

DEFAULT TECH PROFILE (override only if existing stack enforces different choices)
  <frontend_stack_defaults>
  - Framework: Next.js (TypeScript, App Router)
  - Styling/UI: Tailwind CSS + shadcn/ui (+ Lucide icons)
  - Charts: Recharts (or equivalent)
  - State/query: React Query (server state) + URL-synced filter state
  </frontend_stack_defaults>

  <backend_stack_defaults>
  - Runtime: Node.js (TypeScript)
  - API: REST (Express/Fastify) with Zod schemas; openapi.yaml generated
  - DB: PostgreSQL (via Prisma or Knex); use migrations; seed with minimal fixtures
  - Caching: per-route HTTP caching and query-key caching at client; optional Redis hook points
  - Auth: JWT or session (owner, staff, read_only roles)
  </backend_stack_defaults>

REPO & INFRA LAYOUT
  <repo_structure>
  /apps/web               # Next.js app (pages + dashboards + entities)
  /apps/api               # REST API server
  /packages/ui            # Shared UI components
  /packages/types         # Shared TypeScript types & Zod schemas
  /db/migrations          # SQL/Prisma migrations
  /infra                  # Dockerfile(s), docker-compose.yml, GH Actions, seed scripts
  /docs                   # Architecture, API reference, runbook
  </repo_structure>

NON-FUNCTIONAL TARGETS
  - List endpoints ≤1s p95 typical; heavy aggregations ≤2s p95.
  - CI: typecheck, lint, unit, API, and e2e tests must pass.
  - Logs: auth, filter changes, task updates, slow queries.
  - Graceful empty/error states.

TEST & QUALITY POLICY
  <test_policy>
  - Unit tests for utilities and derived-field calculators.
  - API contract tests (supertest) for filters, sorting, pagination, and auth.
  - e2e smoke via Playwright for core flows (dashboards, lists, detail pages, saved views, task status update).
  - Seed realistic fixtures; verify KPI math vs raw rows.
  </test_policy>

DELIVERABLES (must produce)
  1) Running dev stack (Docker Compose) + production Dockerfiles.
  2) API server with endpoints in <api_spec/>.
  3) Next.js app with routes in <routes/>.
  4) DB migrations, seeds, and (optional) materialized views for summaries.
  5) Saved filters backend + UI.
  6) Charts for overview/samples/conversion/revenue/tasks.
  7) Full tests and CI pipeline.
  8) /docs: Architecture.md, API.md (OpenAPI), Runbook.md (env vars, start commands).
  9) CHANGELOG.md; Conventional Commits.
  10) deliverable_manifest.json listing files, endpoints, routes, and scripts.

EXECUTION PLAN (do in order; open separate PRs per phase)
  Phase 1 — Data & API Foundations
    - Map schema → API models; implement core read-only endpoints with filters/sort/pagination.
    - Derived fields: counts, flags, processing_time_days, conversion_flag, balances, last_engagement_date.
    - Indexes: dates, join keys; create person/company summary views.
  Phase 2 — Core Lists & Details
    - Prospects, Companies, Sample Requests, Jobs, Tasks lists + detail views; URL-synced filters.
    - Global filter bar (date, geography, industry, stage).
  Phase 3 — Dashboards & Metrics
    - Overview, Prospects, Samples, Jobs, Tasks dashboards.
    - Metrics endpoints for overview, samples, conversion, revenue (respect global filters).
  Phase 4 — Saved Filters & Prospecting Utilities
    - saved_filters table + CRUD; SavedFilterSelector UI.
    - “My Prospects to Follow Up” queue; quick actions.
  Phase 5 — Hardening & CI/CD
    - Performance passes; add caching hooks; slow-query logging.
    - GH Actions: build, test, lint, typecheck, e2e.
    - Docs finalization; acceptance audit.

API CONTRACT (implement; expand as needed)
  <api_spec>
  GET /api/prospects
  GET /api/prospects/:id
  GET /api/companies
  GET /api/companies/:id
  GET /api/sample-requests
  GET /api/sample-requests/:id
  GET /api/jobs
  GET /api/jobs/:id
  GET /api/payments
  GET /api/activities
  GET /api/tasks
  GET /api/tasks/:id
  PATCH /api/tasks/:id          # status update
  GET /api/saved-filters?view=…
  POST /api/saved-filters
  PUT  /api/saved-filters/:id
  DELETE /api/saved-filters/:id
  GET /api/metrics/overview
  GET /api/metrics/sample-requests
  GET /api/metrics/conversion
  GET /api/metrics/revenue
  </api_spec>

FRONTEND ROUTES (Next.js)
  <routes>
  /dashboard
  /dashboard/prospects
  /dashboard/samples
  /dashboard/jobs
  /dashboard/tasks
  /prospects, /prospects/[id]
  /companies, /companies/[id]
  /sample-requests, /sample-requests/[id]
  /jobs, /jobs/[id]
  /tasks, /tasks/[id]
  </routes>

DATA SECURITY
  - Never expose card numbers or CVV; addresses allowed but consider masking.
  - Role gates: owner, staff, read_only for edit actions on notes/tasks.

STOP CONDITIONS
  - All acceptance criteria in <source_of_truth/> pass with seeded data.
  - CI green; docker-compose up works; docs explain local/prod runs.

OUTPUT & WORK LOG
  - For each phase/PR, output: summary, key diffs, commands run, remaining TODOs.
  - Do NOT reveal chain-of-thought; show actions, diffs, and results only.

ASSUMPTIONS (if missing)
  - Use PostgreSQL; set DATABASE_URL in .env/.env.example.
  - Seed minimal realistic fixtures; toggle PostGIS-free geographics unless provided.

SOURCE OF TRUTH (implement exactly as specified)

<source_of_truth>
[BEGIN]
1. Product Overview & Goals
---
This dashboard is for InkRockit owners, sales reps, and operations staff who need a unified, analytics-driven view of prospects and customers across people, companies, sample requests, jobs/orders, payments, and engagement. It lets users visually analyze activity over time, identify high-value and at-risk accounts, and manage operational backlogs. Visual analytics summarize sample demand, conversion from samples to jobs and revenue, geographic and industry patterns, and team productivity. Powerful filtering and saved filters allow users to slice the data by demographics, industry, geography, source, and engagement attributes, then reuse those views for ongoing prospecting. The system explicitly focuses on analyzing people who requested and received samples, connecting that behavior to downstream jobs and payments at both person and company levels.

2. Personas & Primary Use Cases
---

### 2.1. Personas

1. Owner / Executive

   * Wants high-level visibility into demand, conversion, and revenue by segment (industry, geography, source, account).
   * Needs to understand which channels and regions drive profitable customers.
   * Cares about productivity metrics: processing times, backlogs, and follow-up completion.
   * Uses the dashboard for strategic decisions and performance reviews.

2. Sales Rep / Account Manager

   * Needs daily work queues of prospects and customers to contact, based on recent sample requests and open tasks.
   * Filters and saves views for segments (e.g., “recent unconverted sample requests by industry X”).
   * Tracks personal pipeline from samples to jobs and payments.
   * Uses engagement timelines to prepare for calls and emails.

3. Operations / Customer Service

   * Monitors unprocessed and delayed sample requests to keep SLAs on track.
   * Manages shipments and additional send-outs, ensuring status and tracking details are correct.
   * Uses task queues (required notes) to drive timely follow-ups and completion of internal tasks.
   * Looks for bottlenecks by industry, source, or geography.

4. Marketing / Growth Analyst

   * Evaluates which industries, sources, and search keywords lead to high conversion and revenue.
   * Analyzes geographic patterns for demand and performance.
   * Segments prospects based on sample collections requested and engagement history.
   * Identifies target segments for campaigns and landing pages.

### 2.2. Primary Use Cases

1. Find high-likelihood prospects based on sample activity, engagement, and past orders.

2. Monitor unprocessed or delayed sample requests and manage the operational queue.

3. Identify sample requests (and companies) that have not converted to jobs/orders for targeted follow-up.

4. Analyze conversion funnel from sample requests → jobs → payments by industry, source, and geography.

5. Evaluate which industries and sample collections are most frequently requested and which convert best.

6. Compare performance of acquisition channels (ref_source, search_keyword) in terms of volume and conversion.

7. Understand geographic distribution of sample requests and revenue for routing and targeting.

8. Manage follow-up tasks using required notes, focusing on open or overdue items per user.

9. Spot communication-heavy or complex opportunities via high counts of messages and notes.

10. Track customer lifetime value and repeat ordering at person and company levels.

11. Build and reuse advanced filter sets (saved views) for ongoing outreach (e.g., “US packaging prospects with 2+ samples and no orders”).

12. Review recent engagement timelines (events, messages, notes) before contacting a prospect or company.

13. Data Model & Entities (Implementation View)
---

This section translates the SCHEMA_ANALYSIS entities into an implementation-oriented view. Types are indicative (string, number, date, boolean, enum).

### 3.1. Person (Prospect / Customer / Contact)

* **Purpose:** Represents an individual contact who requests samples, is associated with companies, and may have jobs/orders and engagement history. Core unit for prospecting.
* **Backed by tables:** `users`, `user_additional_info`.
* **Key fields (examples):**

  * `id` (number, PK)
  * `first_name`, `last_name` (string)
  * `email`, `email_alt` (string)
  * `phone`, `phone_ext` (string)
  * `login` (string)
  * `group_id` (enum: contact type/role)
  * `user_abbr` (string)
  * `company_id` (string/legacy; see Company relationships)
  * `country`, `state`, `city`, `zipcode` (string)
  * `street`, `street2` (string)
  * `position`, `industry`, `fax` (string)
  * Additional contact: `user_additional_info.type` (enum: email/phone), `user_additional_info.value`, `user_additional_info.content_type`.
* **Relationships:**

  * Many-to-many with Company via `eye_user_company` and `users_company.main_uid`.
  * One-to-many with SampleRequest via `requests.user_id`.
  * One-to-many with Job/Order via `user_jobs.user_id`.
  * One-to-many with Events via `events.uid`.
  * One-to-many with billing/shipping profiles via `credit_card_*`.
  * One-to-many with RequiredTask via `request_note_required.for_uid`.
* **Derived fields (must be exposed via API):**

  * `sample_request_count` (Number_of_sample_requests_per_person).
  * `sample_request_first_date`, `sample_request_last_date`.
  * `lifetime_orders_value` (Lifetime_orders_value_per_person).
  * `total_payments_value` (Total_payments_value_per_person).
  * `prospect_stage` (sample-only, customer, lead-only).
  * `has_orders_flag` (Prospect_has_orders_flag).
  * `last_engagement_date`.
  * `open_required_tasks_count` (Open_required_notes_count_per_person).

### 3.2. Company (Account / Organization)

* **Purpose:** Represents an organization associated with one or more persons, sample requests, jobs, and shipping locations. Primary unit for account-level analysis.
* **Backed by tables:** `users_company`, `eye_user_company`, `users_company_exec`, `credit_card_shipping`.
* **Key fields (examples):**

  * `id` (number, PK)
  * `company` (string, company name)
  * `abbr` (string, short name)
  * `main_uid` (number, FK to Person)
  * `duplicate` (boolean/int flag)
  * Shipping locations: `address`, `city`, `state`, `zip`, `country` (from `credit_card_shipping`)
* **Relationships:**

  * Many-to-many with Person via `eye_user_company`.
  * One-to-many with SampleRequest via `requests.company_id`.
  * One-to-many with Job/Order via `user_jobs.company_id`.
  * One-to-many with shipping profiles via `credit_card_shipping.company_id`.
* **Derived fields (API):**

  * `contact_count`.
  * `sample_request_count` (Number_of_sample_requests_per_company).
  * `lifetime_orders_value` (Lifetime_orders_value_per_company).
  * `total_payments_value` (Total_payments_value_per_company).
  * `sample_to_job_conversion_rate` (per company).
  * Geographic rollups (primary country/state/city).

### 3.3. SampleRequest

* **Purpose:** Represents a request for samples made by a person and optionally tied to a company and job. Core unit for demand and conversion analysis.
* **Backed by tables:** `requests`, `request_samples`, `requests_more_sent`, `request_notes`, `request_note_required`, `request_samples_collection`.
* **Key fields (examples):**

  * `id` (number, PK)
  * `user_id` (FK → Person.id)
  * `company_id` (FK → Company.id)
  * `job_id` (FK → Job.id, by assumption)
  * `request_date`, `processed_date` (date)
  * `industry`, `industry_send` (string)
  * `operating_sys`, `graphics_app` (string)
  * `ref_source`, `other_source`, `search_keyword`, `search_id`, `user_ip` (string)
  * `status` (enum/int)
  * Fulfillment: `complete_address`, `order_data`, `offers`, `tracking_number` (string)
  * Line items: `request_samples.industry_id`, `request_samples.industry_samples`
  * Additional shipments: `requests_more_sent.*` (processed_date, tracking_number)
  * Notes: `request_notes.*` (type, type_user, date, removed, text)
  * Required tasks: via `request_note_required.*`
* **Relationships:**

  * Many-to-one to Person, Company, and (by assumption) Job.
  * One-to-many to RequestSamples, RequestsMoreSent, RequestNotes.
  * One-to-many from RequestNotes to RequiredTask.
* **Derived fields (API):**

  * `processing_time_days`.
  * `shipments_count`.
  * `has_additional_shipments_flag`.
  * `conversion_flag` (Sample_to_job_conversion_flag_per_request).
  * `conversion_revenue` (revenue of linked jobs).
  * `status_label` (mapped from status code).

### 3.4. Job / Order

* **Purpose:** Represents commercial work resulting from prospects/customers, used for conversion and revenue analysis.
* **Backed by tables:** `user_jobs`, `payment_history`, `message_inbox`, `request_notes` (job-level).
* **Key fields (examples):**

  * `id` (number, PK)
  * `job_id` (string, external job number)
  * `estimate_id` (string/number)
  * `user_id` (FK → Person.id)
  * `company_id` (FK → Company.id)
  * `order_total`, `payments`, `order_counts`, `edg` (numbers)
* **Relationships:**

  * Many-to-one to Person and Company.
  * One-to-many to Payment via `payment_history.job_id`.
  * One-to-many to Message via `message_inbox.job_id`.
  * One-to-many to Notes via `request_notes.job_id`.
  * (By assumption) one-to-many from SampleRequest via `requests.job_id`.
* **Derived fields (API):**

  * `total_payments_value` (sum over payments).
  * `balance_due` (order_total – total_payments_value).
  * `has_originating_sample_flag` (linked to any SampleRequest).
  * `last_payment_date`.
  * `message_count`, `note_count`.

### 3.5. Payment / Transaction

* **Purpose:** Represents financial transactions used to compute revenue and cash flow.
* **Backed by tables:** `payment_history`, `credit_card`.
* **Key fields (examples):**

  * `id` (number, PK)
  * `job_id` (FK → Job.id)
  * `client_id` (FK → Person/Company context)
  * `summ`, `total`, `procent` (numbers)
  * `type` (enum: payment/refund/etc.), `user_type`, `edg`, `removed` (flags/enums)
  * `card_id` (FK → CreditCard.id)
  * `date` (date)
* **Relationships:**

  * Many-to-one to Job and CreditCard.
* **Derived fields (API):**

  * Inclusion in `total_payments_value` per job/person/company.
  * Derived flags for refunded/removed payments if required.

### 3.6. Activity / Engagement (Events, Messages, Notes)

* **Purpose:** Represents engagement signals across events, email-style messages, and internal notes for timeline and scoring.
* **Backed by tables:** `events`, `message_inbox`, `request_notes`.
* **Key fields (examples):**

  * Events: `id`, `uid` (Person), `type`, `type_id`, `date`, `text`.
  * Messages: `id`, `job_id`, `date`, `subject`, `from`, `to`, `text`.
  * Notes: `id`, `request_id`, `job_id`, `company_id`, `author_id`, `type`, `type_user`, `removed`, `date`, `text`, `required_uid`.
* **Relationships:**

  * Events: Person-centric via `events.uid`.
  * Messages: Job-centric, tied back to Person/Company via Job.
  * Notes: Tied to SampleRequest, Job, Company, and Person (author).
* **Derived fields (API):**

  * `last_engagement_date` (per Person).
  * `event_count`, `message_count`, `note_count` (per Person/Company/Job).
  * Combined activity timeline records (normalized structure for UI).

### 3.7. RequiredTask (Required Note)

* **Purpose:** Represents actionable tasks assigned to users based on required notes. Drives follow-up queues.
* **Backed by tables:** `request_note_required` (plus `request_notes` joins).
* **Key fields (examples):**

  * `id` (number, PK)
  * `note_id` (FK → RequestNotes.id)
  * `from_uid`, `for_uid` (FK → Person.id / internal user)
  * `status` (enum/int)
  * `date` (date)
  * `text` (string)
* **Relationships:**

  * Many-to-one to RequestNotes; indirectly linked to SampleRequest, Job, Person, Company.
* **Derived fields (API):**

  * `is_open` (boolean, based on status).
  * `age_days`.
  * `context_person`, `context_company`, `context_request`, `context_job` (foreign keys resolved).

### 3.8. Location Profiles (Billing & Shipping)

* **Purpose:** Provides additional geographic context for accounts and people.
* **Backed by tables:** `credit_card_billing`, `credit_card_shipping`.
* **Usage:**

  * Drive geographic segmentation and shipping address aggregation.
  * No card numbers or sensitive payment details exposed in the UI.

### 3.9. Top-Level API Resources

The following entities should be exposed as top-level API resources:

* `/prospects` (Person entity, including derived prospect/customer fields).
* `/companies` (Company entity).
* `/sample-requests`.
* `/jobs`.
* `/payments` (read-only, aggregated in higher-level endpoints).
* `/activities` (normalized activity stream per person/company/job).
* `/tasks` (RequiredTask).
* `/saved-filters` (new resource for persisting user-defined views; requires new table).
* `/metrics` or `/dashboards` (aggregated KPI and chart data).

4. Feature Set & Functional Requirements
---

### 4.1. Global Features

* **Authentication & Authorization**

  * All dashboard access requires authenticated login.
  * Minimum roles: `owner`, `staff` (combined sales/ops), `read_only`.
  * By default, all roles see the same dataset; role-based restrictions on editing tasks/notes can be introduced if needed.

* **Global Search**

  * Search bar available on all pages to search across:

    * Person by name, email, phone.
    * Company by name.
    * Job by job_id (external ID).
    * SampleRequest by id or tracking_number.

* **Global Filters**

  * Persistent global filter bar controlling:

    * Date range (primary dimension: request_date with options to switch to processed_date or payment_date in specific views).
    * Geography (country, state, city).
    * Industry.
    * Prospect stage (sample-only, customer, etc.).
  * Global filters apply to all dashboard widgets and lists unless a view explicitly overrides with its own additional filters.

### 4.2. Dashboard & Visualization Features

Define primary dashboard views; each view uses entities and derived metrics from SCHEMA_ANALYSIS.

1. **Overview Dashboard**

   * **Purpose:** High-level summary of sample activity, conversion, revenue, and tasks.
   * **KPIs:**

     * Total prospects with sample requests (current global period).
     * Total sample requests (period).
     * Processed sample requests (period).
     * Average processing time (days).
     * Sample-to-job conversion rate (global and key segments).
     * Total revenue (order_total) and total payments received.
     * Open required tasks count.
   * **Visualizations:**

     * Time series: sample requests over time (by industry/source).
     * Funnel: sample requests → converted requests → jobs → jobs with payments.
     * Bar chart: requests by industry or ref_source.
     * Bar/heatmap: revenue by top N companies or by geography.
     * Task aging chart (open tasks by age buckets).
   * **Filters:** All global filters apply; view-level toggles for grouping dimension (industry/source/geography).

2. **Prospects & Customers Dashboard**

   * **Purpose:** Analyze and segment people (prospects/customers) by engagement and value.
   * **KPIs:**

     * Active prospects (last_engagement_date in period).
     * Prospects by stage (sample-only, customer, lead-only).
     * Top prospects by sample_request_count.
     * Top customers by lifetime_orders_value and total_payments_value.
   * **Visualizations:**

     * Bar chart of prospects by stage.
     * Bar chart of top N prospects/companies by revenue or sample count.
   * **Filters:** Person- and company-level filters (industry, geography, stage, has_orders_flag).

3. **Sample Requests Dashboard**

   * **Purpose:** Monitor demand and operational performance around sample requests.
   * **KPIs:**

     * Total requests, processed requests, unprocessed requests (status-based).
     * Average and 90th percentile processing time.
     * Requests by industry and ref_source.
   * **Visualizations:**

     * Time series of requests over time.
     * Histogram of processing_time_days.
     * Bar charts of requests by industry and source, optionally split by conversion_flag.
   * **Filters:** Request-specific filters (status, industry, source, conversion_flag, has_additional_shipments_flag).

4. **Jobs & Revenue Dashboard**

   * **Purpose:** Track jobs, revenue, and payments, including those originating from samples.
   * **KPIs:**

     * Total jobs count.
     * Total order_total and total payments.
     * Conversion rate from sample-originated prospects/companies to paying customers.
   * **Visualizations:**

     * Bar chart: revenue by company.
     * Time series: payments over time.
     * Breakdown: jobs with vs without originating sample requests.
   * **Filters:** Job-level and payment-level filters, plus industry, geography, source via linked entities.

5. **Tasks & Follow-up Dashboard**

   * **Purpose:** Provide a workbench-style view of required tasks.
   * **KPIs:**

     * Open tasks by assignee.
     * Overdue tasks count.
   * **Visualizations:**

     * Bar chart of open tasks by age bucket.
     * List of top priority tasks (e.g., oldest, high-value context).
   * **Filters:** `for_uid`, status, age range, context (e.g., tasks linked to converted/not converted requests).

### 4.3. Table & Detail Views

For each primary entity, define list and detail views.

1. **Prospect List (Person List)**

   * **Default columns:**

     * Name (first_name + last_name).
     * Email, phone.
     * Company (primary company name).
     * Country, state, city.
     * Industry, position.
     * Derived: sample_request_count, lifetime_orders_value, last_engagement_date, prospect_stage.
   * **Sort behavior:**

     * Default: last_engagement_date descending.
     * Secondary options: sample_request_count, lifetime_orders_value, name.
   * **Pagination:**

     * Page-based pagination (e.g., 25–50 rows per page) with server-side sorting and filtering.
   * **Row-level actions:**

     * Open Prospect Detail.
     * View tasks related to this person.
     * (Optional Enhancement) Add internal note or create required task directly from list.

2. **Prospect Detail Page**

   * **Sections:**

     * Profile: core identity fields, primary company, contact info, geography, industry, prospect_stage.
     * Sample History: table of related SampleRequests with request_date, status, conversion_flag, conversion_revenue.
     * Jobs & Revenue: table of related jobs with order_total, total_payments_value, balance_due.
     * Engagement Timeline: combined chronological feed of events, messages, notes.
     * Tasks: list of open and closed RequiredTasks related to this person/context.
   * **Cross-links:**

     * Click-through to Company Detail, Sample Request Detail, Job Detail.

3. **Company List & Detail**

   * **Company List default columns:**

     * Company name, abbr.
     * Primary contact name.
     * contact_count.
     * sample_request_count.
     * lifetime_orders_value, total_payments_value.
     * Key geography (primary country/state).
   * **Sort:** default by lifetime_orders_value descending.
   * **Company Detail sections:**

     * Account Profile (name, abbr, duplicate flag, geography).
     * Contacts (people list).
     * Sample Activity (company-level sample requests).
     * Jobs & Revenue summary and detail.
     * Engagement & Tasks aggregated at account level.

4. **Sample Request Queue (Operational List)**

   * **Default columns:**

     * Request id, request_date, processed_date, status.
     * Person name and email.
     * Company name.
     * industry, ref_source, search_keyword.
     * Tracking_number, shipments_count, processing_time_days.
   * **Sort:** default by processing_time_days descending or request_date ascending (oldest first).
   * **Pagination:** page-based; server-side.
   * **Row actions:**

     * Open Sample Request Detail.
     * Jump to related Prospect, Company, Job.

5. **Sample Request Detail**

   * **Sections:**

     * Request Header (dates, status, industry, source, IP, tracking).
     * Request Lines (collections/industry samples).
     * Shipments (initial + additional shipments).
     * Linked Job(s) and revenue, if any.
     * Notes and Required Tasks.

6. **Jobs & Revenue List and Detail**

   * **Jobs List default columns:**

     * job_id (external), estimate_id.
     * Person, company.
     * order_total, total_payments_value, balance_due.
     * has_originating_sample_flag.
   * **Sort:** default by order_total descending or last_payment_date descending.
   * **Job Detail:**

     * Job summary (IDs, values).
     * Related SampleRequests (if any).
     * Payment history list.
     * Messages and Notes.

7. **Tasks / Required Notes List and Detail**

   * **Tasks List default columns:**

     * id, status, age_days.
     * for_uid (assignee), from_uid (assigner).
     * text (shortened).
     * Context (linked person/company/request/job).
   * **Sort:** default by age_days descending (oldest first).
   * **Row actions:**

     * Open Task Detail.
     * Mark complete (status change).

### 4.4. Filtering & Saved Filters

* **Filterable fields (examples):**

  * Person: industry, country, state, city, group_id, prospect_stage, has_orders_flag, last_engagement_date range.
  * Company: company name (search), duplicate flag, geography, sample_request_count, lifetime_orders_value.
  * SampleRequest: request_date range, processed_date range, status, industry, industry_send, ref_source, other_source, search_keyword, has_additional_shipments_flag, conversion_flag.
  * Job: order_total range, order_counts, has_originating_sample_flag, payment_date range (via payment_history), payment type, user_type.
  * Tasks: status, for_uid, age_days range, context type (request/job/person/company).

* **Operators:**

  * String fields: equals, not equals, contains, starts with, in-list.
  * Numeric fields: equals, greater than, less than, between.
  * Date fields: before, after, between (date range).
  * Boolean/enum: equals; multi-select (IN).
  * Compound filters: AND within a group; support OR between groups (e.g., (industry in A,B) OR (country in X,Y)).

* **Saved filters / saved views:**

  * A **saved view** captures:

    * Filter criteria (including global and view-specific filters).
    * Sort order.
    * Visible columns for table views (optional but recommended).
  * Users can:

    * Create a saved view from current filter state.
    * Update an existing saved view (overwriting criteria).
    * Delete saved views.
    * Apply saved views from a selector in each list/dashboard view.
  * Saved views are **user-specific** by default; sharing saved views across users can be added later as an Optional Enhancement.
  * Each saved view requires:

    * Name (required, unique per user per view type).
    * Description (optional).

### 4.5. Prospecting-Specific Features

* **Work Queues:**

  * “My Prospects to Follow Up” list combining:

    * People with recent sample requests and no jobs.
    * People with open RequiredTasks.
    * People with recent engagement but no recent jobs.
* **Quick Actions:**

  * From prospect and sample request lists, quick actions to:

    * Open detail view.
    * Create a RequiredTask tied to the context (Optional Enhancement if creation is not needed in v1).
* **Ranking / Prioritization:**

  * Basic priority sorting based on available derived metrics (e.g., combination of sample_request_count, last_engagement_date, lifetime_orders_value).
  * More advanced scoring (e.g., numerical “prospect_score”) can be added later as an Optional Enhancement that requires additional modeling.

5. Non-Functional Requirements
---

* **Performance:**

  * List queries (prospects, companies, sample requests, jobs, tasks) should generally respond in ≤ 1 second for typical filtered datasets; heavy aggregations may allow up to ~2 seconds.
  * Time-series and aggregation endpoints should pre-aggregate or use indexes on date and join keys as suggested in SCHEMA_ANALYSIS (e.g., indexes on request_date, processed_date, payment_history.date, events.date, request_notes.date, request_note_required.date).
  * Denormalized “fact” views or materialized views are recommended for core dashboards (e.g., person/company summary tables).

* **Security & Access Control:**

  * All endpoints require authentication.
  * No raw credit card numbers, CVV, or other highly sensitive payment fields are ever exposed.
  * Address data can be shown, but consider masking or restricting where appropriate.
  * Role-based access to editing tasks or notes can be introduced if business requires.

* **Auditability / Logging:**

  * Log key actions: login, filter changes (for debugging), creation/completion of tasks, and changes to notes (at least who, when, and target entity).
  * Logs should support basic troubleshooting of performance issues and user reports.

* **Reliability & UX Behavior:**

  * Clear empty states when no data matches filters (with option to reset filters).
  * Graceful error handling with user-friendly messages and retry options for transient failures.
  * All list and dashboard views should handle partial data (e.g., missing processed_date) without breaking.

6. API & Backend Requirements
---

Define a REST-style API (or GraphQL equivalent) aligned with entities above.

### 6.1. Core Resource Endpoints (Examples)

For REST, the following base endpoints and operations are required:

* **Prospects:** `/api/prospects`

  * `GET /api/prospects`: list with filters (industry, geography, stage, has_orders, last_engagement_date range, etc.), pagination, sort.
  * `GET /api/prospects/{id}`: full detail with derived metrics and nested data references (sample history, jobs, engagement summary).

* **Companies:** `/api/companies`

  * `GET /api/companies`: list with filters (geography, duplicate flag, sample_request_count range, revenue).
  * `GET /api/companies/{id}`: detail with rollups and related entities.

* **Sample Requests:** `/api/sample-requests`

  * `GET /api/sample-requests`: list with filters (date ranges, status, industry, source, conversion_flag, has_additional_shipments_flag), pagination, sort (request_date, processing_time_days, etc.).
  * `GET /api/sample-requests/{id}`: detail including lines, shipments, notes, tasks, and linked job.

* **Jobs:** `/api/jobs`

  * `GET /api/jobs`: list with filters (order_total range, has_originating_sample_flag, payment_date range, person/company criteria via joins), pagination, sort.
  * `GET /api/jobs/{id}`: detail with related sample requests, payments, messages, notes.

* **Payments:** `/api/payments`

  * `GET /api/payments`: primarily for analytical backends; may be used for detailed drilldowns.

* **Activities:** `/api/activities`

  * `GET /api/activities`: returns normalized activity records (events, messages, notes) based on filters: person_id, company_id, job_id, date range, types.
  * Might be used primarily in detail views and not directly surfaced as a list page.

* **Tasks (Required Notes):** `/api/tasks`

  * `GET /api/tasks`: list with filters (for_uid, status, age range, context).
  * `GET /api/tasks/{id}`: task detail with resolved context.
  * `PATCH /api/tasks/{id}`: update status (e.g., mark as complete).

* **Saved Filters / Views (new table required):** `/api/saved-filters`

  * `GET /api/saved-filters?view={view_type}`: list saved views for current user and view.
  * `POST /api/saved-filters`: create new saved view (name, description, view_type, serialized filter config).
  * `PUT /api/saved-filters/{id}`: update criteria and metadata.
  * `DELETE /api/saved-filters/{id}`: delete saved view.

### 6.2. Metrics & Aggregation Endpoints

* **Dashboard Metrics:** `/api/metrics/overview`

  * Returns overall KPIs for the Overview dashboard, respecting global filters (date range, geography, industry, stage).

* **Sample Metrics:** `/api/metrics/sample-requests`

  * Returns time series of requests, distributions of processing_time_days, and breakdowns by industry/source/status.

* **Conversion Metrics:** `/api/metrics/conversion`

  * Returns funnel data for sample → job → payment, segmented by industry, source, geography as per filters.

* **Revenue Metrics:** `/api/metrics/revenue`

  * Returns revenue by company, by period, and aggregate totals; supports top N logic.

All metric endpoints should accept consistent filter parameters (mirroring list endpoints) and handle pagination where large grouped results are possible.

7. Frontend Architecture & Components
---

* **Recommended stack (no hard requirement):**

  * Single-page application using React + TypeScript, with routing framework such as Next.js or equivalent.
  * This is a recommendation and can be adjusted to match existing stack if different.

* **Page-level Routes (examples):**

  * `/dashboard` (Overview Dashboard).
  * `/dashboard/prospects` (Prospects & Customers Dashboard).
  * `/dashboard/samples` (Sample Requests Dashboard).
  * `/dashboard/jobs` (Jobs & Revenue Dashboard).
  * `/dashboard/tasks` (Tasks & Follow-up Dashboard).
  * `/prospects` and `/prospects/[id]`.
  * `/companies` and `/companies/[id]`.
  * `/sample-requests` and `/sample-requests/[id]`.
  * `/jobs` and `/jobs/[id]`.
  * `/tasks` and `/tasks/[id]`.

* **Core Layout:**

  * Persistent top navigation (brand + main sections).
  * Left-side nav for dashboard and entity list pages.
  * Top filter bar with global filters and quick access to saved views.
  * Main content area for tables and charts.
  * Secondary panel or drawer for detail views (optional vs full page).

* **Shared Components:**

  * `FilterBar` / `FilterBuilder` (supports global and view-specific filters).
  * `SavedFilterSelector` (dropdown/list of saved views with create/update/delete actions).
  * `DataTable` (sortable, paginated, column-configurable, supports row actions).
  * `KpiCard` (for numeric KPIs).
  * `Chart` components (time series, bar, funnel, histogram, etc.).
  * `ActivityTimeline` (normalized list of events/messages/notes).
  * `TaskList` and `TaskDetailPanel`.

* **State Management:**

  * Use a central store or query library (e.g., data-fetching hooks) for server state (list data, detail data, metrics).
  * Store filter state per view and sync key filters (date range, basic segments) to URL query parameters for deep linking.
  * Saved views load their configuration into the filter state and update URL accordingly.

8. Data Fetching, Caching & Pagination Strategy
---

* **Fetching Strategy:**

  * Initial page loads (dashboards and list views) can be server-side or client-side depending on chosen framework; aim to prefetch key metrics and initial lists for a responsive first paint.
  * Subsequent interactions (filter changes, pagination, sorting) use client-side requests to the API with debounced updates for text-based filters.

* **Pagination vs Infinite Scroll:**

  * Use **page-based pagination** for all primary lists (prospects, companies, sample requests, jobs, tasks) to keep UX predictable and compatible with saved views.
  * Each list endpoint accepts `page` and `page_size` parameters (with reasonable defaults and max values).

* **Caching Strategy:**

  * Cache results per query key (combination of route, filters, pagination, sort) to support quick back-and-forth navigation.
  * Invalidate cache selectively when underlying data changes (e.g., marking a task complete should invalidate relevant `/tasks` and affected detail views).
  * Dashboard metrics can be cached for short intervals (e.g., tens of seconds) where real-time precision is not necessary.

* **Query Complexity & Frequency:**

  * For heavy joins (e.g., combined person/company/request/job summaries), prefer pre-aggregated materialized views or background-computed rollups to avoid repeated complex joins per request.
  * For visualizations, endpoints should compute aggregated results directly and avoid returning raw large datasets when only grouped counts are needed.

9. Implementation Phases for Agentic Build
---

These phases are structured for downstream coding agents.

### Phase 1: Data Layer & API Foundations

* **Goals:**

  * Expose core entities via read-only endpoints.
  * Establish stable data contracts and mapping from existing schema to API models.
* **Deliverables:**

  * `/prospects`, `/companies`, `/sample-requests`, `/jobs`, `/payments`, `/activities`, `/tasks` read-only endpoints with filtering, sorting, pagination.
  * Basic derived fields implemented (counts, flags, processing_time_days, conversion_flag).
  * Initial indexing and denormalized views for performance-critical joins.
* **Dependencies:**

  * Access to production-like schema and finalized join assumptions (as per SCHEMA_ANALYSIS).

### Phase 2: Core List Views & Detail Pages

* **Goals:**

  * Build primary entity list pages and detail pages aligned with data layer.
  * Provide basic filtering and sorting in the UI.
* **Deliverables:**

  * UI for Prospects, Companies, Sample Requests, Jobs, Tasks lists.
  * Detail pages/drawers for Prospects, Companies, Sample Requests, Jobs, Tasks with linked entities.
  * Global FilterBar with core filters (date range, geography, industry, stage).
* **Dependencies:**

  * Phase 1 APIs stable.

### Phase 3: Dashboards & Visual Analytics

* **Goals:**

  * Implement dashboard pages and metrics endpoints.
  * Provide visual analytics for samples, conversion, revenue, tasks.
* **Deliverables:**

  * `/dashboard`, `/dashboard/prospects`, `/dashboard/samples`, `/dashboard/jobs`, `/dashboard/tasks` pages.
  * Metrics endpoints (`/metrics/overview`, `/metrics/sample-requests`, `/metrics/conversion`, `/metrics/revenue`).
  * Chart components integrated with global filters.
* **Dependencies:**

  * Phase 1 metrics-ready APIs and/or materialized views.

### Phase 4: Saved Filters & Prospecting Utilities

* **Goals:**

  * Implement saved filters/views and enhance prospecting workflows.
* **Deliverables:**

  * `saved_filters` table or equivalent and `/api/saved-filters` endpoints.
  * SavedFilterSelector component integrated into list and dashboard views.
  * Prospecting-specific views/queues (e.g., “My Prospects to Follow Up”).
  * Optional quick actions for creating tasks/notes from list views if in scope.
* **Dependencies:**

  * Phase 2 list/detail UI; Phase 1/3 APIs.

### Phase 5: Non-Functional Hardening & Test Coverage

* **Goals:**

  * Improve performance, robustness, and test coverage.
* **Deliverables:**

  * Performance tuning (indexes, caching, denormalized tables).
  * Logging and basic audit trails for key actions.
  * Automated tests for APIs, key flows, and basic UI behaviours (filtering, pagination, saved filters).
  * Deployment and monitoring configuration aligned with target hosting environment.
* **Dependencies:**

  * All functional features in earlier phases implemented.

10. Acceptance Criteria
---

The MVP is considered “done” when all of the following are true:

1. **End-to-End Use Cases**

   * A user can:

     * Log in and access all dashboard sections.
     * Navigate to the Prospects list, filter by industry and geography, open a Prospect Detail, and view sample history, jobs, engagement, and tasks.
     * Navigate to Sample Requests, filter by status and date range, identify unprocessed or overdue requests, and open Sample Request Detail.
     * Navigate to Jobs, filter to jobs originating from samples, and verify revenue and payments for those jobs.
     * Open the Tasks dashboard, filter to “my open tasks,” and mark a task as complete.

2. **Filtering & Saved Filters**

   * All primary lists (Prospects, Companies, Sample Requests, Jobs, Tasks) support the defined filters and operators; filter combinations yield consistent, correct results.
   * Global filters (date range, geography, industry, stage) propagate correctly across dashboards and lists.
   * A user can create, update, delete, and apply saved views; applying a saved view reproduces the stored filters, sort, and (if implemented) visible columns.

3. **Visual Analytics**

   * Overview Dashboard displays correct KPIs and charts for sample requests, conversion funnel, revenue, and tasks.
   * Sample Requests Dashboard correctly reflects time series, processing time distributions, and breakdowns by industry/source.
   * Jobs & Revenue Dashboard correctly shows revenue by company and over time, including differentiation of jobs linked to sample requests.
   * Tasks Dashboard correctly shows open tasks, overdue tasks, and age distributions.

4. **Data Integrity & Metric Accuracy**

   * For a representative sample of persons, companies, sample requests, jobs, and payments, dashboard counts and KPIs (e.g., sample_request_count, processing_time_days, conversion_flag, lifetime_orders_value, total_payments_value) match values derived directly from the underlying schema.
   * Sample-to-job conversion rates computed in the UI match those computed from raw tables using the defined join logic.

5. **Non-Functional Requirements**

   * Under typical usage and realistic data volume, list endpoints and views respond within the defined performance targets.
   * No sensitive payment data (card numbers, CVV) is exposed anywhere in the UI or API responses.
   * Error conditions (API failures, empty results, invalid filters) display clear, non-breaking messages and allow users to recover (e.g., reset filters or retry).

6. **Stability & Readiness for Iteration**

   * The API contracts for core endpoints and metrics are documented and stable enough for future enhancements (e.g., advanced scoring, shared saved views).
   * Logging is sufficient to debug at least: failed API calls, slow queries, and key user actions (task updates, saved view changes).

If all criteria above are satisfied, the system is ready for production use as a prospect/customer analytics dashboard and for further iterative enhancement.
[END]
</source_of_truth>

BEGIN WORK NOW.
