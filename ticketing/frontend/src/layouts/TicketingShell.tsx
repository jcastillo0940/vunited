import type { ReactNode } from 'react';
import { Layout, type NavLink } from '@veraguas/ui';

const NAV: NavLink[] = [
    { label: 'Eventos', url: '/' },
    { label: 'Mi wallet', url: '/wallet' },
    { label: 'Escáner', url: '/escaner' },
];

export interface TicketingShellProps {
    activeUrl: string;
    children: ReactNode;
}

/** Cabecera/pie de Boletería, mismo sistema visual que Web (shared/ui). */
export function TicketingShell({ activeUrl, children }: TicketingShellProps) {
    return (
        <Layout
            mainClassName="pt-24"
            header={{
                brandName: 'VERAGUAS UNITED BOLETOS',
                links: NAV.map((link) => ({ ...link, active: link.url === activeUrl })),
                offsetTop: false,
            }}
            footer={{
                brandName: 'Veraguas United FC · Boletería',
                description: 'Compra segura de entradas para el Estadio Atalaya.',
                columns: [
                    {
                        title: 'Ayuda',
                        items: [
                            { label: 'Mi wallet', url: '/wallet' },
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
