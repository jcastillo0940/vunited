import type { HTMLAttributes } from 'react';
import { cx } from '../cx';

export type ContainerProps = HTMLAttributes<HTMLDivElement>;

/** Equivale a la clase `.page-shell` del frontend actual. */
export function Container({ className, ...rest }: ContainerProps) {
    return <div className={cx('page-shell', className)} {...rest} />;
}
