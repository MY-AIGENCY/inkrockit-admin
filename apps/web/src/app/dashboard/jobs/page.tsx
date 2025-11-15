import { GlobalFilterBar } from '@/components/global-filter-bar';
import { ResourceTable } from '@/components/resource-table';
import { Card, CardContent, CardHeader, CardTitle } from '@inkrockit/ui';
import { getJobs } from '@/lib/server-api';

export default async function JobsDashboardPage() {
  const jobs = await getJobs();

  const columns = [
    { key: 'id', header: 'Job', render: (row: any) => row.id },
    { key: 'status', header: 'Status', render: (row: any) => row.status },
    { key: 'value', header: 'Est. Value', render: (row: any) => `$${row.estimatedValue.toLocaleString()}` },
    { key: 'dueDate', header: 'Due Date', render: (row: any) => row.dueDate ?? 'TBD' },
  ];

  const totalValue = jobs.data.reduce((total, row) => total + row.estimatedValue, 0);

  return (
    <div className="space-y-8">
      <GlobalFilterBar />
      <Card>
        <CardHeader>
          <CardTitle>Production Load</CardTitle>
        </CardHeader>
        <CardContent>
          <dl className="grid gap-4 sm:grid-cols-3">
            <div>
              <dt className="text-sm text-slate-500">Jobs</dt>
              <dd className="text-2xl font-semibold text-slate-900">{jobs.total}</dd>
            </div>
            <div>
              <dt className="text-sm text-slate-500">Total Value</dt>
              <dd className="text-2xl font-semibold text-slate-900">${totalValue.toLocaleString()}</dd>
            </div>
            <div>
              <dt className="text-sm text-slate-500">In Production</dt>
              <dd className="text-2xl font-semibold text-slate-900">
                {jobs.data.filter((job) => job.status === 'in_production').length}
              </dd>
            </div>
          </dl>
        </CardContent>
      </Card>
      <ResourceTable columns={columns} data={jobs.data} emptyLabel="No jobs in the system" />
    </div>
  );
}
