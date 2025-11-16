import { ResourceTable } from '@/components/resource-table';
import { getJobs } from '@/lib/server-api';

export default async function JobsPage() {
  const jobs = await getJobs();

  const columns = [
    { key: 'description', header: 'Job', render: (row: any) => row.description },
    { key: 'status', header: 'Status', render: (row: any) => row.status },
    { key: 'value', header: 'Est. Value', render: (row: any) => `$${row.estimatedValue.toLocaleString()}` },
    { key: 'conversion', header: 'Conversion Days', render: (row: any) => row.conversionFromSampleDays ?? '—' },
  ];

  return <ResourceTable columns={columns} data={jobs.data} emptyLabel="No jobs" />;
}
