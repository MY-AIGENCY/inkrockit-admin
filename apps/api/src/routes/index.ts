import type { Request, Response, NextFunction } from 'express';
import { Router } from 'express';
import type { RowDataPacket } from 'mysql2/promise';
import {
  type Activity,
  type Company,
  type ConversionMetric,
  type Job,
  type MetricsOverviewResponse,
  type Prospect,
  type RevenueMetric,
  type SampleMetric,
  type SampleRequest,
  type SavedFilter,
  type Task,
} from '@inkrockit/types';
import { z } from 'zod';
import { query } from '../db.js';
import { createPaginatedResponse, parsePagination } from '../lib/pagination.js';

const router = Router();

const stageOptions = ['new', 'engaged', 'sample_sent', 'job_created', 'customer'] as const;
const regionOptions = ['West', 'Mountain', 'Midwest', 'South', 'Northeast'] as const;

const prospectFiltersSchema = z.object({
  stage: z.enum(stageOptions).optional(),
  region: z.enum(regionOptions).optional(),
  search: z.string().optional(),
});

const sampleRequestFiltersSchema = z.object({
  status: z.enum(['pending', 'in_transit', 'delivered', 'cancelled']).optional(),
});

const jobFiltersSchema = z.object({
  status: z.enum(['estimating', 'queued', 'in_production', 'completed', 'cancelled']).optional(),
});

const taskFiltersSchema = z.object({
  status: z.enum(['open', 'in_progress', 'completed', 'blocked']).optional(),
  owner: z.string().optional(),
});

const savedFilterViewSchema = z.object({
  view: z.string().optional(),
});

const requestStatusMap: Record<number, SampleRequest['status']> = {
  0: 'pending',
  1: 'in_transit',
  2: 'delivered',
  3: 'cancelled',
  4: 'cancelled',
};

const inverseRequestStatusMap: Record<SampleRequest['status'], number[]> = {
  pending: [0],
  in_transit: [1],
  delivered: [2],
  cancelled: [3, 4],
};

const stageExpression = `
  CASE
    WHEN COALESCE(job_stats.job_total, 0) > 0 THEN 'job_created'
    WHEN COALESCE(req_stats.request_total, 0) >= 2 THEN 'sample_sent'
    WHEN u.group_id >= 2 THEN 'engaged'
    ELSE 'new'
  END
`;

const regionExpression = `
  CASE
    WHEN UPPER(u.state) IN ('CA','OR','WA','AK','HI') THEN 'West'
    WHEN UPPER(u.state) IN ('AZ','CO','ID','MT','NV','NM','UT','WY') THEN 'Mountain'
    WHEN UPPER(u.state) IN ('IL','IN','IA','KS','MI','MN','MO','NE','ND','OH','SD','WI') THEN 'Midwest'
    WHEN UPPER(u.state) IN ('AL','AR','DC','DE','FL','GA','KY','LA','MD','MS','NC','OK','SC','TN','TX','VA','WV') THEN 'South'
    WHEN UPPER(u.state) IN ('CT','MA','ME','NH','NJ','NY','PA','RI','VT') THEN 'Northeast'
    ELSE 'Other'
  END
`;

const jobStatusExpression = `
  CASE
    WHEN j.order_total IS NOT NULL
      AND j.order_total > 0
      AND j.payments IS NOT NULL
      AND j.payments >= j.order_total THEN 'completed'
    WHEN j.payments IS NOT NULL
      AND j.payments > 0 THEN 'in_production'
    WHEN j.order_counts IS NOT NULL
      AND j.order_counts > 0 THEN 'queued'
    ELSE 'estimating'
  END
`;

const taskStatusExpression = `
  CASE
    WHEN rn.removed = 1 THEN 'completed'
    WHEN rn.type IN ('blocker','hold') THEN 'blocked'
    WHEN rn.type IN ('call','email','meeting','followup') THEN 'in_progress'
    ELSE 'open'
  END
`;

const taskPriorityExpression = `
  CASE
    WHEN rn.type IN ('urgent','escalation') THEN 'high'
    WHEN rn.type IN ('call','email','meeting','followup') THEN 'medium'
    ELSE 'low'
  END
`;

type AsyncHandler = (req: Request, res: Response, next: NextFunction) => Promise<void>;

const asyncHandler =
  (handler: AsyncHandler) =>
  (req: Request, res: Response, next: NextFunction): void => {
    handler(req, res, next).catch(next);
  };

