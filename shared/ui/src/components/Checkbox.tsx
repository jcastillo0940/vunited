import { forwardRef, type InputHTMLAttributes } from 'react';
import { cx } from '../cx';

export interface CheckboxProps extends InputHTMLAttributes<HTMLInputElement> {
    label?: string;
}

export const Checkbox = forwardRef<HTMLInputElement, CheckboxProps>(function Checkbox(
    { label, className, id, ...rest },
    ref,
) {
    return (
        <label htmlFor={id} className="inline-flex items-center gap-2 text-sm text-text-main">
            <input
                ref={ref}
                id={id}
                type="checkbox"
                className={cx('rounded border-outline text-primary focus:ring-accent', className)}
                {...rest}
            />
            {label}
        </label>
    );
});
