import { ResourceTable } from '@/components/resource-table';
import { getCompanies } from '@/lib/server-api';

export default async function CompaniesPage() {
  const companies = await getCompanies();

  const columns = [
    { key: 'name', header: 'Company', render: (row: any) => row.name },
    { key: 'industry', header: 'Industry', render: (row: any) => row.industry },
    { key: 'segment', header: 'Segment', render: (row: any) => row.segment },
    { key: 'owner', header: 'Owner', render: (row: any) => row.owner },
    { key: 'revenue', header: 'Revenue', render: (row: any) => `$${row.totalRevenue.toLocaleString()}` },
  ];

  return <ResourceTable columns={columns} data={companies.data} emptyLabel="No companies available" />;
}
