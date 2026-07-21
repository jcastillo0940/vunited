import type { HTMLAttributes } from 'react';
import { cx } from '../cx';

export interface SkeletonProps extends HTMLAttributes<HTMLDivElement> {
    variant?: 'text' | 'block' | 'circle';
}

/** Usa el mismo keyframe `shimmer` del ticker actual, reutilizado aquí. */
export function Skeleton({ variant = 'block', className, ...rest }: SkeletonProps) {
    return (
        <div
            aria-hidden="true"
            className={cx(
                'relative overflow-hidden bg-surface-container-high',
                variant === 'text' && 'h-4 rounded',
                variant === 'block' && 'h-24 rounded-lg',
                variant === 'circle' && 'h-12 w-12 rounded-full',
                className,
            )}
            {...rest}
        >
            <div className="pointer-events-none absolute inset-0 animate-shimmer bg-gradient-to-r from-transparent via-white/60 to-transparent" />
        </div>
    );
}
