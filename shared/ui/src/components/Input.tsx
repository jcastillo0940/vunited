import { forwardRef, type InputHTMLAttributes } from 'react';
import { cx } from '../cx';

export interface InputProps extends InputHTMLAttributes<HTMLInputElement> {
    invalid?: boolean;
}

const BASE_CLASS =
    'block w-full rounded-md border-outline bg-white text-sm text-text-main shadow-sm focus:border-accent focus:ring-accent disabled:bg-surface disabled:text-text-main/50';

export const Input = forwardRef<HTMLInputElement, InputProps>(function Input(
    { invalid, className, ...rest },
    ref,
) {
    return (
        <input
            ref={ref}
            className={cx(BASE_CLASS, invalid && 'border-red-500 focus:border-red-500 focus:ring-red-500', className)}
            aria-invalid={invalid || undefined}
            {...rest}
        />
    );
});
