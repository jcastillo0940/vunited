import type { ReactNode } from 'react';
import { Layout } from '@veraguas/ui';
import { AnnouncementBar } from '../components/AnnouncementBar';
import { buildHeaderLinks, footerColumns, legalLinks, primaryCta } from '../config/navigation';

export interface AppShellProps {
    activeUrl: string;
    children: ReactNode;
    navbarVariant?: 'light' | 'dark';
}

/** Equivalente tipado de AppLayout.jsx del frontend actual (Ticker + Header + Footer). */
export function AppShell({ activeUrl, children, navbarVariant = 'light' }: AppShellProps) {
    return (
        <Layout
            announcement={<AnnouncementBar />}
            mainClassName="pt-32"
            header={{
                links: buildHeaderLinks(activeUrl),
                variant: navbarVariant,
                offsetTop: true,
                ctaLabel: primaryCta.label,
                ctaHref: primaryCta.href,
            }}
            footer={{
                columns: footerColumns,
                legalLinks,
                socialLinks: [
                    { label: 'Instagram', url: 'https://instagram.com/veraguasunited', icon: 'photo_camera' },
                    { label: 'Facebook', url: 'https://facebook.com/veraguasunited', icon: 'facebook' },
                ],
            }}
        >
            {children}
        </Layout>
    );
}
