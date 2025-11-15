import type { Prospect, SampleRequest, Job, Task } from '@inkrockit/types';

export function filterProspects(items: Prospect[], query: Record<string, any>) {
  let filtered = [...items];

  if (query.stage) {
    filtered = filtered.filter((item) => item.stage === query.stage);
  }

  if (query.region) {
    filtered = filtered.filter((item) => item.region === query.region);
  }

  if (query.search) {
    const value = (query.search as string).toLowerCase();
    filtered = filtered.filter((item) =>
      `${item.firstName} ${item.lastName}`.toLowerCase().includes(value) ||
      item.email.toLowerCase().includes(value),
    );
  }

  return filtered;
}

export function filterSamples(items: SampleRequest[], query: Record<string, any>) {
  if (!query.status) {
    return items;
  }

  return items.filter((sample) => sample.status === query.status);
}

export function filterJobs(items: Job[], query: Record<string, any>) {
  if (!query.status) {
    return items;
  }

  return items.filter((job) => job.status === query.status);
}

export function filterTasks(items: Task[], query: Record<string, any>) {
  let filtered = [...items];

  if (query.status) {
    filtered = filtered.filter((task) => task.status === query.status);
  }

  if (query.owner) {
    filtered = filtered.filter((task) => task.owner === query.owner);
  }

  return filtered;
}
