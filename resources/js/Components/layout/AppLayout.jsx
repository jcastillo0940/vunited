import { useEffect, useMemo, useState } from 'react';
import Footer from '@/components/layout/Footer';
import MainNavbar from '@/components/layout/MainNavbar';
import TopTicker from '@/components/layout/TopTicker';
import { LayoutContext } from '@/context/LayoutContext';
import { publicLegalLinks, publicPrimaryCta, buildPublicHeaderLinks, buildPublicFooterLinks } from '@/config/publicNavigation';
import { fetchSiteSettings } from '@/services/siteService';
import { fetchMenu } from '@/services/menuService';
import matchService, { normalizeMatchForTicker } from '@/services/matchService';

const FALLBACK_SETTINGS = {
    site_name: 'Veraguas United FC',
    site_tagline: 'Orgullo de Veraguas',
    primary_logo_url: null,
    secondary_logo_url: null,
    contact_email: 'hola@veraguasunited.test',
    contact_phone: '+507 6000-0000',
    social_links: {
        instagram: 'https://instagram.com/veraguasunited',
        facebook: 'https://facebook.com/veraguasunited',
    },
    global_seo_title: 'Veraguas United FC',
    global_seo_description: 'Sitio oficial del Veraguas United FC.',
    maintenance_mode: false,
};

function toMenuLinks(apiItems = [], fallback = []) {
    if (!Array.isArray(apiItems) || apiItems.length === 0) return fallback;
    return apiItems.map((item) => ({
        label:        item.label ?? item.title ?? '',
        url:          item.url ?? item.href ?? null,
        pending:      item.pending ?? false,
        pendingLabel: item.pending_label ?? null,
        active:       false,
        children:     toMenuLinks(item.children ?? []),
    }));
}

export default function AppLayout({
    children,
    activeUrl = '',
    navbarVariant = 'light',
    navbarCtaLabel = publicPrimaryCta.label,
    navbarCtaHref = publicPrimaryCta.url,
    navbarCtaPending = publicPrimaryCta.pending,
    navbarCtaPendingLabel = publicPrimaryCta.pendingLabel,
    mainClassName = 'pt-24',
}) {
    const defaultHeaderLinks = useMemo(() => buildPublicHeaderLinks(activeUrl), [activeUrl]);
    const defaultFooterLinks = useMemo(() => buildPublicFooterLinks(), []);

    const [settings,    setSettings]    = useState(FALLBACK_SETTINGS);
    const [headerMenu,  setHeaderMenu]  = useState(defaultHeaderLinks);
    const [footerMenu,  setFooterMenu]  = useState(defaultFooterLinks);
    const [tickerData,  setTickerData]  = useState(null);

    useEffect(() => {
        let active = true;

        async function load() {
            const [siteSettings, header, footer] = await Promise.all([
                fetchSiteSettings().catch(() => null),
                fetchMenu('header').catch(() => null),
                fetchMenu('footer').catch(() => null),
            ]);
            if (!active) return;
            if (siteSettings) setSettings(siteSettings);
            setHeaderMenu(toMenuLinks(header?.items ?? [], defaultHeaderLinks));
            setFooterMenu(toMenuLinks(footer?.items ?? [], defaultFooterLinks));
        }

        load();
        return () => { active = false; };
    }, []);

    useEffect(() => {
        let active = true;
        matchService
            .getMatches({ status: 'scheduled' })
            .then((res) => {
                if (!active) return;
                const next = (res?.data?.data ?? [])[0] ?? null;
                setTickerData(normalizeMatchForTicker(next));
            })
            .catch(() => {});
        return () => { active = false; };
    }, []);

    const socialLinks = Object.entries(settings?.social_links ?? {}).map(([label, url]) => ({
        label,
        url,
        icon: label === 'instagram' ? 'photo_camera' : label === 'facebook' ? 'facebook' : 'share',
    }));

    return (
        <LayoutContext.Provider value={{ settings }}>
            <div className="min-h-screen bg-background text-text-main">
                <TopTicker
                    clubLabel={settings.site_name ?? 'Veraguas United FC'}
                    tickerLabel={tickerData?.label ?? null}
                    tickerText={tickerData?.text ?? null}
                    ctaLabel="Comprar entradas"
                    ctaHref="/boletos"
                />
                <MainNavbar
                    variant={navbarVariant}
                    logoUrl={settings.primary_logo_url}
                    brandName={settings.site_name ?? 'Veraguas United FC'}
                    links={headerMenu}
                    ctaLabel={navbarCtaLabel}
                    ctaHref={navbarCtaHref}
                    ctaPending={navbarCtaPending}
                    ctaPendingLabel={navbarCtaPendingLabel}
                />
                <main className={mainClassName}>{children}</main>
                <Footer
                    logoUrl={settings.secondary_logo_url ?? settings.primary_logo_url}
                    brandName={settings.site_name ?? 'Veraguas United FC'}
                    description={settings.site_tagline ?? 'El orgullo de la provincia de Veraguas.'}
                    socialLinks={socialLinks}
                    footerMenu={footerMenu}
                    legalMenu={publicLegalLinks}
                />
            </div>
        </LayoutContext.Provider>
    );
}
