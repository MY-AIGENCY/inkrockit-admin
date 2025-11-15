import { ResourceTable } from '@/components/resource-table';
import { getSampleRequests } from '@/lib/server-api';

export default async function SampleRequestsPage() {
  const samples = await getSampleRequests();

  const columns = [
    { key: 'id', header: 'Request', render: (row: any) => row.id },
    { key: 'status', header: 'Status', render: (row: any) => row.status },
    { key: 'material', header: 'Material', render: (row: any) => row.material },
    { key: 'requestedAt', header: 'Requested', render: (row: any) => new Date(row.requestedAt).toLocaleDateString() },
  ];

  return <ResourceTable columns={columns} data={samples.data} emptyLabel="No sample requests" />;
}
