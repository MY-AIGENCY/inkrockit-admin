import { Router } from 'express';
import { z } from 'zod';
import { savedFilterSchema, taskSchema } from '@inkrockit/types';
import {
  activities,
  companies,
  jobs,
  payments,
  prospects,
  sampleRequests,
  tasks,
  savedFilters,
} from '../data/mockData';
import { paginate } from '../lib/pagination';
import { filterProspects, filterSamples, filterJobs, filterTasks } from '../lib/filters';

const router = Router();

router.get('/healthz', (_req, res) => {
  res.json({ status: 'ok', timestamp: new Date().toISOString() });
});

router.get('/api/prospects', (req, res) => {
  const filtered = filterProspects(prospects, req.query);
  res.json(paginate(filtered, req.query));
});

router.get('/api/prospects/:id', (req, res) => {
  const prospect = prospects.find((item) => item.id === req.params.id);
  if (!prospect) {
    return res.status(404).json({ message: 'Prospect not found' });
  }
  res.json(prospect);
});

router.get('/api/companies', (req, res) => {
  res.json(paginate(companies, req.query));
});

router.get('/api/companies/:id', (req, res) => {
  const company = companies.find((item) => item.id === req.params.id);
  if (!company) {
    return res.status(404).json({ message: 'Company not found' });
  }
  res.json(company);
});

router.get('/api/sample-requests', (req, res) => {
  const filtered = filterSamples(sampleRequests, req.query);
  res.json(paginate(filtered, req.query));
});

router.get('/api/sample-requests/:id', (req, res) => {
  const sample = sampleRequests.find((item) => item.id === req.params.id);
  if (!sample) {
    return res.status(404).json({ message: 'Sample request not found' });
  }
  res.json(sample);
});

router.get('/api/jobs', (req, res) => {
  const filtered = filterJobs(jobs, req.query);
  res.json(paginate(filtered, req.query));
});

router.get('/api/jobs/:id', (req, res) => {
  const job = jobs.find((item) => item.id === req.params.id);
  if (!job) {
    return res.status(404).json({ message: 'Job not found' });
  }
  res.json(job);
});

router.get('/api/payments', (req, res) => {
  res.json(paginate(payments, req.query));
});

router.get('/api/activities', (req, res) => {
  res.json(paginate(activities, req.query));
});

router.get('/api/tasks', (req, res) => {
  const filtered = filterTasks(tasks, req.query);
  res.json(paginate(filtered, req.query));
});

router.get('/api/tasks/:id', (req, res) => {
  const task = tasks.find((item) => item.id === req.params.id);
  if (!task) {
    return res.status(404).json({ message: 'Task not found' });
  }
  res.json(task);
});

const taskUpdateSchema = z.object({
  status: taskSchema.shape.status.optional(),
  priority: taskSchema.shape.priority.optional(),
  dueDate: taskSchema.shape.dueDate.optional(),
});

router.patch('/api/tasks/:id', (req, res) => {
  const task = tasks.find((item) => item.id === req.params.id);
  if (!task) {
    return res.status(404).json({ message: 'Task not found' });
  }

  const parsed = taskUpdateSchema.safeParse(req.body);
  if (!parsed.success) {
    return res.status(400).json({ message: 'Invalid payload', issues: parsed.error.issues });
  }

  Object.assign(task, parsed.data, { updatedAt: new Date().toISOString() });
  res.json(task);
});

router.get('/api/saved-filters', (req, res) => {
  const { view } = req.query;
  if (view) {
    return res.json(savedFilters.filter((filter) => filter.view === view));
  }
  res.json(savedFilters);
});

router.post('/api/saved-filters', (req, res) => {
  const parsed = savedFilterSchema.omit({ id: true, createdAt: true }).safeParse(req.body);
  if (!parsed.success) {
    return res.status(400).json({ message: 'Invalid payload', issues: parsed.error.issues });
  }

  const newFilter = {
    ...parsed.data,
    id: `flt_${Date.now()}`,
    createdAt: new Date().toISOString(),
  };
  savedFilters.push(newFilter);
  res.status(201).json(newFilter);
});

router.put('/api/saved-filters/:id', (req, res) => {
  const filter = savedFilters.find((item) => item.id === req.params.id);
  if (!filter) {
    return res.status(404).json({ message: 'Saved filter not found' });
  }

  const parsed = savedFilterSchema.partial().safeParse(req.body);
  if (!parsed.success) {
    return res.status(400).json({ message: 'Invalid payload', issues: parsed.error.issues });
  }

  Object.assign(filter, parsed.data);
  res.json(filter);
});

router.delete('/api/saved-filters/:id', (req, res) => {
  const index = savedFilters.findIndex((item) => item.id === req.params.id);
  if (index === -1) {
    return res.status(404).json({ message: 'Saved filter not found' });
  }
  savedFilters.splice(index, 1);
  res.status(204).end();
});

router.get('/api/metrics/overview', (_req, res) => {
  res.json({
    kpis: [
      { label: 'Active Prospects', value: prospects.length, change: 12, trend: [2, 4, 5, 7, 8] },
      { label: 'Open Jobs', value: jobs.length, change: -4, trend: [8, 6, 5, 5, 4] },
      { label: 'Revenue (90d)', value: payments.reduce((sum, payment) => sum + payment.amount, 0), change: 8, trend: [120, 150, 170, 180, 190] },
    ],
    summaries: {
      prospects: prospects.length,
      companies: companies.length,
      sampleRequests: sampleRequests.length,
      openTasks: tasks.filter((task) => task.status !== 'completed').length,
    },
  });
});

router.get('/api/metrics/sample-requests', (_req, res) => {
  res.json(
    ['pending', 'in_transit', 'delivered', 'cancelled'].map((status) => ({
      status,
      count: sampleRequests.filter((sample) => sample.status === status).length,
    })),
  );
});

router.get('/api/metrics/conversion', (_req, res) => {
  const totalSamples = sampleRequests.length || 1;
  const delivered = sampleRequests.filter((sample) => sample.status === 'delivered').length;
  const jobsCreated = jobs.length;
  const revenueWon = payments.filter((payment) => payment.status === 'succeeded').length;

  res.json([
    { stage: 'Sample Requested', rate: totalSamples / totalSamples },
    { stage: 'Sample Delivered', rate: delivered / totalSamples },
    { stage: 'Job Created', rate: jobsCreated / totalSamples },
    { stage: 'Revenue Won', rate: revenueWon / totalSamples },
  ]);
});

router.get('/api/metrics/revenue', (_req, res) => {
  const series = Array.from({ length: 6 }).map((_, idx) => ({
    timeframe: `Week ${idx + 1}`,
    revenue: 25000 + idx * 5000,
  }));
  res.json(series);
});

export default router;
