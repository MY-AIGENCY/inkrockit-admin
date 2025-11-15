import type {
  Activity,
  Company,
  Job,
  Payment,
  Prospect,
  SampleRequest,
  SavedFilter,
  Task,
} from '@inkrockit/types';

const now = new Date();

export const companies: Company[] = [
  {
    id: 'cmp_1',
    name: 'Northwind Builders',
    industry: 'Commercial Construction',
    segment: 'mid_market',
    region: 'West',
    headquarters: {
      line1: '123 Harbor Way',
      line2: null,
      city: 'San Francisco',
      state: 'CA',
      postalCode: '94105',
      country: 'US',
    },
    totalSampleRequests: 8,
    totalJobs: 3,
    totalRevenue: 425000,
    lastEngagementDate: now.toISOString(),
    healthScore: 78,
    owner: 'Ava Patel',
  },
  {
    id: 'cmp_2',
    name: 'Atlas Engineering',
    industry: 'Infrastructure',
    segment: 'enterprise',
    region: 'South',
    headquarters: {
      line1: '88 Stonewall Ave',
      line2: 'Suite 500',
      city: 'Austin',
      state: 'TX',
      postalCode: '78701',
      country: 'US',
    },
    totalSampleRequests: 12,
    totalJobs: 5,
    totalRevenue: 910000,
    lastEngagementDate: new Date(now.getTime() - 86400000 * 5).toISOString(),
    healthScore: 84,
    owner: 'Maya Chen',
  },
];

export const prospects: Prospect[] = [
  {
    id: 'pr_1',
    firstName: 'Caleb',
    lastName: 'Ramirez',
    email: 'caleb.ramirez@example.com',
    phone: '555-123-9988',
    title: 'Project Manager',
    companyId: 'cmp_1',
    source: 'Trade Show',
    persona: 'Specifier',
    stage: 'sample_sent',
    region: 'West',
    industry: 'Commercial Construction',
    createdAt: now.toISOString(),
    lastActivityAt: now.toISOString(),
    engagementScore: 72,
    sampleRequests: 2,
    conversionLikelihood: 0.64,
  },
  {
    id: 'pr_2',
    firstName: 'Isabella',
    lastName: 'Ford',
    email: 'isabella.ford@example.com',
    phone: '555-552-4490',
    title: 'Director of Operations',
    companyId: 'cmp_2',
    source: 'Inbound',
    persona: 'Decision Maker',
    stage: 'engaged',
    region: 'South',
    industry: 'Infrastructure',
    createdAt: now.toISOString(),
    lastActivityAt: new Date(now.getTime() - 86400000).toISOString(),
    engagementScore: 81,
    sampleRequests: 1,
    conversionLikelihood: 0.52,
  },
];

export const sampleRequests: SampleRequest[] = [
  {
    id: 'sr_1',
    prospectId: 'pr_1',
    companyId: 'cmp_1',
    requestedAt: now.toISOString(),
    fulfilledAt: now.toISOString(),
    status: 'delivered',
    shippingCarrier: 'FedEx',
    shippingTracking: '123456789',
    material: 'Stone Veneer',
    quantity: 8,
    source: 'Trade Show',
  },
  {
    id: 'sr_2',
    prospectId: 'pr_2',
    companyId: 'cmp_2',
    requestedAt: new Date(now.getTime() - 86400000 * 3).toISOString(),
    fulfilledAt: null,
    status: 'in_transit',
    shippingCarrier: 'UPS',
    shippingTracking: '998877665',
    material: 'Polished Concrete',
    quantity: 5,
    source: 'Website',
  },
];

export const jobs: Job[] = [
  {
    id: 'job_1',
    companyId: 'cmp_1',
    prospectId: 'pr_1',
    description: 'Flagship lobby renovation',
    status: 'in_production',
    estimatedValue: 350000,
    actualValue: 320000,
    createdAt: new Date(now.getTime() - 86400000 * 20).toISOString(),
    dueDate: new Date(now.getTime() + 86400000 * 10).toISOString(),
    conversionFromSampleDays: 14,
  },
  {
    id: 'job_2',
    companyId: 'cmp_2',
    prospectId: 'pr_2',
    description: 'Bridge cladding pilot',
    status: 'estimating',
    estimatedValue: 560000,
    actualValue: null,
    createdAt: new Date(now.getTime() - 86400000 * 5).toISOString(),
    dueDate: null,
    conversionFromSampleDays: null,
  },
];

export const tasks: Task[] = [
  {
    id: 'tsk_1',
    prospectId: 'pr_1',
    companyId: 'cmp_1',
    title: 'Send updated pricing workbook',
    description: 'Include new catalog section for terrazzo finishes.',
    status: 'open',
    priority: 'high',
    dueDate: new Date(now.getTime() + 86400000).toISOString(),
    owner: 'Ava Patel',
    createdAt: now.toISOString(),
    updatedAt: now.toISOString(),
  },
  {
    id: 'tsk_2',
    prospectId: 'pr_2',
    companyId: 'cmp_2',
    title: 'Schedule sample walkthrough',
    description: 'Loop in manufacturing lead for feasibility questions.',
    status: 'in_progress',
    priority: 'medium',
    dueDate: new Date(now.getTime() + 86400000 * 2).toISOString(),
    owner: 'Maya Chen',
    createdAt: now.toISOString(),
    updatedAt: now.toISOString(),
  },
];

export const payments: Payment[] = [
  {
    id: 'pay_1',
    jobId: 'job_1',
    companyId: 'cmp_1',
    amount: 150000,
    currency: 'USD',
    paidAt: new Date(now.getTime() - 86400000 * 2).toISOString(),
    method: 'ach',
    status: 'succeeded',
  },
];

export const activities: Activity[] = [
  {
    id: 'act_1',
    prospectId: 'pr_1',
    companyId: 'cmp_1',
    actor: 'Ava Patel',
    type: 'call',
    occurredAt: now.toISOString(),
    notes: 'Reviewed structural details and shipping timeline.',
  },
];

export let savedFilters: SavedFilter[] = [
  {
    id: 'flt_1',
    view: 'prospects',
    name: 'West Coast Active',
    createdBy: 'Ava Patel',
    filters: {
      regions: ['West'],
      stages: ['engaged', 'sample_sent'],
    },
    isDefault: true,
    createdAt: now.toISOString(),
  },
];
