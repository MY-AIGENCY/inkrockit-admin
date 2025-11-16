import type { Metadata, Route } from 'next';
import Link from 'next/link';
import { Inter } from 'next/font/google';
import './globals.css';
import { Providers } from './providers';

export const dynamic = 'force-dynamic';

const inter = Inter({ subsets: ['latin'] });

const navigation = [
  { href: '/dashboard', label: 'Overview' },
  { href: '/dashboard/prospects', label: 'Prospect KPIs' },
  { href: '/dashboard/samples', label: 'Samples' },
  { href: '/dashboard/jobs', label: 'Jobs' },
  { href: '/dashboard/tasks', label: 'Tasks' },
  { href: '/prospects', label: 'Prospects' },
  { href: '/companies', label: 'Companies' },
  { href: '/sample-requests', label: 'Sample Requests' },
  { href: '/jobs', label: 'Jobs' },
  { href: '/tasks', label: 'Tasks' },
] satisfies ReadonlyArray<{ href: Route; label: string }>;

export const metadata: Metadata = {
  title: 'InkRockit Revenue Operations',
  description: 'Unified prospects, samples, jobs, tasks, and revenue analytics.',
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en" className="bg-slate-50">
      <body className={`${inter.className} bg-slate-50`}>
        <Providers>
          <div className="flex min-h-screen">
            <aside className="hidden w-72 flex-shrink-0 border-r border-slate-200 bg-white px-6 py-10 md:block">
              <div className="space-y-8">
                <div>
                  <p className="text-sm font-semibold uppercase tracking-widest text-indigo-600">InkRockit</p>
                  <p className="text-xl font-bold text-slate-900">Revenue Ops</p>
                </div>
                <nav className="space-y-2">
                  {navigation.map((item) => (
                    <Link
                      key={item.href}
                      href={item.href}
                      className="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-600"
                    >
                      {item.label}
                    </Link>
                  ))}
                </nav>
              </div>
            </aside>
            <div className="flex-1">
              <header className="border-b border-slate-200 bg-white px-6 py-4 shadow-sm">
                <div className="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                  <div>
                    <h1 className="text-xl font-semibold text-slate-900">InkRockit Intelligence Hub</h1>
                    <p className="text-sm text-slate-500">Prospect, sample, and production telemetry</p>
                  </div>
                  <div className="flex gap-3">
                    <a className="text-sm font-medium text-indigo-600 hover:text-indigo-700" href="/docs/Runbook">
                      Runbook
                    </a>
                    <a
                      className="text-sm font-medium text-indigo-600 hover:text-indigo-700"
                      href="/docs/Architecture"
                    >
                      Architecture
                    </a>
                  </div>
                </div>
              </header>
              <main className="px-4 py-8 sm:px-6 lg:px-8">{children}</main>
            </div>
          </div>
        </Providers>
      </body>
    </html>
  );
}
