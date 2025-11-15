import { notFound } from 'next/navigation';
import { Card, CardContent, CardHeader, CardTitle } from '@inkrockit/ui';
import { getProspects, getActivities } from '@/lib/server-api';

interface Params {
  params: { id: string };
}

export default async function ProspectDetailPage({ params }: Params) {
  const [prospects, activities] = await Promise.all([getProspects(), getActivities()]);
  const prospect = prospects.data.find((item) => item.id === params.id);

  if (!prospect) {
    notFound();
  }

  const relatedActivities = activities.data.filter((activity) => activity.prospectId === prospect.id);

  return (
    <div className="space-y-8">
      <Card>
        <CardHeader>
          <CardTitle>
            {prospect.firstName} {prospect.lastName}
          </CardTitle>
        </CardHeader>
        <CardContent>
          <dl className="grid gap-4 sm:grid-cols-2">
            <div>
              <dt className="text-sm text-slate-500">Persona</dt>
              <dd className="text-base text-slate-900">{prospect.persona}</dd>
            </div>
            <div>
              <dt className="text-sm text-slate-500">Stage</dt>
              <dd className="text-base text-slate-900">{prospect.stage}</dd>
            </div>
            <div>
              <dt className="text-sm text-slate-500">Company</dt>
              <dd className="text-base text-slate-900">{prospect.companyId}</dd>
            </div>
            <div>
              <dt className="text-sm text-slate-500">Engagement Score</dt>
              <dd className="text-base text-slate-900">{prospect.engagementScore}</dd>
            </div>
          </dl>
        </CardContent>
      </Card>
      <Card>
        <CardHeader>
          <CardTitle>Latest Activities</CardTitle>
        </CardHeader>
        <CardContent>
          <ul className="space-y-3 text-sm">
            {relatedActivities.map((activity) => (
              <li key={activity.id} className="rounded-lg border border-slate-200 p-3">
                <p className="font-medium text-slate-800">{activity.type}</p>
                <p className="text-slate-500">{activity.notes}</p>
              </li>
            ))}
            {!relatedActivities.length && <p className="text-slate-500">No recent activities.</p>}
          </ul>
        </CardContent>
      </Card>
    </div>
  );
}
