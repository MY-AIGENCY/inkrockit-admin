import { ButtonHTMLAttributes } from 'react';
import clsx from 'clsx';

const variants = {
  primary: 'bg-indigo-600 hover:bg-indigo-500 text-white shadow-sm',
  secondary: 'bg-white text-slate-900 ring-1 ring-inset ring-slate-200 hover:bg-slate-50',
  ghost: 'bg-transparent text-slate-700 hover:bg-slate-100',
};

type Variant = keyof typeof variants;

export interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: Variant;
  isLoading?: boolean;
}

export function Button({
  className,
  variant = 'primary',
  isLoading = false,
  children,
  disabled,
  ...props
}: ButtonProps) {
  return (
    <button
      className={clsx(
        'inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed disabled:opacity-60',
        variants[variant],
        className,
      )}
      disabled={disabled || isLoading}
      {...props}
    >
      {isLoading ? 'Loading…' : children}
    </button>
  );
}
