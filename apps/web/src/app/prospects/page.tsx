import { ResourceTable } from '@/components/resource-table';
import { getProspects, getSavedFilters } from '@/lib/server-api';
import { Card, CardContent, CardHeader, CardTitle } from '@inkrockit/ui';

export default async function ProspectsPage() {
  const [prospects, savedFilters] = await Promise.all([getProspects(), getSavedFilters()]);

  const columns = [
    { key: 'name', header: 'Prospect', render: (row: any) => `${row.firstName} ${row.lastName}` },
    { key: 'persona', header: 'Persona', render: (row: any) => row.persona },
    { key: 'stage', header: 'Stage', render: (row: any) => row.stage },
    { key: 'company', header: 'Company', render: (row: any) => row.companyId },
    { key: 'engagement', header: 'Engagement', render: (row: any) => row.engagementScore },
  ];

  return (
    <div className="space-y-8">
      <Card>
        <CardHeader>
          <CardTitle>Saved Filters</CardTitle>
        </CardHeader>
        <CardContent>
          <ul className="flex flex-wrap gap-3 text-sm">
            {savedFilters.map((filter) => (
              <li key={filter.id} className="rounded-full border border-indigo-200 px-3 py-1 text-indigo-700">
                {filter.name}
              </li>
            ))}
          </ul>
        </CardContent>
      </Card>
      <ResourceTable columns={columns} data={prospects.data} emptyLabel="No prospects found" />
    </div>
  );
}
