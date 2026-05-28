import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import TryoutsHero from '@/components/tryouts/TryoutsHero';
import TryoutsForm from '@/components/tryouts/TryoutsForm';
import TryoutsInfoCards from '@/components/tryouts/TryoutsInfoCards';
import homeMock from '@/mocks/homeMock';
import tryoutsMock from '@/mocks/tryoutsMock';
import { fetchSiteSettings } from '@/services/siteService';
import { fetchMenu } from '@/services/menuService';
import {
    buildPublicFooterLinks,
    buildPublicHeaderLinks,
    publicLegalLinks,
} from '@/config/publicNavigation';

const fallbackSettings = {
    site_name: 'Veraguas United FC',
    site_tagline: 'Orgullo de Veraguas',
    primary_logo_url: null,
    secondary_logo_url: null,
    primary_color: '#1D428A',
    accent_color: '#5BC2E7',
    contact_email: 'hola@veraguasunited.test',
    contact_phone: '+507 6000-0000',
    social_links: {
        instagram: 'https://instagram.com/veraguasunited',
        facebook: 'https://facebook.com/veraguasunited',
    },
    global_seo_title: 'Pruebas | Veraguas United FC',
    global_seo_description: 'Inscripcion publica a pruebas del semillero indio.',
    maintenance_mode: false,
};

export default function Tryouts() {
    const defaultHeaderLinks = useMemo(() => buildPublicHeaderLinks('/pruebas'), []);
    const defaultFooterLinks = useMemo(() => buildPublicFooterLinks(), []);
    const [settings, setSettings] = useState(fallbackSettings);
    const [headerMenu, setHeaderMenu] = useState(defaultHeaderLinks);
    const [footerMenu, setFooterMenu] = useState(defaultFooterLinks);

    useEffect(() => {
        let active = true;

        async function loadShell() {
            try {
                const [siteSettings, header, footer] = await Promise.all([
                    fetchSiteSettings(),
                    fetchMenu('header'),
                    fetchMenu('footer'),
                ]);

                if (!active) {
                    return;
                }

                setSettings(siteSettings ?? fallbackSettings);
                setHeaderMenu(toMenuLinks(header?.items ?? [], defaultHeaderLinks));
                setFooterMenu(toMenuLinks(footer?.items ?? [], defaultFooterLinks));
            } catch {
                if (!active) {
                    return;
                }

                setSettings(fallbackSettings);
                setHeaderMenu(defaultHeaderLinks);
                setFooterMenu(defaultFooterLinks);
            }
        }

        loadShell();

        return () => {
            active = false;
        };
    }, []);

    const pageTitle = useMemo(
        () => settings.global_seo_title || 'Pruebas | Veraguas United FC',
        [settings.global_seo_title],
    );

    return (
        <>
            <Head title={pageTitle} />
            <AppLayout
                settings={settings}
                headerMenu={headerMenu}
                footerMenu={footerMenu}
                legalMenu={publicLegalLinks}
                ticker={homeMock.ticker}
                tickerClubLabel="VERAGUAS UNITED FC"
                navbarBrandName="VERAGUAS UNITED"
                navbarCtaLabel="UNETE A LA TRIBU"
                navbarVariant="light"
                mainClassName="pt-32"
            >
                <TryoutsHero hero={tryoutsMock.hero} />
                <TryoutsForm positionOptions={tryoutsMock.positionOptions} />
                <TryoutsInfoCards cards={tryoutsMock.infoCards} />
            </AppLayout>
        </>
    );
}

function toMenuLinks(items = [], fallback = []) {
    if (!items.length) {
        return fallback;
    }

    return items.map((item) => ({
        label: item.label,
        url: item.url,
        active: item.url === '/pruebas',
        children: toMenuLinks(item.children ?? []),
    }));
}
