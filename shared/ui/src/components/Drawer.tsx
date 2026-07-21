import type { ReactNode } from 'react';
import { Icon } from './Icon';
import { cx } from '../cx';
import { zIndex } from '../tokens';

export interface DrawerProps {
    open: boolean;
    onClose: () => void;
    title: string;
    side?: 'left' | 'right';
    children: ReactNode;
}

/** Panel lateral (carrito, filtros móviles, menú móvil). */
export function Drawer({ open, onClose, title, side = 'right', children }: DrawerProps) {
    return (
        <div
            className={cx('fixed inset-0', open ? 'pointer-events-auto' : 'pointer-events-none')}
            style={{ zIndex: zIndex.drawer }}
            aria-hidden={!open}
        >
            <button
                type="button"
                onClick={onClose}
                aria-label="Cerrar"
                tabIndex={open ? 0 : -1}
                className={cx(
                    'absolute inset-0 cursor-default bg-primary/40 transition-opacity',
                    open ? 'opacity-100' : 'opacity-0',
                )}
            />
            <div
                role="dialog"
                aria-modal="true"
                aria-labelledby="drawer-title"
                className={cx(
                    'absolute top-0 h-full w-full max-w-sm bg-white shadow-panel transition-transform',
                    side === 'right' ? 'right-0' : 'left-0',
                    open
                        ? 'translate-x-0'
                        : side === 'right'
                          ? 'translate-x-full'
                          : '-translate-x-full',
                )}
            >
                <div className="flex items-center justify-between border-b border-outline px-6 py-4">
                    <h2 id="drawer-title" className="font-display text-lg font-bold uppercase text-primary">
                        {title}
                    </h2>
                    <button
                        type="button"
                        onClick={onClose}
                        aria-label="Cerrar"
                        className="rounded-md p-1 text-text-main/60 hover:bg-surface hover:text-text-main"
                    >
                        <Icon name="close" size="sm" />
                    </button>
                </div>
                <div className="overflow-y-auto p-6">{children}</div>
            </div>
        </div>
    );
}
