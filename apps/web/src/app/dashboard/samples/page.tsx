import { Suspense } from 'react';
import { GlobalFilterBar } from '@/components/global-filter-bar';
import { ResourceTable } from '@/components/resource-table';
import { Card, CardContent, CardHeader, CardTitle } from '@inkrockit/ui';
import { getSampleRequests } from '@/lib/server-api';

export default async function SamplesDashboardPage() {
  const samples = await getSampleRequests();

  const columns = [
    { key: 'id', header: 'Request', render: (row: any) => row.id },
    { key: 'status', header: 'Status', render: (row: any) => row.status },
    { key: 'material', header: 'Material', render: (row: any) => row.material },
    { key: 'quantity', header: 'Quantity', render: (row: any) => row.quantity },
  ];

  const delivered = samples.data.filter((sample) => sample.status === 'delivered').length;

  return (
    <div className="space-y-8">
      <Suspense fallback={null}>
        <GlobalFilterBar pathname="/dashboard/samples" />
      </Suspense>
      <Card>
        <CardHeader>
          <CardTitle>Sample Logistics</CardTitle>
        </CardHeader>
        <CardContent>
          <dl className="grid gap-4 sm:grid-cols-3">
            <div>
              <dt className="text-sm text-slate-500">Total Requests</dt>
              <dd className="text-2xl font-semibold text-slate-900">{samples.total}</dd>
            </div>
            <div>
              <dt className="text-sm text-slate-500">Delivered</dt>
              <dd className="text-2xl font-semibold text-slate-900">{delivered}</dd>
            </div>
            <div>
              <dt className="text-sm text-slate-500">In Transit</dt>
              <dd className="text-2xl font-semibold text-slate-900">
                {samples.data.filter((sample) => sample.status === 'in_transit').length}
              </dd>
            </div>
          </dl>
        </CardContent>
      </Card>
      <ResourceTable columns={columns} data={samples.data} emptyLabel="No sample requests recorded" />
    </div>
  );
}
