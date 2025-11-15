import { PropsWithChildren } from 'react';
import clsx from 'clsx';

export function Card({ className, children }: PropsWithChildren<{ className?: string }>) {
  return <div className={clsx('rounded-xl border border-slate-200 bg-white shadow-sm', className)}>{children}</div>;
}

export function CardHeader({ children, className }: PropsWithChildren<{ className?: string }>) {
  return <div className={clsx('border-b border-slate-100 px-6 py-4', className)}>{children}</div>;
}

export function CardContent({ children, className }: PropsWithChildren<{ className?: string }>) {
  return <div className={clsx('px-6 py-4', className)}>{children}</div>;
}

export function CardTitle({ children, className }: PropsWithChildren<{ className?: string }>) {
  return <h3 className={clsx('text-base font-semibold text-slate-900', className)}>{children}</h3>;
}
