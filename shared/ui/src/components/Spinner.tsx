import { cx } from '../cx';

export interface SpinnerProps {
    size?: 'sm' | 'md' | 'lg';
    label?: string;
    className?: string;
}

const SIZE_CLASS = {
    sm: 'h-4 w-4 border-2',
    md: 'h-8 w-8 border-2',
    lg: 'h-12 w-12 border-[3px]',
} as const;

export function Spinner({ size = 'md', label = 'Cargando', className }: SpinnerProps) {
    return (
        <span
            role="status"
            aria-label={label}
            className={cx(
                'inline-block animate-spin rounded-full border-outline border-t-primary',
                SIZE_CLASS[size],
                className,
            )}
        />
    );
}
