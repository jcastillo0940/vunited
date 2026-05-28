import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import AcademyHero from '@/components/academy/AcademyHero';
import AcademyIntro from '@/components/academy/AcademyIntro';
import AcademyCategories from '@/components/academy/AcademyCategories';
import AcademyStats from '@/components/academy/AcademyStats';
import AcademyProcess from '@/components/academy/AcademyProcess';
import AcademyCTA from '@/components/academy/AcademyCTA';
import academyMock from '@/mocks/academyMock';
import homeMock from '@/mocks/homeMock';
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
    global_seo_title: 'Fuerzas Basicas | Veraguas United FC',
    global_seo_description: 'Semillero indio y desarrollo de talento juvenil.',
    maintenance_mode: false,
};

export default function Academy() {
    const defaultHeaderLinks = useMemo(() => buildPublicHeaderLinks('/fuerzas-basicas'), []);
    const defaultFooterLinks = useMemo(() => buildPublicFooterLinks(), []);
    const [settings, setSettings] = useState(fallbackSettings);
    const [headerMenu, setHeaderMenu] = useState(defaultHeaderLinks);
    const [footerMenu, setFooterMenu] = useState(defaultFooterLinks);
    const [activeCategoryId, setActiveCategoryId] = useState(academyMock.categories[0].id);

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
        () => settings.global_seo_title || 'Fuerzas Basicas | Veraguas United FC',
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
                navbarCtaLabel="SOCIO INDIO"
                navbarVariant="solid"
                mainClassName="pt-28"
            >
                <AcademyHero hero={academyMock.hero} />
                <AcademyStats stats={academyMock.impactStats} />
                <AcademyIntro intro={academyMock.intro} />
                <AcademyCategories
                    categories={academyMock.categories}
                    activeCategoryId={activeCategoryId}
                    onCategoryChange={setActiveCategoryId}
                />
                <AcademyProcess steps={academyMock.process} />
                <AcademyCTA cta={academyMock.finalCta} />
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
        active: item.url === '/fuerzas-basicas',
        children: toMenuLinks(item.children ?? []),
    }));
}
