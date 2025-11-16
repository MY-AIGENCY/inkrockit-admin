import type { PaginatedResponse } from '@inkrockit/types';
import { paginationQuerySchema } from '@inkrockit/types';

export interface PaginationOptions {
  page?: number;
  pageSize?: number;
  offset?: number;
}

export function parsePagination(query: Record<string, unknown>): Required<PaginationOptions> {
  const { page, pageSize } = paginationQuerySchema.parse(query);
  const sanitizedPage = Math.max(1, page);
  const sanitizedPageSize = Math.min(100, Math.max(1, pageSize));

  return {
    page: sanitizedPage,
    pageSize: sanitizedPageSize,
    offset: (sanitizedPage - 1) * sanitizedPageSize,
  };
}

export function createPaginatedResponse<T>(
  data: T[],
  total: number,
  pagination: PaginationOptions,
): PaginatedResponse<T> {
  const page = pagination.page ?? 1;
  const pageSize = pagination.pageSize ?? (data.length || 1);

  return {
    data,
    total,
    page,
    pageSize,
  };
}
