import clsx from 'clsx';
import { jsx as _jsx } from 'react/jsx-runtime';

export function Card({ className, children }) {
  return _jsx('div', {
    className: clsx('rounded-xl border border-slate-200 bg-white shadow-sm', className),
    children,
  });
}

export function CardHeader({ children, className }) {
  return _jsx('div', {
    className: clsx('border-b border-slate-100 px-6 py-4', className),
    children,
  });
}

export function CardContent({ children, className }) {
  return _jsx('div', {
    className: clsx('px-6 py-4', className),
    children,
  });
}

export function CardTitle({ children, className }) {
  return _jsx('h3', {
    className: clsx('text-base font-semibold text-slate-900', className),
    children,
  });
}