function toIso(value: string | Date | null | undefined, fallback?: Date): string {
  const date = value ? new Date(value) : fallback ?? new Date();
  return date.toISOString();
}

function toIsoOrNull(value: string | Date | null | undefined): string | null {
  return value ? new Date(value).toISOString() : null;
}

function clampScore(value: number) {
  return Math.min(100, Math.max(0, Math.round(value)));
}

function mapStateToRegion(state?: string | null) {
  const normalized = state?.toUpperCase() ?? '';
  if (!normalized) {
    return 'Other';
  }

  if (['CA', 'OR', 'WA', 'AK', 'HI'].includes(normalized)) {
    return 'West';
  }
  if (['AZ', 'CO', 'ID', 'MT', 'NV', 'NM', 'UT', 'WY'].includes(normalized)) {
    return 'Mountain';
  }
  if (['IL', 'IN', 'IA', 'KS', 'MI', 'MN', 'MO', 'NE', 'ND', 'OH', 'SD', 'WI'].includes(normalized)) {
    return 'Midwest';
  }
  if (['AL', 'AR', 'DC', 'DE', 'FL', 'GA', 'KY', 'LA', 'MD', 'MS', 'NC', 'OK', 'SC', 'TN', 'TX', 'VA', 'WV'].includes(normalized)) {
    return 'South';
  }
  if (['CT', 'MA', 'ME', 'NH', 'NJ', 'NY', 'PA', 'RI', 'VT'].includes(normalized)) {
    return 'Northeast';
  }

  return 'Other';
}

function derivePersona(position?: string | null) {
  const jobTitle = position?.toLowerCase() ?? '';
  if (!jobTitle) {
    return 'Specifier';
  }
  if (jobTitle.includes('chief') || jobTitle.includes('cfo') || jobTitle.includes('ceo') || jobTitle.includes('president')) {
    return 'Executive';
  }
  if (jobTitle.includes('vp') || jobTitle.includes('vice')) {
    return 'Executive';
  }
  if (jobTitle.includes('director')) {
    return 'Decision Maker';
  }
  if (jobTitle.includes('manager')) {
    return 'Manager';
  }
  return 'Specifier';
}

function determineSegment(totalRevenue: number): Company['segment'] {
  if (totalRevenue >= 750000) {
    return 'enterprise';
  }
  if (totalRevenue >= 150000) {
    return 'mid_market';
  }
  return 'smb';
}

function calculateEngagementScore(requestsCount: number, jobsCount: number, groupId?: number | null) {
  const base = 35 + requestsCount * 5 + jobsCount * 10 + (groupId ?? 0) * 3;
  return clampScore(base);
}

function calculateConversionLikelihood(requestsCount: number, jobsCount: number) {
  if (!requestsCount) {
    return 0.2;
  }
  return Math.min(1, Number((jobsCount / requestsCount + 0.25).toFixed(2)));
}

function buildOwnerName(firstName?: string | null, lastName?: string | null) {
  const name = [firstName, lastName].filter(Boolean).join(' ').trim();
  return name || 'Unassigned';
}

function getSampleStatus(value: number | null): SampleRequest['status'] {
  if (value === null || value === undefined) {
    return 'pending';
  }
  return requestStatusMap[value] ?? 'pending';
}

function differenceInDays(later: Date | string | null | undefined, earlier: Date | string | null | undefined) {
  if (!later || !earlier) {
    return null;
  }
  const end = new Date(later).getTime();
  const start = new Date(earlier).getTime();
  if (Number.isNaN(end) || Number.isNaN(start)) {
    return null;
  }
  return Math.max(0, Math.round((end - start) / (1000 * 60 * 60 * 24)));
}

function buildLimitClause(limit: number, offset = 0) {
  const safeLimit = Math.max(1, Math.floor(limit));
  const safeOffset = Math.max(0, Math.floor(offset));
  return `LIMIT ${safeLimit} OFFSET ${safeOffset}`;
}

async function getMonthlySeries(sql: string, params: any[] = [], buckets = 5) {
  const rows = await query<RowDataPacket[]>(sql, params);
  const now = new Date();
  const results: number[] = [];

  for (let index = buckets - 1; index >= 0; index -= 1) {
    const current = new Date(Date.UTC(now.getUTCFullYear(), now.getUTCMonth() - index, 1));
    const bucketKey = current.toISOString().slice(0, 7);
    const match = rows.find((row) => row.bucket === bucketKey);
    results.push(Number(match?.total ?? 0));
  }

  return results;
}

