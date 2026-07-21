import { forwardRef, type TextareaHTMLAttributes } from 'react';
import { cx } from '../cx';

export interface TextareaProps extends TextareaHTMLAttributes<HTMLTextAreaElement> {
    invalid?: boolean;
}

const BASE_CLASS =
    'block w-full rounded-md border-outline bg-white text-sm text-text-main shadow-sm focus:border-accent focus:ring-accent disabled:bg-surface disabled:text-text-main/50';

export const Textarea = forwardRef<HTMLTextAreaElement, TextareaProps>(function Textarea(
    { invalid, className, rows = 4, ...rest },
    ref,
) {
    return (
        <textarea
            ref={ref}
            rows={rows}
            className={cx(BASE_CLASS, invalid && 'border-red-500 focus:border-red-500 focus:ring-red-500', className)}
            aria-invalid={invalid || undefined}
            {...rest}
        />
    );
});
