import { notFound } from 'next/navigation';
import { Card, CardContent, CardHeader, CardTitle } from '@inkrockit/ui';
import { getTasks } from '@/lib/server-api';

interface Params {
  params: { id: string };
}

export default async function TaskDetailPage({ params }: Params) {
  const tasks = await getTasks();
  const task = tasks.data.find((item) => item.id === params.id);

  if (!task) {
    notFound();
  }

  return (
    <Card>
      <CardHeader>
        <CardTitle>{task.title}</CardTitle>
      </CardHeader>
      <CardContent>
        <dl className="grid gap-4 sm:grid-cols-2">
          <div>
            <dt className="text-sm text-slate-500">Owner</dt>
            <dd className="text-base text-slate-900">{task.owner}</dd>
          </div>
          <div>
            <dt className="text-sm text-slate-500">Status</dt>
            <dd className="text-base text-slate-900">{task.status}</dd>
          </div>
          <div>
            <dt className="text-sm text-slate-500">Priority</dt>
            <dd className="text-base text-slate-900">{task.priority}</dd>
          </div>
          <div>
            <dt className="text-sm text-slate-500">Due Date</dt>
            <dd className="text-base text-slate-900">{task.dueDate ?? 'TBD'}</dd>
          </div>
        </dl>
      </CardContent>
    </Card>
  );
}