function computeTrendChange(trend: number[]) {
  if (!trend.length) {
    return 0;
  }
  const first = trend[0] || 1;
  const last = trend[trend.length - 1] || 0;
  const delta = first === 0 ? last : ((last - first) / first) * 100;
  return Math.round(delta);
}

async function buildSavedFilters(view?: string) {
  const filters: SavedFilter[] = [];
  const timestamp = new Date().toISOString();

  const topStates = await query<RowDataPacket[]>(
    `
      SELECT UPPER(state) AS state, COUNT(*) AS total
      FROM users
      WHERE state IS NOT NULL AND state <> ''
      GROUP BY UPPER(state)
      ORDER BY total DESC
      LIMIT 3
    `,
  );

  topStates.forEach((row, index) => {
    const region = mapStateToRegion(row.state);
    filters.push({
      id: `flt_region_${row.state}`,
      view: 'prospects',
      name: `${region} Focus`,
      createdBy: 'System',
      filters: { region },
      isDefault: index === 0,
      createdAt: timestamp,
    });
  });

  const topIndustries = await query<RowDataPacket[]>(
    `
      SELECT industry_send AS industry, COUNT(*) AS total
      FROM requests
      WHERE industry_send IS NOT NULL AND industry_send <> ''
      GROUP BY industry_send
      ORDER BY total DESC
      LIMIT 2
    `,
  );

  topIndustries.forEach((row) => {
    filters.push({
      id: `flt_industry_${row.industry}`,
      view: 'sample-requests',
      name: `${row.industry} Samples`,
      createdBy: 'System',
      filters: { industry: row.industry },
      isDefault: false,
      createdAt: timestamp,
    });
  });

  filters.push({
    id: 'flt_samples_pending',
    view: 'sample-requests',
    name: 'Pending Logistics',
    createdBy: 'System',
    filters: { status: 'pending' },
    isDefault: filters.every((filter) => filter.view !== 'sample-requests'),
    createdAt: timestamp,
  });

  if (view) {
    return filters.filter((filter) => filter.view === view);
  }

  return filters;
}

router.get(
  ['/healthz', '/api/health'],
  asyncHandler(async (_req, res) => {
    await query('SELECT 1');
    res.json({ status: 'ok', timestamp: new Date().toISOString() });
  }),
);

router.get(
  '/api/prospects',
  asyncHandler(async (req, res) => {
    const filters = prospectFiltersSchema.parse(req.query);
    const pagination = parsePagination(req.query);
    const whereClauses = ['1=1'];
    const params: any[] = [];

    if (filters.search) {
      const value = `%${filters.search.toLowerCase()}%`;
      whereClauses.push(
        `(LOWER(CONCAT(u.first_name, ' ', u.last_name)) LIKE ? OR LOWER(u.email) LIKE ? OR LOWER(u.company_id) LIKE ?)`,
      );
      params.push(value, value, value);
    }

    if (filters.stage) {
      whereClauses.push(`${stageExpression} = ?`);
      params.push(filters.stage);
    }

    if (filters.region) {
      whereClauses.push(`${regionExpression} = ?`);
      params.push(filters.region);
    }

    const baseFrom = `
      FROM users u
      LEFT JOIN (
        SELECT user_id, COUNT(*) AS request_total, MAX(request_date) AS last_request_date, MIN(request_date) AS first_request_date
        FROM requests
        GROUP BY user_id
      ) req_stats ON req_stats.user_id = u.id
      LEFT JOIN (
        SELECT user_id, COUNT(*) AS job_total
        FROM user_jobs
        GROUP BY user_id
      ) job_stats ON job_stats.user_id = u.id
      WHERE ${whereClauses.join(' AND ')}
    `;

    const rows = await query<RowDataPacket[]>(
      `
        SELECT
          u.id,
          u.first_name,
          u.last_name,
          u.email,
          u.phone,
          u.position,
          u.company_id,
          u.industry,
          u.state,
          u.group_id,
          COALESCE(req_stats.request_total, 0) AS request_total,
          req_stats.last_request_date,
          req_stats.first_request_date,
          COALESCE(job_stats.job_total, 0) AS job_total,
          ${stageExpression} AS stage,
          ${regionExpression} AS region
        ${baseFrom}
        ORDER BY COALESCE(req_stats.last_request_date, u.id) DESC
        ${buildLimitClause(pagination.pageSize, pagination.offset)}
      `,
      params,
    );

    const [{ total }] = await query<RowDataPacket[]>(
      `
        SELECT COUNT(*) AS total
        ${baseFrom}
      `,
      params,
    );

    const prospects: Prospect[] = rows.map((row) => {
      const requestsCount = Number(row.request_total ?? 0);
      const jobsCount = Number(row.job_total ?? 0);

      return {
        id: String(row.id),
        firstName: row.first_name ?? 'Unknown',
        lastName: row.last_name ?? '',
        email: row.email ?? '',
        phone: row.phone ?? undefined,
        title: row.position ?? undefined,
        companyId: row.company_id ? String(row.company_id) : 'unassigned',
        source: requestsCount > 0 ? 'inbound' : 'outbound',
        persona: derivePersona(row.position),
        stage: (row.stage ?? 'new') as Prospect['stage'],
        region: row.region ?? 'Other',
        industry: row.industry ?? 'General',
        createdAt: toIso(row.first_request_date, new Date(Date.now() - 30 * 24 * 60 * 60 * 1000)),
        lastActivityAt: toIso(row.last_request_date ?? row.first_request_date ?? new Date()),
        engagementScore: calculateEngagementScore(requestsCount, jobsCount, row.group_id),
        sampleRequests: requestsCount,
        conversionLikelihood: calculateConversionLikelihood(requestsCount, jobsCount),
      };
    });

    res.json(createPaginatedResponse(prospects, Number(total ?? 0), pagination));
  }),
);

