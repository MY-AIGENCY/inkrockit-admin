import { Suspense } from 'react';
import { GlobalFilterBar } from '@/components/global-filter-bar';
import { ResourceTable } from '@/components/resource-table';
import { Card, CardContent, CardHeader, CardTitle } from '@inkrockit/ui';
import { getProspects } from '@/lib/server-api';

export default async function ProspectDashboardPage() {
  const prospects = await getProspects();

  const columns = [
    { key: 'name', header: 'Prospect', render: (row: any) => `${row.firstName} ${row.lastName}` },
    { key: 'company', header: 'Company', render: (row: any) => row.companyId },
    { key: 'stage', header: 'Stage', render: (row: any) => row.stage },
    { key: 'score', header: 'Engagement', render: (row: any) => `${row.engagementScore}` },
  ];

  const avgScore = (
    prospects.data.reduce((total, row) => total + row.engagementScore, 0) /
    Math.max(1, prospects.data.length)
  ).toFixed(1);

  return (
    <div className="space-y-8">
      <Suspense fallback={null}>
        <GlobalFilterBar pathname="/dashboard/prospects" />
      </Suspense>
      <Card>
        <CardHeader>
          <CardTitle>Engagement Overview</CardTitle>
        </CardHeader>
        <CardContent>
          <dl className="grid gap-4 sm:grid-cols-3">
            <div>
              <dt className="text-sm text-slate-500">Active Prospects</dt>
              <dd className="text-2xl font-semibold text-slate-900">{prospects.total}</dd>
            </div>
            <div>
              <dt className="text-sm text-slate-500">Average Engagement</dt>
              <dd className="text-2xl font-semibold text-slate-900">{avgScore}</dd>
            </div>
            <div>
              <dt className="text-sm text-slate-500">Sample Requests</dt>
              <dd className="text-2xl font-semibold text-slate-900">
                {prospects.data.reduce((total, row) => total + row.sampleRequests, 0)}
              </dd>
            </div>
          </dl>
        </CardContent>
      </Card>
      <ResourceTable columns={columns} data={prospects.data} emptyLabel="No prospects available" />
    </div>
  );
}
