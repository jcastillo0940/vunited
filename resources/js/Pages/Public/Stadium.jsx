import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import StadiumHero from '@/components/stadium/StadiumHero';
import StadiumInfo from '@/components/stadium/StadiumInfo';
import StadiumMap from '@/components/stadium/StadiumMap';
import StadiumZones from '@/components/stadium/StadiumZones';
import MatchdayExperience from '@/components/stadium/MatchdayExperience';
import StadiumRules from '@/components/stadium/StadiumRules';
import StadiumCTA from '@/components/stadium/StadiumCTA';
import homeMock from '@/mocks/homeMock';
import stadiumMock from '@/mocks/stadiumMock';
import stadiumService, { normalizeStadium } from '@/services/stadiumService';
import { fetchSiteSettings } from '@/services/siteService';
import { fetchMenu } from '@/services/menuService';
import {
    buildPublicFooterLinks,
    buildPublicHeaderLinks,
    publicLegalLinks,
    publicPrimaryCta,
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
    global_seo_title: 'Estadio Atalaya | Veraguas United FC',
    global_seo_description: 'Información, zonas y experiencia matchday del Estadio Atalaya, casa del Veraguas United FC.',
    maintenance_mode: false,
};

export default function Stadium() {
    const defaultHeaderLinks = useMemo(() => buildPublicHeaderLinks('/estadio'), []);
    const defaultFooterLinks = useMemo(() => buildPublicFooterLinks(), []);
    const [settings, setSettings]     = useState(fallbackSettings);
    const [headerMenu, setHeaderMenu] = useState(defaultHeaderLinks);
    const [footerMenu, setFooterMenu] = useState(defaultFooterLinks);
    const [stadium, setStadium]       = useState(stadiumMock);

    useEffect(() => {
        let active = true;

        async function load() {
            try {
                const [siteSettings, header, footer, stadiumRes] = await Promise.all([
                    fetchSiteSettings(),
                    fetchMenu('header'),
                    fetchMenu('footer'),
                    stadiumService.getStadium().catch(() => null),
                ]);

                if (!active) return;

                setSettings(siteSettings ?? fallbackSettings);
                setHeaderMenu(toMenuLinks(header?.items ?? [], defaultHeaderLinks, '/estadio'));
                setFooterMenu(toMenuLinks(footer?.items ?? [], defaultFooterLinks));

                const raw = stadiumRes?.data?.data ?? null;
                if (raw) setStadium(normalizeStadium(raw));
            } catch {
                if (!active) return;
                setSettings(fallbackSettings);
                setHeaderMenu(defaultHeaderLinks);
                setFooterMenu(defaultFooterLinks);
            }
        }

        load();
        return () => { active = false; };
    }, [defaultFooterLinks, defaultHeaderLinks]);

    const pageTitle = useMemo(
        () => settings.global_seo_title || 'Estadio Atalaya | Veraguas United FC',
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
                navbarCtaLabel={publicPrimaryCta.label}
                navbarCtaHref={publicPrimaryCta.url}
                navbarCtaPending={publicPrimaryCta.pending}
                navbarCtaPendingLabel={publicPrimaryCta.pendingLabel}
                navbarVariant="light"
                mainClassName="pt-0"
            >
                <StadiumHero hero={stadium.hero} />
                <StadiumInfo info={stadium.info} />
                <StadiumMap map={stadium.map} />
                <StadiumZones zones={stadium.zones} />
                <MatchdayExperience items={stadium.matchday} />
                <StadiumRules rules={stadium.rules} />
                <StadiumCTA cta={stadium.cta} />
            </AppLayout>
        </>
    );
}

function toMenuLinks(items = [], fallback = [], activeUrl = '') {
    if (!items.length) return fallback;
    return items.map((item) => ({
        ...item,
        active:   item.url ? item.url === activeUrl : false,
        children: toMenuLinks(item.children ?? [], [], activeUrl),
    }));
}