router.get(
  '/api/companies',
  asyncHandler(async (req, res) => {
    const pagination = parsePagination(req.query);
    const rows = await query<RowDataPacket[]>(
      `
        SELECT
          c.id,
          c.company,
          c.abbr,
          c.main_uid,
          COUNT(DISTINCT r.id) AS sample_requests,
          COUNT(DISTINCT j.id) AS job_count,
          COALESCE(SUM(j.order_total), 0) AS total_revenue,
          MAX(r.request_date) AS last_engagement,
          ANY_VALUE(owner.first_name) AS owner_first_name,
          ANY_VALUE(owner.last_name) AS owner_last_name,
          ANY_VALUE(owner.street) AS owner_street,
          ANY_VALUE(owner.street2) AS owner_street2,
          ANY_VALUE(owner.city) AS owner_city,
          ANY_VALUE(owner.state) AS owner_state,
          ANY_VALUE(owner.zipcode) AS owner_zip,
          ANY_VALUE(owner.country) AS owner_country,
          ANY_VALUE(r.industry_send) AS industry
        FROM users_company c
        LEFT JOIN requests r ON r.company_id = c.id
        LEFT JOIN user_jobs j ON j.company_id = c.id
        LEFT JOIN users owner ON owner.id = c.main_uid
        GROUP BY c.id
        ORDER BY total_revenue DESC
        ${buildLimitClause(pagination.pageSize, pagination.offset)}
      `,
      [],
    );

    const [{ total }] = await query<RowDataPacket[]>(`SELECT COUNT(*) AS total FROM users_company`);

    const companies: Company[] = rows.map((row) => {
      const revenue = Number(row.total_revenue ?? 0);
      const sampleRequests = Number(row.sample_requests ?? 0);
      const jobsCount = Number(row.job_count ?? 0);
      const ownerState = row.owner_state ?? null;

      return {
        id: String(row.id),
        name: row.company ?? 'Unknown Company',
        industry: row.industry ?? 'Mixed Use',
        segment: determineSegment(revenue),
        region: mapStateToRegion(ownerState),
        headquarters: {
          line1: row.owner_street ?? 'On File',
          line2: row.owner_street2 ?? null,
          city: row.owner_city ?? 'N/A',
          state: ownerState ?? 'NA',
          postalCode: row.owner_zip ?? '00000',
          country: row.owner_country ?? 'US',
        },
        totalSampleRequests: sampleRequests,
        totalJobs: jobsCount,
        totalRevenue: revenue,
        lastEngagementDate: toIsoOrNull(row.last_engagement),
        healthScore: clampScore(40 + jobsCount * 5 + sampleRequests * 2),
        owner: buildOwnerName(row.owner_first_name, row.owner_last_name),
      };
    });

    res.json(createPaginatedResponse(companies, Number(total ?? 0), pagination));
  }),
);

