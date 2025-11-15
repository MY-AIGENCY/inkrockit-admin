import type {
  ConversionMetric,
  MetricsOverviewResponse,
  PaginatedResponse,
  Prospect,
  Company,
  SampleRequest,
  Job,
  Task,
  Activity,
  SavedFilter,
  RevenueMetric,
  SampleMetric,
} from '@inkrockit/types';
import {
  fallbackActivities,
  fallbackCompanies,
  fallbackConversionMetrics,
  fallbackJobs,
  fallbackOverview,
  fallbackProspects,
  fallbackRevenueSeries,
  fallbackSampleMetrics,
  fallbackSampleRequests,
  fallbackSavedFilters,
  fallbackTasks,
} from './fallback-data';

const API_BASE_URL =
  process.env.API_BASE_URL ?? process.env.NEXT_PUBLIC_API_BASE_URL ?? 'http://localhost:4000';

type APIPath =
  | '/api/metrics/overview'
  | '/api/metrics/sample-requests'
  | '/api/metrics/conversion'
  | '/api/metrics/revenue'
  | '/api/prospects'
  | '/api/companies'
  | '/api/sample-requests'
  | '/api/jobs'
  | '/api/tasks'
  | '/api/activities'
  | '/api/saved-filters';

async function fetchWithFallback<T>(path: APIPath, fallback: T): Promise<T> {
  try {
    const response = await fetch(`${API_BASE_URL}${path}`, {
      next: { revalidate: 0 },
    });

    if (!response.ok) {
      throw new Error('Failed to fetch');
    }

    return (await response.json()) as T;
  } catch (error) {
    console.warn(`Falling back to mocked payload for ${path}:`, error);
    return fallback;
  }
}

export async function getOverviewMetrics() {
  return fetchWithFallback<MetricsOverviewResponse>('/api/metrics/overview', fallbackOverview);
}

export async function getSampleMetrics() {
  return fetchWithFallback<SampleMetric[]>('/api/metrics/sample-requests', fallbackSampleMetrics);
}

export async function getConversionMetrics() {
  return fetchWithFallback<ConversionMetric[]>('/api/metrics/conversion', fallbackConversionMetrics);
}

export async function getRevenueMetrics() {
  return fetchWithFallback<RevenueMetric[]>('/api/metrics/revenue', fallbackRevenueSeries);
}

export async function getProspects() {
  return fetchWithFallback<PaginatedResponse<Prospect>>('/api/prospects', fallbackProspects);
}

export async function getCompanies() {
  return fetchWithFallback<PaginatedResponse<Company>>('/api/companies', fallbackCompanies);
}

export async function getSampleRequests() {
  return fetchWithFallback<PaginatedResponse<SampleRequest>>(
    '/api/sample-requests',
    fallbackSampleRequests,
  );
}

export async function getJobs() {
  return fetchWithFallback<PaginatedResponse<Job>>('/api/jobs', fallbackJobs);
}

export async function getTasks() {
  return fetchWithFallback<PaginatedResponse<Task>>('/api/tasks', fallbackTasks);
}

export async function getActivities() {
  return fetchWithFallback<PaginatedResponse<Activity>>('/api/activities', fallbackActivities);
}

export async function getSavedFilters() {
  return fetchWithFallback<SavedFilter[]>('/api/saved-filters', fallbackSavedFilters);
}
