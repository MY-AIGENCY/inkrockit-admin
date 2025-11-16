import { Suspense } from 'react';
import { KpiCard } from '@/components/kpi-card';
import { BarTrendChart, TrendChart } from '@/components/trend-chart';
import { GlobalFilterBar } from '@/components/global-filter-bar';
import { Card, CardContent, CardHeader, CardTitle } from '@inkrockit/ui';
import { getOverviewMetrics, getSampleMetrics, getConversionMetrics, getRevenueMetrics } from '@/lib/server-api';

export const dynamic = 'force-dynamic';
export const revalidate = 0;

function mapSeries(series: { label: string; value: number }[]) {
  return series.map((entry) => ({ label: entry.label, value: entry.value }));
}

export default async function DashboardPage({
  searchParams
}: {
  searchParams: Promise<{ stage?: string; region?: string }> | { stage?: string; region?: string }
}) {
  const params = await Promise.resolve(searchParams);
  const [overview, sampleMetrics, conversionMetrics, revenueSeries] = await Promise.all([
    getOverviewMetrics(params),
    getSampleMetrics(params),
    getConversionMetrics(params),
    getRevenueMetrics(params),
  ]);

  return (
    <div className="space-y-8">
      <Suspense fallback={null}>
        <GlobalFilterBar pathname="/dashboard" />
      </Suspense>
      <section className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        {overview.kpis.map((kpi) => (
          <KpiCard key={kpi.label} label={kpi.label} value={kpi.value} change={kpi.change} />
        ))}
        <KpiCard label="Open Tasks" value={overview.summaries.openTasks} helper="Operational backlog" />
      </section>
      <section className="grid gap-6 lg:grid-cols-2">
        <Card>
          <CardHeader>
            <CardTitle>Sample Velocity</CardTitle>
          </CardHeader>
          <CardContent>
            <BarTrendChart data={mapSeries(sampleMetrics.map((metric) => ({ label: metric.status, value: metric.count })))} />
          </CardContent>
        </Card>
        <Card>
          <CardHeader>
            <CardTitle>Revenue Momentum</CardTitle>
          </CardHeader>
          <CardContent>
            <TrendChart data={mapSeries(revenueSeries.map((item) => ({ label: item.timeframe, value: item.revenue })))} />
          </CardContent>
        </Card>
      </section>
      <section className="grid gap-6 lg:grid-cols-2">
        <Card>
          <CardHeader>
            <CardTitle>Conversion Funnel</CardTitle>
          </CardHeader>
          <CardContent>
            <ul className="space-y-3">
              {conversionMetrics.map((stage) => (
                <li key={stage.stage} className="flex items-center justify-between">
                  <p className="font-medium text-slate-700">{stage.stage}</p>
                  <p className="text-sm font-semibold text-indigo-600">{(stage.rate * 100).toFixed(1)}%</p>
                </li>
              ))}
            </ul>
          </CardContent>
        </Card>
        <Card>
          <CardHeader>
            <CardTitle>Pipeline Snapshot</CardTitle>
          </CardHeader>
          <CardContent>
            <dl className="grid grid-cols-2 gap-4 text-sm">
              <div>
                <dt className="text-slate-500">Prospects</dt>
                <dd className="text-2xl font-semibold text-slate-900">{overview.summaries.prospects}</dd>
              </div>
              <div>
                <dt className="text-slate-500">Companies</dt>
                <dd className="text-2xl font-semibold text-slate-900">{overview.summaries.companies}</dd>
              </div>
              <div>
                <dt className="text-slate-500">Samples</dt>
                <dd className="text-2xl font-semibold text-slate-900">{overview.summaries.sampleRequests}</dd>
              </div>
              <div>
                <dt className="text-slate-500">Open Tasks</dt>
                <dd className="text-2xl font-semibold text-slate-900">{overview.summaries.openTasks}</dd>
              </div>
            </dl>
          </CardContent>
        </Card>
      </section>
    </div>
  );
}