router.get(
  '/api/sample-requests',
  asyncHandler(async (req, res) => {
    const filters = sampleRequestFiltersSchema.parse(req.query);
    const pagination = parsePagination(req.query);
    const whereClauses = ['1=1'];
    const params: any[] = [];

    if (filters.status) {
      const statuses = inverseRequestStatusMap[filters.status];
      if (statuses?.length) {
        whereClauses.push(`r.status IN (${statuses.map(() => '?').join(',')})`);
        params.push(...statuses);
      }
    }

    const rows = await query<RowDataPacket[]>(
      `
        SELECT
          r.id,
          r.user_id,
          r.company_id,
          r.job_id,
          r.request_date,
          r.processed_date,
          r.status,
          r.industry,
          r.industry_send,
          r.ref_source,
          r.order_data
        FROM requests r
        WHERE ${whereClauses.join(' AND ')}
        ORDER BY r.request_date DESC
        ${buildLimitClause(pagination.pageSize, pagination.offset)}
      `,
      params,
    );

    const [{ total }] = await query<RowDataPacket[]>(
      `SELECT COUNT(*) AS total FROM requests r WHERE ${whereClauses.join(' AND ')}`,
      params,
    );

    const sampleRequests: SampleRequest[] = rows.map((row) => ({
      id: String(row.id),
      prospectId: row.user_id ? String(row.user_id) : 'anonymous',
      companyId: row.company_id ? String(row.company_id) : 'unassigned',
      requestedAt: toIso(row.request_date, new Date()),
      fulfilledAt: toIsoOrNull(row.processed_date),
      status: getSampleStatus(row.status),
      shippingCarrier: undefined,
      shippingTracking: undefined,
      material: row.industry_send ?? row.industry ?? 'Custom Sample',
      quantity: Number(row.order_data ?? 1) || 1,
      source: row.ref_source ?? 'Inbound',
    }));

    res.json(createPaginatedResponse(sampleRequests, Number(total ?? 0), pagination));
  }),
);

router.get(
  '/api/requests/recent',
  asyncHandler(async (req, res) => {
    const limit = Math.min(100, Math.max(1, Number(req.query.limit ?? 25)));
    const rows = await query<RowDataPacket[]>(
      `
        SELECT
          r.id,
          r.user_id,
          r.company_id,
          r.request_date,
          r.status,
          r.ref_source
        FROM requests r
        ORDER BY r.request_date DESC
        ${buildLimitClause(limit)}
      `,
      [],
    );

    const payload = rows.map((row) => ({
      id: String(row.id),
      userId: row.user_id ? String(row.user_id) : null,
      companyId: row.company_id ? String(row.company_id) : null,
      requestedAt: toIso(row.request_date, new Date()),
      status: getSampleStatus(row.status),
      source: row.ref_source ?? 'Inbound',
    }));

    res.json(payload);
  }),
);

router.get(
  '/api/jobs',
  asyncHandler(async (req, res) => {
    const filters = jobFiltersSchema.parse(req.query);
    const pagination = parsePagination(req.query);
    const whereClauses = ['1=1'];
    const params: any[] = [];

    if (filters.status) {
      whereClauses.push(`${jobStatusExpression} = ?`);
      params.push(filters.status);
    }

    const rows = await query<RowDataPacket[]>(
      `
        SELECT
          j.id,
          j.user_id,
          j.company_id,
          j.job_id,
          j.order_total,
          j.payments,
          j.order_counts,
          j.edg,
          COALESCE(${jobStatusExpression}, 'estimating') AS status,
          req_meta.first_request,
          req_meta.last_processed
        FROM user_jobs j
        LEFT JOIN (
          SELECT job_id, MIN(request_date) AS first_request, MAX(processed_date) AS last_processed
          FROM requests
          WHERE job_id IS NOT NULL
          GROUP BY job_id
        ) req_meta ON req_meta.job_id = j.job_id
        WHERE ${whereClauses.join(' AND ')}
        ORDER BY req_meta.last_processed DESC, j.id DESC
        ${buildLimitClause(pagination.pageSize, pagination.offset)}
      `,
      params,
    );

    const [{ total }] = await query<RowDataPacket[]>(
      `SELECT COUNT(*) AS total FROM user_jobs j WHERE ${whereClauses.join(' AND ')}`,
      params,
    );

    const jobs: Job[] = rows.map((row) => ({
      id: String(row.id),
      companyId: row.company_id ? String(row.company_id) : 'unassigned',
      prospectId: row.user_id ? String(row.user_id) : null,
      description: row.job_id ? `Project ${row.job_id}` : 'Production Run',
      status: (row.status ?? 'estimating') as Job['status'],
      estimatedValue: Number(row.order_total ?? 0),
      actualValue: row.payments !== null ? Number(row.payments) : null,
      createdAt: toIso(row.first_request, new Date(Date.now() - 14 * 24 * 60 * 60 * 1000)),
      dueDate: toIsoOrNull(row.last_processed),
      conversionFromSampleDays: differenceInDays(row.last_processed, row.first_request),
    }));

    res.json(createPaginatedResponse(jobs, Number(total ?? 0), pagination));
  }),
);

