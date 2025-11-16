'use client';

import type { Route } from 'next';
import { useSearchParams, useRouter } from 'next/navigation';
import { useMemo } from 'react';

const stageOptions = ['new', 'engaged', 'sample_sent', 'job_created', 'customer'];
const regionOptions = ['West', 'Mountain', 'Midwest', 'South', 'Northeast'];

type GlobalFilterBarProps = {
  pathname: Route;
};

export function GlobalFilterBar({ pathname }: GlobalFilterBarProps) {
  const router = useRouter();
  const searchParams = useSearchParams();

  const selectedStage = searchParams.get('stage') ?? 'all';
  const selectedRegion = searchParams.get('region') ?? 'all';

  const filterOptions = useMemo(
    () => [
      { label: 'Stage', options: stageOptions, selected: selectedStage, param: 'stage' },
      { label: 'Region', options: regionOptions, selected: selectedRegion, param: 'region' },
    ],
    [selectedStage, selectedRegion],
  );

  function updateParam(param: string, value: string) {
    const updated = new URLSearchParams(searchParams.toString());
    if (value === 'all') {
      updated.delete(param);
    } else {
      updated.set(param, value);
    }

    const queryString = updated.toString();
    const href = queryString ? (`${pathname}?${queryString}` as Route) : pathname;
    router.push(href);
  }

  return (
    <div className="mb-6 grid gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-4">
      {filterOptions.map((group) => (
        <label key={group.param} className="text-sm font-medium text-slate-600">
          {group.label}
          <select
            className="mt-2 w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            value={group.selected}
            onChange={(event) => updateParam(group.param, event.target.value)}
          >
            <option value="all">All</option>
            {group.options.map((option) => (
              <option key={option} value={option}>
                {option.replace('_', ' ').replace(/\b\w/g, (l) => l.toUpperCase())}
              </option>
            ))}
          </select>
        </label>
      ))}
    </div>
  );
}
