import { forwardRef, type InputHTMLAttributes } from 'react';
import { cx } from '../cx';

export interface RadioProps extends InputHTMLAttributes<HTMLInputElement> {
    label?: string;
}

export const Radio = forwardRef<HTMLInputElement, RadioProps>(function Radio(
    { label, className, id, ...rest },
    ref,
) {
    return (
        <label htmlFor={id} className="inline-flex items-center gap-2 text-sm text-text-main">
            <input
                ref={ref}
                id={id}
                type="radio"
                className={cx('border-outline text-primary focus:ring-accent', className)}
                {...rest}
            />
            {label}
        </label>
    );
});