router.get(
  '/api/jobs/summary',
  asyncHandler(async (_req, res) => {
    const rows = await query<RowDataPacket[]>(
      `
        SELECT
          j.company_id,
          COALESCE(c.company, 'Unassigned') AS company_name,
          COUNT(*) AS jobs,
          COALESCE(SUM(j.order_total), 0) AS revenue
        FROM user_jobs j
        LEFT JOIN users_company c ON c.id = j.company_id
        GROUP BY j.company_id, c.company
        ORDER BY revenue DESC
        LIMIT 50
      `,
    );

    const summary = rows.map((row) => ({
      companyId: row.company_id ? String(row.company_id) : 'unassigned',
      companyName: row.company_name ?? 'Unassigned',
      jobs: Number(row.jobs ?? 0),
      revenue: Math.round(Number(row.revenue ?? 0)),
    }));

    res.json(summary);
  }),
);

router.get(
  '/api/tasks',
  asyncHandler(async (req, res) => {
    const filters = taskFiltersSchema.parse(req.query);
    const pagination = parsePagination(req.query);
    const whereClauses = ['rn.text IS NOT NULL', "rn.text <> ''"];
    const params: any[] = [];

    if (filters.status) {
      whereClauses.push(`${taskStatusExpression} = ?`);
      params.push(filters.status);
    }

    if (filters.owner) {
      whereClauses.push(`LOWER(CONCAT(u.first_name, ' ', u.last_name)) = ?`);
      params.push(filters.owner.toLowerCase());
    }

    const rows = await query<RowDataPacket[]>(
      `
        SELECT
          rn.id,
          rn.company_id,
          rn.request_id,
          rn.author_id,
          rn.type,
          rn.removed,
          rn.date,
          ${taskStatusExpression} AS status,
          ${taskPriorityExpression} AS priority,
          u.first_name,
          u.last_name
        FROM request_notes rn
        LEFT JOIN users u ON u.id = rn.author_id
        WHERE ${whereClauses.join(' AND ')}
        ORDER BY rn.date DESC
        ${buildLimitClause(pagination.pageSize, pagination.offset)}
      `,
      params,
    );

    const [{ total }] = await query<RowDataPacket[]>(
      `SELECT COUNT(*) AS total FROM request_notes rn LEFT JOIN users u ON u.id = rn.author_id WHERE ${whereClauses.join(' AND ')}`,
      params,
    );

    const tasks: Task[] = rows.map((row) => ({
      id: String(row.id),
      prospectId: row.author_id ? String(row.author_id) : null,
      companyId: row.company_id ? String(row.company_id) : null,
      title: row.type ? `Follow-up: ${row.type}` : 'Account Task',
      description: row.type ?? undefined,
      status: (row.status ?? 'open') as Task['status'],
      priority: (row.priority ?? 'medium') as Task['priority'],
      dueDate: null,
      owner: buildOwnerName(row.first_name, row.last_name),
      createdAt: toIso(row.date, new Date()),
      updatedAt: toIso(row.date, new Date()),
    }));

    res.json(createPaginatedResponse(tasks, Number(total ?? 0), pagination));
  }),
);

