import { notFound } from 'next/navigation';
import { Card, CardContent, CardHeader, CardTitle } from '@inkrockit/ui';
import { getJobs, getTasks } from '@/lib/server-api';

interface Params {
  params: { id: string };
}

export default async function JobDetailPage({ params }: Params) {
  const [jobs, tasks] = await Promise.all([getJobs(), getTasks()]);
  const job = jobs.data.find((item) => item.id === params.id);

  if (!job) {
    notFound();
  }

  const relatedTasks = tasks.data.filter((task) => task.prospectId === job.prospectId);

  return (
    <div className="space-y-8">
      <Card>
        <CardHeader>
          <CardTitle>{job.description}</CardTitle>
        </CardHeader>
        <CardContent>
          <dl className="grid gap-4 sm:grid-cols-2">
            <div>
              <dt className="text-sm text-slate-500">Status</dt>
              <dd className="text-base text-slate-900">{job.status}</dd>
            </div>
            <div>
              <dt className="text-sm text-slate-500">Estimated Value</dt>
              <dd className="text-base text-slate-900">${job.estimatedValue.toLocaleString()}</dd>
            </div>
            <div>
              <dt className="text-sm text-slate-500">Actual Value</dt>
              <dd className="text-base text-slate-900">{job.actualValue ? `$${job.actualValue.toLocaleString()}` : 'Pending'}</dd>
            </div>
            <div>
              <dt className="text-sm text-slate-500">Due Date</dt>
              <dd className="text-base text-slate-900">{job.dueDate ?? 'TBD'}</dd>
            </div>
          </dl>
        </CardContent>
      </Card>
      <Card>
        <CardHeader>
          <CardTitle>Related Tasks</CardTitle>
        </CardHeader>
        <CardContent>
          <ul className="space-y-3 text-sm">
            {relatedTasks.map((task) => (
              <li key={task.id} className="rounded-lg border border-slate-200 p-3">
                <p className="font-semibold text-slate-800">{task.title}</p>
                <p className="text-slate-500">{task.status}</p>
              </li>
            ))}
            {!relatedTasks.length && <p className="text-slate-500">No tasks for this job.</p>}
          </ul>
        </CardContent>
      </Card>
    </div>
  );
}
