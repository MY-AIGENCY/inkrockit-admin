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

function buildQueryString(params?: Record<string, string | undefined>): string {
  if (!params) return '';
  const searchParams = new URLSearchParams();
  Object.entries(params).forEach(([key, value]) => {
    if (value) searchParams.append(key, value);
  });
  const queryString = searchParams.toString();
  return queryString ? `?${queryString}` : '';
}

async function fetchWithFallback<T>(path: APIPath, fallback: T, params?: Record<string, string | undefined>): Promise<T> {
  try {
    const queryString = buildQueryString(params);
    const response = await fetch(`${API_BASE_URL}${path}${queryString}`, {
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

export async function getOverviewMetrics(params?: { stage?: string; region?: string }) {
  return fetchWithFallback<MetricsOverviewResponse>('/api/metrics/overview', fallbackOverview, params);
}

export async function getSampleMetrics(params?: { stage?: string; region?: string }) {
  return fetchWithFallback<SampleMetric[]>('/api/metrics/sample-requests', fallbackSampleMetrics, params);
}

export async function getConversionMetrics(params?: { stage?: string; region?: string }) {
  return fetchWithFallback<ConversionMetric[]>('/api/metrics/conversion', fallbackConversionMetrics, params);
}

export async function getRevenueMetrics(params?: { stage?: string; region?: string }) {
  return fetchWithFallback<RevenueMetric[]>('/api/metrics/revenue', fallbackRevenueSeries, params);
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