router.get(
  '/api/activities',
  asyncHandler(async (req, res) => {
    const pagination = parsePagination(req.query);
    const rows = await query<RowDataPacket[]>(
      `
        SELECT
          rn.id,
          rn.company_id,
          rn.request_id,
          rn.author_id,
          rn.type,
          rn.date,
          rn.text,
          u.first_name,
          u.last_name
        FROM request_notes rn
        LEFT JOIN users u ON u.id = rn.author_id
        WHERE rn.text IS NOT NULL AND rn.text <> ''
        ORDER BY rn.date DESC
        ${buildLimitClause(pagination.pageSize, pagination.offset)}
      `,
      [],
    );

    const [{ total }] = await query<RowDataPacket[]>(`SELECT COUNT(*) AS total FROM request_notes WHERE text IS NOT NULL AND text <> ''`);

    const activities: Activity[] = rows.map((row) => ({
      id: String(row.id),
      prospectId: row.author_id ? String(row.author_id) : null,
      companyId: row.company_id ? String(row.company_id) : 'unassigned',
      actor: buildOwnerName(row.first_name, row.last_name),
      type: row.type ?? 'note',
      occurredAt: toIso(row.date, new Date()),
      notes: row.text ?? undefined,
    }));

    res.json(createPaginatedResponse(activities, Number(total ?? 0), pagination));
  }),
);

router.get(
  '/api/saved-filters',
  asyncHandler(async (req, res) => {
    const { view } = savedFilterViewSchema.parse(req.query);
    const filters = await buildSavedFilters(view);
    res.json(filters);
  }),
);

router.get(
  '/api/metrics/overview',
  asyncHandler(async (_req, res) => {
    const [prospectsCount] = await query<RowDataPacket[]>(`SELECT COUNT(*) AS total FROM users`);
    const [companiesCount] = await query<RowDataPacket[]>(`SELECT COUNT(*) AS total FROM users_company`);
    const [sampleRequestsCount] = await query<RowDataPacket[]>(`SELECT COUNT(*) AS total FROM requests`);
    const [openTasksCount] = await query<RowDataPacket[]>(`SELECT COUNT(*) AS total FROM request_notes WHERE removed = 0`);
    const [openJobsCount] = await query<RowDataPacket[]>(
      `SELECT SUM(CASE WHEN ${jobStatusExpression} = 'completed' THEN 0 ELSE 1 END) AS total FROM user_jobs j`,
    );
    const [recentRevenue] = await query<RowDataPacket[]>(
      `SELECT COALESCE(SUM(summ), 0) AS total FROM payment_history WHERE date >= DATE_SUB(NOW(), INTERVAL 90 DAY)`,
    );

    const prospectTrend = await getMonthlySeries(
      `
        SELECT DATE_FORMAT(request_date, '%Y-%m') AS bucket, COUNT(*) AS total
        FROM requests
        WHERE request_date >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
        GROUP BY bucket
      `,
      [],
      5,
    );

    const jobTrend = await getMonthlySeries(
      `
        SELECT DATE_FORMAT(request_date, '%Y-%m') AS bucket, COUNT(*) AS total
        FROM requests
        WHERE job_id IS NOT NULL AND request_date >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
        GROUP BY bucket
      `,
      [],
      5,
    );

    const revenueTrend = await getMonthlySeries(
      `
        SELECT DATE_FORMAT(date, '%Y-%m') AS bucket, SUM(summ) AS total
        FROM payment_history
        WHERE date >= DATE_SUB(NOW(), INTERVAL 5 MONTH)
        GROUP BY bucket
      `,
      [],
      5,
    );

    const overview: MetricsOverviewResponse = {
      kpis: [
        {
          label: 'Active Prospects',
          value: Number(prospectsCount?.total ?? 0),
          change: computeTrendChange(prospectTrend),
          trend: prospectTrend,
        },
        {
          label: 'Open Jobs',
          value: Number(openJobsCount?.total ?? 0),
          change: computeTrendChange(jobTrend),
          trend: jobTrend,
        },
        {
          label: 'Revenue (90d)',
          value: Math.round(Number(recentRevenue?.total ?? 0)),
          change: computeTrendChange(revenueTrend),
          trend: revenueTrend,
        },
      ],
      summaries: {
        prospects: Number(prospectsCount?.total ?? 0),
        companies: Number(companiesCount?.total ?? 0),
        sampleRequests: Number(sampleRequestsCount?.total ?? 0),
        openTasks: Number(openTasksCount?.total ?? 0),
      },
    };

    res.json(overview);
  }),
);

