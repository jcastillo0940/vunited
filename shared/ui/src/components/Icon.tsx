import type { HTMLAttributes } from 'react';
import { cx } from '../cx';

export interface IconProps extends HTMLAttributes<HTMLSpanElement> {
    /** Nombre del icono de Material Symbols Outlined, p. ej. "shield". */
    name: string;
    size?: 'sm' | 'md' | 'lg';
}

const SIZE_CLASS: Record<NonNullable<IconProps['size']>, string> = {
    sm: 'text-base',
    md: 'text-2xl',
    lg: 'text-3xl',
};

/** Mismo mecanismo que el frontend actual: fuente Material Symbols Outlined. */
export function Icon({ name, size = 'md', className, ...rest }: IconProps) {
    return (
        <span
            className={cx('material-symbols-outlined', SIZE_CLASS[size], className)}
            aria-hidden="true"
            {...rest}
        >
            {name}
        </span>
    );
}
