import type { ReactNode } from 'react';
import { Layout, type NavLink } from '@veraguas/ui';

const NAV: NavLink[] = [
    { label: 'Catálogo', url: '/' },
    { label: 'Carrito', url: '/carrito' },
    { label: 'Mi orden', url: '/orden' },
];

export interface StoreShellProps {
    activeUrl: string;
    children: ReactNode;
}

/** Cabecera/pie de la Tienda, mismo sistema visual que Web (shared/ui). */
export function StoreShell({ activeUrl, children }: StoreShellProps) {
    return (
        <Layout
            mainClassName="pt-24"
            header={{
                brandName: 'VERAGUAS UNITED TIENDA',
                links: NAV.map((link) => ({ ...link, active: link.url === activeUrl })),
                offsetTop: false,
            }}
            footer={{
                brandName: 'Veraguas United FC · Tienda',
                description: 'Mercancía oficial del club.',
                columns: [
                    {
                        title: 'Ayuda',
                        items: [
                            { label: 'Consultar orden', url: '/orden' },
                            { label: 'Volver al sitio principal', url: '/' },
                        ],
                    },
                ],
            }}
        >
            {children}
        </Layout>
    );
}
