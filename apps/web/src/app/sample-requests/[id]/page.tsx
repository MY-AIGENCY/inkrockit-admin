import { notFound } from 'next/navigation';
import { Card, CardContent, CardHeader, CardTitle } from '@inkrockit/ui';
import { getSampleRequests } from '@/lib/server-api';

interface Params {
  params: { id: string };
}

export default async function SampleRequestDetailPage({ params }: Params) {
  const samples = await getSampleRequests();
  const sample = samples.data.find((item) => item.id === params.id);

  if (!sample) {
    notFound();
  }

  return (
    <Card>
      <CardHeader>
        <CardTitle>Sample Request {sample.id}</CardTitle>
      </CardHeader>
      <CardContent>
        <dl className="grid gap-4 sm:grid-cols-2">
          <div>
            <dt className="text-sm text-slate-500">Status</dt>
            <dd className="text-base text-slate-900">{sample.status}</dd>
          </div>
          <div>
            <dt className="text-sm text-slate-500">Material</dt>
            <dd className="text-base text-slate-900">{sample.material}</dd>
          </div>
          <div>
            <dt className="text-sm text-slate-500">Quantity</dt>
            <dd className="text-base text-slate-900">{sample.quantity}</dd>
          </div>
          <div>
            <dt className="text-sm text-slate-500">Tracking</dt>
            <dd className="text-base text-slate-900">{sample.shippingTracking ?? 'TBD'}</dd>
          </div>
        </dl>
      </CardContent>
    </Card>
  );
}
