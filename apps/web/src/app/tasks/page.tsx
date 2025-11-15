import { ResourceTable } from '@/components/resource-table';
import { getTasks } from '@/lib/server-api';

export default async function TasksPage() {
  const tasks = await getTasks();

  const columns = [
    { key: 'title', header: 'Task', render: (row: any) => row.title },
    { key: 'owner', header: 'Owner', render: (row: any) => row.owner },
    { key: 'status', header: 'Status', render: (row: any) => row.status },
    { key: 'dueDate', header: 'Due Date', render: (row: any) => row.dueDate ?? 'TBD' },
  ];

  return <ResourceTable columns={columns} data={tasks.data} emptyLabel="No tasks" />;
}
