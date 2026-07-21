import type { HTMLAttributes } from 'react';
import { cx } from '../cx';

export interface CardProps extends HTMLAttributes<HTMLDivElement> {
    variant?: 'card' | 'panel';
}

/** Equivale a .surface-card / .surface-panel del frontend actual. */
export function Card({ variant = 'card', className, ...rest }: CardProps) {
    return <div className={cx(variant === 'card' ? 'surface-card' : 'surface-panel', 'p-6', className)} {...rest} />;
}
