import { notFound } from 'next/navigation';
import { Card, CardContent, CardHeader, CardTitle } from '@inkrockit/ui';
import { getCompanies, getJobs } from '@/lib/server-api';

interface Params {
  params: { id: string };
}

export default async function CompanyDetailPage({ params }: Params) {
  const [companies, jobs] = await Promise.all([getCompanies(), getJobs()]);
  const company = companies.data.find((item) => item.id === params.id);

  if (!company) {
    notFound();
  }

  const relatedJobs = jobs.data.filter((job) => job.companyId === company.id);

  return (
    <div className="space-y-8">
      <Card>
        <CardHeader>
          <CardTitle>{company.name}</CardTitle>
        </CardHeader>
        <CardContent>
          <dl className="grid gap-4 sm:grid-cols-2">
            <div>
              <dt className="text-sm text-slate-500">Industry</dt>
              <dd className="text-base text-slate-900">{company.industry}</dd>
            </div>
            <div>
              <dt className="text-sm text-slate-500">Segment</dt>
              <dd className="text-base text-slate-900">{company.segment}</dd>
            </div>
            <div>
              <dt className="text-sm text-slate-500">Owner</dt>
              <dd className="text-base text-slate-900">{company.owner}</dd>
            </div>
            <div>
              <dt className="text-sm text-slate-500">Revenue</dt>
              <dd className="text-base text-slate-900">${company.totalRevenue.toLocaleString()}</dd>
            </div>
          </dl>
        </CardContent>
      </Card>
      <Card>
        <CardHeader>
          <CardTitle>Jobs</CardTitle>
        </CardHeader>
        <CardContent>
          <ul className="space-y-3 text-sm">
            {relatedJobs.map((job) => (
              <li key={job.id} className="rounded-lg border border-slate-200 p-3">
                <p className="font-semibold text-slate-800">{job.description}</p>
                <p className="text-slate-500">Status: {job.status}</p>
              </li>
            ))}
            {!relatedJobs.length && <p className="text-slate-500">No jobs yet.</p>}
          </ul>
        </CardContent>
      </Card>
    </div>
  );
}
