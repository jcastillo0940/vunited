import { useEffect, useState } from 'react';
import Footer from '@/components/layout/Footer';
import MainNavbar from '@/components/layout/MainNavbar';
import TopTicker from '@/components/layout/TopTicker';
import { publicPrimaryCta } from '@/config/publicNavigation';
import matchService, { normalizeMatchForTicker } from '@/services/matchService';

export default function AppLayout({
    children,
    navbarVariant = 'light',
    navbarBrandName,
    navbarCtaLabel = publicPrimaryCta.label,
    navbarCtaHref = publicPrimaryCta.url,
    navbarCtaPending = publicPrimaryCta.pending,
    navbarCtaPendingLabel = publicPrimaryCta.pendingLabel,
    tickerClubLabel,
    settings,
    headerMenu = [],
    footerMenu = [],
    legalMenu = [],
    ticker,
    mainClassName = 'pt-24',
}) {
    const [tickerData, setTickerData] = useState(null);

    useEffect(() => {
        let active = true;
        matchService
            .getMatches({ status: 'scheduled' })
            .then((res) => {
                if (!active) return;
                const matches = res?.data?.data ?? [];
                const next    = matches[0] ?? null;
                setTickerData(normalizeMatchForTicker(next));
            })
            .catch(() => {});
        return () => { active = false; };
    }, []);

    const socialLinks = Object.entries(settings?.social_links ?? {}).map(
        ([label, url]) => ({
            label,
            url,
            icon:
                label === 'instagram'
                    ? 'photo_camera'
                    : label === 'facebook'
                      ? 'facebook'
                      : 'share',
        }),
    );

    return (
        <div className="min-h-screen bg-background text-text-main">
            <TopTicker
                clubLabel={tickerClubLabel ?? settings?.site_name ?? 'Veraguas United FC'}
                tickerLabel={tickerData?.label ?? null}
                tickerText={tickerData?.text ?? null}
                ctaLabel={ticker?.ctaLabel ?? 'Comprar entradas'}
                ctaHref={ticker?.ctaHref ?? '/boletos'}
            />
            <MainNavbar
                variant={navbarVariant}
                logoUrl={settings?.primary_logo_url}
                brandName={navbarBrandName ?? settings?.site_name ?? 'Veraguas United FC'}
                links={headerMenu}
                ctaLabel={navbarCtaLabel}
                ctaHref={navbarCtaHref}
                ctaPending={navbarCtaPending}
                ctaPendingLabel={navbarCtaPendingLabel}
            />
            <main className={mainClassName}>{children}</main>
            <Footer
                logoUrl={settings?.secondary_logo_url ?? settings?.primary_logo_url}
                brandName={settings?.site_name ?? 'Veraguas United FC'}
                description={
                    settings?.site_tagline ??
                    'El orgullo de la provincia de Veraguas. Comprometidos con la excelencia deportiva y la comunidad.'
                }
                socialLinks={socialLinks}
                footerMenu={footerMenu}
                legalMenu={legalMenu}
            />
        </div>
    );
}
