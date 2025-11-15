import { GlobalFilterBar } from '@/components/global-filter-bar';
import { ResourceTable } from '@/components/resource-table';
import { Card, CardContent, CardHeader, CardTitle } from '@inkrockit/ui';
import { getTasks } from '@/lib/server-api';

export default async function TasksDashboardPage() {
  const tasks = await getTasks();

  const columns = [
    { key: 'title', header: 'Task', render: (row: any) => row.title },
    { key: 'owner', header: 'Owner', render: (row: any) => row.owner },
    { key: 'status', header: 'Status', render: (row: any) => row.status },
    { key: 'due', header: 'Due Date', render: (row: any) => row.dueDate ?? 'TBD' },
  ];

  const overdue = tasks.data.filter((task) => task.status !== 'completed' && task.dueDate).length;

  return (
    <div className="space-y-8">
      <GlobalFilterBar />
      <Card>
        <CardHeader>
          <CardTitle>Action Items</CardTitle>
        </CardHeader>
        <CardContent>
          <dl className="grid gap-4 sm:grid-cols-3">
            <div>
              <dt className="text-sm text-slate-500">Open Tasks</dt>
              <dd className="text-2xl font-semibold text-slate-900">
                {tasks.data.filter((task) => task.status !== 'completed').length}
              </dd>
            </div>
            <div>
              <dt className="text-sm text-slate-500">Overdue</dt>
              <dd className="text-2xl font-semibold text-slate-900">{overdue}</dd>
            </div>
            <div>
              <dt className="text-sm text-slate-500">High Priority</dt>
              <dd className="text-2xl font-semibold text-slate-900">
                {tasks.data.filter((task) => task.priority === 'high').length}
              </dd>
            </div>
          </dl>
        </CardContent>
      </Card>
      <ResourceTable columns={columns} data={tasks.data} emptyLabel="No open tasks" />
    </div>
  );
}
