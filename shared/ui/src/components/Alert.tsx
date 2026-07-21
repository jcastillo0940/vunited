import type { HTMLAttributes } from 'react';
import { Icon } from './Icon';
import { cx } from '../cx';

type Tone = 'info' | 'success' | 'warning' | 'danger';

const TONE_CLASS: Record<Tone, string> = {
    info: 'border-accent/40 bg-accent/10 text-primary',
    success: 'border-emerald-300 bg-emerald-50 text-emerald-800',
    warning: 'border-amber-300 bg-amber-50 text-amber-800',
    danger: 'border-red-300 bg-red-50 text-red-800',
};

const TONE_ICON: Record<Tone, string> = {
    info: 'info',
    success: 'check_circle',
    warning: 'warning',
    danger: 'error',
};

export interface AlertProps extends HTMLAttributes<HTMLDivElement> {
    tone?: Tone;
    title?: string;
}

export function Alert({ tone = 'info', title, children, className, ...rest }: AlertProps) {
    return (
        <div
            role={tone === 'danger' ? 'alert' : 'status'}
            className={cx('flex gap-3 rounded-lg border p-4 text-sm', TONE_CLASS[tone], className)}
            {...rest}
        >
            <Icon name={TONE_ICON[tone]} size="sm" className="mt-0.5 shrink-0" />
            <div>
                {title ? <p className="font-semibold">{title}</p> : null}
                <div>{children}</div>
            </div>
        </div>
    );
}
