import type { ReactNode } from 'react';
import { Layout, type NavLink } from '@veraguas/ui';
import { useCustomerAuth } from '../context/CustomerAuthContext';

export interface TicketingShellProps {
    activeUrl: string;
    children: ReactNode;
}

/** Cabecera/pie de Boletería, mismo sistema visual que Web (shared/ui). */
export function TicketingShell({ activeUrl, children }: TicketingShellProps) {
    const { customer } = useCustomerAuth();

    const nav: NavLink[] = [
        { label: 'Eventos', url: '/' },
        customer ? { label: 'Mi cuenta', url: '/cuenta' } : { label: 'Ingresar', url: '/ingresar' },
        { label: 'Escáner', url: '/escaner' },
    ];

    return (
        <Layout
            mainClassName="pt-24"
            header={{
                brandName: 'VERAGUAS UNITED BOLETOS',
                links: nav.map((link) => ({ ...link, active: link.url === activeUrl })),
                offsetTop: false,
            }}
            footer={{
                brandName: 'Veraguas United FC · Boletería',
                description: 'Compra segura de entradas para el Estadio Atalaya.',
                columns: [
                    {
                        title: 'Ayuda',
                        items: [
                            { label: customer ? 'Mi cuenta' : 'Ingresar', url: customer ? '/cuenta' : '/ingresar' },
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
