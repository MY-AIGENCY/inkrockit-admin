import { z } from 'zod';

export const paginationQuerySchema = z.object({
  page: z.coerce.number().int().min(1).default(1),
  pageSize: z.coerce.number().int().min(1).max(100).default(25),
  sortBy: z.string().optional(),
  sortDir: z.enum(['asc', 'desc']).default('desc'),
});

export const dateRangeSchema = z.object({
  from: z.string().datetime().optional(),
  to: z.string().datetime().optional(),
});

export const addressSchema = z.object({
  line1: z.string(),
  line2: z.string().nullish(),
  city: z.string(),
  state: z.string().length(2),
  postalCode: z.string(),
  country: z.string().default('US'),
});

export const companySchema = z.object({
  id: z.string(),
  name: z.string(),
  industry: z.string(),
  segment: z.enum(['enterprise', 'mid_market', 'smb']),
  region: z.string(),
  headquarters: addressSchema,
  totalSampleRequests: z.number(),
  totalJobs: z.number(),
  totalRevenue: z.number(),
  lastEngagementDate: z.string().datetime().nullable(),
  healthScore: z.number().min(0).max(100),
  owner: z.string(),
});

export const prospectSchema = z.object({
  id: z.string(),
  firstName: z.string(),
  lastName: z.string(),
  email: z.string().email(),
  phone: z.string().optional(),
  title: z.string().optional(),
  companyId: z.string(),
  source: z.string(),
  persona: z.string(),
  stage: z.enum(['new', 'engaged', 'sample_sent', 'job_created', 'customer']),
  region: z.string(),
  industry: z.string(),
  createdAt: z.string().datetime(),
  lastActivityAt: z.string().datetime().nullable(),
  engagementScore: z.number().min(0).max(100),
  sampleRequests: z.number().default(0),
  conversionLikelihood: z.number().min(0).max(1),
});

export const sampleRequestSchema = z.object({
  id: z.string(),
  prospectId: z.string(),
  companyId: z.string(),
  requestedAt: z.string().datetime(),
  fulfilledAt: z.string().datetime().nullable(),
  status: z.enum(['pending', 'in_transit', 'delivered', 'cancelled']),
  shippingCarrier: z.string().optional(),
  shippingTracking: z.string().optional(),
  material: z.string(),
  quantity: z.number(),
  source: z.string(),
});

export const jobSchema = z.object({
  id: z.string(),
  companyId: z.string(),
  prospectId: z.string().nullable(),
  description: z.string(),
  status: z.enum(['estimating', 'queued', 'in_production', 'completed', 'cancelled']),
  estimatedValue: z.number(),
  actualValue: z.number().nullable(),
  createdAt: z.string().datetime(),
  dueDate: z.string().datetime().nullable(),
  conversionFromSampleDays: z.number().nullable(),
});

export const paymentSchema = z.object({
  id: z.string(),
  jobId: z.string(),
  companyId: z.string(),
  amount: z.number(),
  currency: z.string().default('USD'),
  paidAt: z.string().datetime(),
  method: z.enum(['ach', 'credit_card', 'wire', 'check']),
  status: z.enum(['pending', 'succeeded', 'failed']),
});

export const activitySchema = z.object({
  id: z.string(),
  prospectId: z.string().nullable(),
  companyId: z.string(),
  actor: z.string(),
  type: z.string(),
  occurredAt: z.string().datetime(),
  notes: z.string().optional(),
});

export const taskSchema = z.object({
  id: z.string(),
  prospectId: z.string().nullable(),
  companyId: z.string().nullable(),
  title: z.string(),
  description: z.string().optional(),
  status: z.enum(['open', 'in_progress', 'completed', 'blocked']),
  priority: z.enum(['low', 'medium', 'high']),
  dueDate: z.string().datetime().nullable(),
  owner: z.string(),
  createdAt: z.string().datetime(),
  updatedAt: z.string().datetime(),
});

export const savedFilterSchema = z.object({
  id: z.string(),
  view: z.string(),
  name: z.string(),
  createdBy: z.string(),
  filters: z.record(z.string(), z.any()),
  isDefault: z.boolean().default(false),
  createdAt: z.string().datetime(),
});

export const overviewMetricSchema = z.object({
  label: z.string(),
  value: z.number(),
  change: z.number(),
  trend: z.array(z.number()),
});

export const sampleMetricSchema = z.object({
  status: z.string(),
  count: z.number(),
});

export const conversionMetricSchema = z.object({
  stage: z.string(),
  rate: z.number(),
});

export const revenueMetricSchema = z.object({
  timeframe: z.string(),
  revenue: z.number(),
});
