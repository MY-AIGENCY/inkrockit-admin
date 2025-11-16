import { ButtonHTMLAttributes } from 'react';
declare const variants: {
    primary: string;
    secondary: string;
    ghost: string;
};
type Variant = keyof typeof variants;
export interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
    variant?: Variant;
    isLoading?: boolean;
}
export declare function Button({ className, variant, isLoading, children, disabled, ...props }: ButtonProps): import("react/jsx-runtime").JSX.Element;
export {};
