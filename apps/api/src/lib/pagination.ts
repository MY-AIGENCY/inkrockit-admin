import type { PaginatedResponse } from '@inkrockit/types';

interface PaginationInput {
  page?: string | string[];
  pageSize?: string | string[];
}

export function paginate<T>(items: T[], query: PaginationInput): PaginatedResponse<T> {
  const page = Math.max(1, Number(Array.isArray(query.page) ? query.page[0] : query.page) || 1);
  const pageSize = Math.min(
    100,
    Math.max(1, Number(Array.isArray(query.pageSize) ? query.pageSize[0] : query.pageSize) || 25),
  );

  const start = (page - 1) * pageSize;
  const data = items.slice(start, start + pageSize);

  return {
    data,
    page,
    pageSize,
    total: items.length,
  };
}
