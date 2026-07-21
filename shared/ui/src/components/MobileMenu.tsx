import { Icon } from './Icon';
import type { NavLink } from './Navigation';
import { zIndex } from '../tokens';

export interface MobileMenuProps {
    open: boolean;
    onToggle: () => void;
    links: NavLink[];
}

/** Botón hamburguesa + panel colapsable, replica el menú móvil de MainNavbar.jsx. */
export function MobileMenu({ open, onToggle, links }: MobileMenuProps) {
    return (
        <div className="lg:hidden">
            <button
                type="button"
                onClick={onToggle}
                aria-expanded={open}
                aria-controls="mobile-menu-panel"
                aria-label={open ? 'Cerrar menú' : 'Abrir menú'}
                className="rounded-md p-2 text-primary hover:bg-surface"
            >
                <Icon name={open ? 'close' : 'menu'} />
            </button>
            {open ? (
                <div
                    id="mobile-menu-panel"
                    className="absolute left-0 right-0 top-full flex flex-col gap-1 border-b border-outline bg-white px-4 py-4 shadow-panel"
                    style={{ zIndex: zIndex.dropdown }}
                >
                    {links.map((link) =>
                        link.url ? (
                            <a
                                key={link.label}
                                href={link.url}
                                className="rounded-md px-3 py-2 text-sm font-semibold uppercase text-text-main hover:bg-surface hover:text-primary"
                            >
                                {link.label}
                            </a>
                        ) : (
                            <span
                                key={link.label}
                                className="rounded-md px-3 py-2 text-sm font-semibold uppercase text-text-main/40"
                            >
                                {link.pendingLabel ?? link.label}
                            </span>
                        ),
                    )}
                </div>
            ) : null}
        </div>
    );
}
