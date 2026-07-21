import type { HTMLAttributes } from 'react';
import { cx } from '../cx';

export interface GridProps extends HTMLAttributes<HTMLDivElement> {
    cols?: 1 | 2 | 3 | 4;
    gap?: 'sm' | 'md' | 'lg';
}

const COLS_CLASS: Record<NonNullable<GridProps['cols']>, string> = {
    1: 'grid-cols-1',
    2: 'grid-cols-1 md:grid-cols-2',
    3: 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
    4: 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
};

const GAP_CLASS: Record<NonNullable<GridProps['gap']>, string> = {
    sm: 'gap-4',
    md: 'gap-8',
    lg: 'gap-12',
};

export function Grid({ cols = 3, gap = 'md', className, ...rest }: GridProps) {
    return <div className={cx('grid', COLS_CLASS[cols], GAP_CLASS[gap], className)} {...rest} />;
}
