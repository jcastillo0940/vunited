import type { ReactNode } from 'react';
import { FieldError } from './FieldError';
import { cx } from '../cx';

export interface FormFieldProps {
    /** Debe coincidir con el `id` del control (Input/Select/Textarea) que envuelve. */
    htmlFor: string;
    label: string;
    hint?: string;
    error?: string | null;
    required?: boolean;
    children: ReactNode;
    className?: string;
}

/** Agrupa label + control + hint + FieldError con asociación accesible. */
export function FormField({ htmlFor, label, hint, error, required, children, className }: FormFieldProps) {
    const hintId = hint ? `${htmlFor}-hint` : undefined;
    const errorId = error ? `${htmlFor}-error` : undefined;

    return (
        <div className={cx('flex flex-col gap-1', className)}>
            <label htmlFor={htmlFor} className="text-sm font-semibold text-text-main">
                {label}
                {required ? <span aria-hidden="true" className="ml-0.5 text-red-600">*</span> : null}
            </label>
            {hint ? (
                <p id={hintId} className="text-xs text-text-main/60">
                    {hint}
                </p>
            ) : null}
            {children}
            <FieldError id={errorId}>{error}</FieldError>
        </div>
    );
}
