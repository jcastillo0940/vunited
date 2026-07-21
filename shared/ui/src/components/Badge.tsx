import type { HTMLAttributes } from 'react';
import { cx } from '../cx';

type Tone = 'primary' | 'accent' | 'success' | 'warning' | 'danger' | 'neutral';

const TONE_CLASS: Record<Tone, string> = {
    primary: 'bg-primary text-on-primary',
    accent: 'bg-accent text-on-accent',
    success: 'bg-emerald-100 text-emerald-800',
    warning: 'bg-amber-100 text-amber-800',
    danger: 'bg-red-100 text-red-800',
    neutral: 'bg-surface text-text-main',
};

export interface BadgeProps extends HTMLAttributes<HTMLSpanElement> {
    tone?: Tone;
}

export function Badge({ tone = 'neutral', className, ...rest }: BadgeProps) {
    return (
        <span
            className={cx(
                'inline-flex items-center rounded-full px-3 py-0.5 text-[10px] font-bold uppercase tracking-wide',
                TONE_CLASS[tone],
                className,
            )}
            {...rest}
        />
    );
}