router.get(
  '/api/metrics/sample-requests',
  asyncHandler(async (_req, res) => {
    const rows = await query<RowDataPacket[]>(
      `
        SELECT status, COUNT(*) AS total
        FROM requests
        GROUP BY status
      `,
    );

    const map: Record<SampleRequest['status'], number> = {
      pending: 0,
      in_transit: 0,
      delivered: 0,
      cancelled: 0,
    };

    rows.forEach((row) => {
      const status = getSampleStatus(row.status);
      map[status] += Number(row.total ?? 0);
    });

    const metrics: SampleMetric[] = Object.entries(map).map(([status, count]) => ({
      status,
      count,
    }));

    res.json(metrics);
  }),
);

router.get(
  '/api/metrics/conversion',
  asyncHandler(async (_req, res) => {
    const [requestStats] = await query<RowDataPacket[]>(
      `
        SELECT
          COUNT(*) AS total,
          SUM(CASE WHEN status IN (2,3,4) THEN 1 ELSE 0 END) AS delivered
        FROM requests
      `,
    );
    const [jobStats] = await query<RowDataPacket[]>(`SELECT COUNT(*) AS total FROM user_jobs`);
    const [revenueStats] = await query<RowDataPacket[]>(
      `SELECT COUNT(*) AS total FROM payment_history WHERE summ IS NOT NULL AND summ > 0`,
    );

    const totalSamples = Number(requestStats?.total ?? 0);
    const delivered = Number(requestStats?.delivered ?? 0);
    const jobs = Number(jobStats?.total ?? 0);
    const revenueWins = Number(revenueStats?.total ?? 0);

    const conversion: ConversionMetric[] = [
      { stage: 'Sample Requested', rate: totalSamples ? 1 : 0 },
      { stage: 'Sample Delivered', rate: totalSamples ? Number((delivered / totalSamples).toFixed(2)) : 0 },
      { stage: 'Job Created', rate: delivered ? Number((jobs / delivered).toFixed(2)) : 0 },
      { stage: 'Revenue Won', rate: jobs ? Number((revenueWins / jobs).toFixed(2)) : 0 },
    ];

    res.json(conversion);
  }),
);

router.get(
  '/api/metrics/revenue',
  asyncHandler(async (_req, res) => {
    const rows = await query<RowDataPacket[]>(
      `
        SELECT DATE_FORMAT(date, '%Y-%m') AS bucket, SUM(summ) AS total
        FROM payment_history
        WHERE date >= DATE_SUB(NOW(), INTERVAL 5 MONTH)
        GROUP BY bucket
      `,
    );

    const now = new Date();
    const metrics: RevenueMetric[] = [];

    for (let index = 5; index >= 0; index -= 1) {
      const bucketDate = new Date(Date.UTC(now.getUTCFullYear(), now.getUTCMonth() - index, 1));
      const bucketKey = bucketDate.toISOString().slice(0, 7);
      const match = rows.find((row) => row.bucket === bucketKey);
      const revenue = Number(match?.total ?? 0);
      metrics.push({
        timeframe: bucketDate.toLocaleString('en-US', { month: 'short', year: 'numeric' }),
        revenue: Math.round(revenue),
      });
    }

    res.json(metrics);
  }),
);

router.get(
  '/api/payments/recent',
  asyncHandler(async (req, res) => {
    const limit = Math.min(100, Math.max(1, Number(req.query.limit ?? 50)));
    const rows = await query<RowDataPacket[]>(
      `
        SELECT
          p.id,
          p.job_id,
          p.client_id,
          p.date,
          p.summ,
          p.description
        FROM payment_history p
        ORDER BY p.date DESC
        ${buildLimitClause(limit)}
      `,
      [],
    );

    const payments = rows.map((row) => ({
      id: String(row.id),
      jobId: row.job_id ? String(row.job_id) : null,
      companyId: row.client_id ? String(row.client_id) : null,
      amount: Number(row.summ ?? 0),
      date: toIso(row.date, new Date()),
      description: row.description ?? '',
    }));

    res.json(payments);
  }),
);

router.use((error: unknown, _req: Request, res: Response, _next: NextFunction) => {
  // eslint-disable-next-line no-console
  console.error('[api] unhandled error:', error);
  res.status(500).json({ message: 'Internal server error' });
});

export default router;
