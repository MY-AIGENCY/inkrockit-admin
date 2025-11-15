import { Card, CardContent, CardHeader, CardTitle } from '@inkrockit/ui';

interface Props {
  label: string;
  value: number | string;
  change?: number;
  helper?: string;
}

export function KpiCard({ label, value, change, helper }: Props) {
  const formattedChange = change !== undefined ? `${change > 0 ? '+' : ''}${change}%` : null;

  return (
    <Card>
      <CardHeader>
        <CardTitle className="text-sm font-medium text-slate-500">{label}</CardTitle>
      </CardHeader>
      <CardContent>
        <p className="text-3xl font-semibold text-slate-900">{value.toLocaleString?.() ?? value}</p>
        {formattedChange && (
          <p className={`text-sm ${change! >= 0 ? 'text-emerald-600' : 'text-rose-500'}`}>{formattedChange}</p>
        )}
        {helper && <p className="mt-1 text-xs text-slate-500">{helper}</p>}
      </CardContent>
    </Card>
  );
}
