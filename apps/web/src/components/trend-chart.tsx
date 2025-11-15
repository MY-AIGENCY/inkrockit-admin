'use client';

import { Line, LineChart, ResponsiveContainer, Tooltip, XAxis, YAxis, BarChart, Bar } from 'recharts';

interface TrendChartProps {
  data: { label: string; value: number }[];
  color?: string;
}

export function TrendChart({ data, color = '#7c3aed' }: TrendChartProps) {
  return (
    <div className="h-48">
      <ResponsiveContainer width="100%" height="100%">
        <LineChart data={data} margin={{ top: 10, right: 0, bottom: 0, left: 0 }}>
          <XAxis dataKey="label" stroke="#94a3b8" fontSize={12} />
          <YAxis stroke="#94a3b8" fontSize={12} />
          <Tooltip />
          <Line type="monotone" dataKey="value" stroke={color} strokeWidth={3} dot={false} />
        </LineChart>
      </ResponsiveContainer>
    </div>
  );
}

interface BarTrendProps {
  data: { label: string; value: number }[];
  color?: string;
}

export function BarTrendChart({ data, color = '#6366f1' }: BarTrendProps) {
  return (
    <div className="h-48">
      <ResponsiveContainer width="100%" height="100%">
        <BarChart data={data} margin={{ top: 10, right: 0, bottom: 0, left: 0 }}>
          <XAxis dataKey="label" stroke="#94a3b8" fontSize={12} />
          <YAxis stroke="#94a3b8" fontSize={12} />
          <Tooltip />
          <Bar dataKey="value" fill={color} radius={[6, 6, 0, 0]} />
        </BarChart>
      </ResponsiveContainer>
    </div>
  );
}
