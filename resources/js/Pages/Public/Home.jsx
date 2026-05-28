import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import HomeHero from '@/components/home/HomeHero';
import QuickMatchBar from '@/components/home/QuickMatchBar';
import HomeNewsSection from '@/components/home/HomeNewsSection';
import AcademyBlock from '@/components/home/AcademyBlock';
import StandingsCard from '@/components/home/StandingsCard';
import ShopPreview from '@/components/home/ShopPreview';
import MembershipBanner from '@/components/home/MembershipBanner';
import PartnersCarousel from '@/components/home/PartnersCarousel';
import homeMock from '@/mocks/homeMock';
import { fetchSiteSettings } from '@/services/siteService';
import { fetchMenu } from '@/services/menuService';
import { fetchNews } from '@/services/newsService';
import matchService, { normalizeMatchForBar } from '@/services/matchService';
import standingService, { normalizeStandingsForCard } from '@/services/standingService';
import sponsorService from '@/services/sponsorService';
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
    global_seo_title: 'Veraguas United FC',
    global_seo_description: 'Sitio oficial del Veraguas United FC. Calendario, plantilla, boletos, noticias y más.',
    maintenance_mode: false,
};

export default function Home() {
    const defaultHeaderLinks = useMemo(() => buildPublicHeaderLinks('/'), []);
    const defaultFooterLinks = useMemo(() => buildPublicFooterLinks(), []);
    const [settings, setSettings] = useState(fallbackSettings);
    const [headerMenu, setHeaderMenu] = useState(defaultHeaderLinks);
    const [footerMenu, setFooterMenu] = useState(defaultFooterLinks);
    const [newsItems, setNewsItems] = useState(defaultNewsItems);
    const [lastResult, setLastResult] = useState(homeMock.lastResult);
    const [nextMatch, setNextMatch] = useState(homeMock.nextMatch);
    const [standings, setStandings] = useState(homeMock.standings);
    const [partners, setPartners]   = useState(homeMock.partners);

    useEffect(() => {
        let active = true;

        async function loadHome() {
            try {
                const [siteSettings, header, footer, news, featuredRes, finishedRes, standingsRes, sponsorsRes] = await Promise.all([
                    fetchSiteSettings(),
                    fetchMenu('header'),
                    fetchMenu('footer'),
                    fetchNews(),
                    matchService.getFeatured().catch(() => null),
                    matchService.getMatches({ status: 'finished' }).catch(() => null),
                    standingService.getStandings().catch(() => null),
                    sponsorService.getSponsors().catch(() => null),
                ]);

                if (!active) {
                    return;
                }

                setSettings(siteSettings ?? fallbackSettings);
                setHeaderMenu(toMenuLinks(header?.items ?? [], defaultHeaderLinks, '/'));
                setFooterMenu(toMenuLinks(footer?.items ?? [], defaultFooterLinks));
                setNewsItems(normalizeNews(news));

                const featuredMatch = featuredRes?.data?.data ?? null;
                if (featuredMatch) setNextMatch(normalizeMatchForBar(featuredMatch));

                const finishedMatches = finishedRes?.data?.data ?? [];
                if (finishedMatches.length) {
                    setLastResult(normalizeMatchForBar(finishedMatches[finishedMatches.length - 1]));
                }

                const standingRows = standingsRes?.data?.data ?? [];
                if (standingRows.length) setStandings(normalizeStandingsForCard(standingRows));

                const sponsorRows = sponsorsRes?.data?.data ?? [];
                if (sponsorRows.length) setPartners(sponsorRows);
            } catch {
                if (!active) {
                    return;
                }

                setSettings(fallbackSettings);
                setHeaderMenu(defaultHeaderLinks);
                setFooterMenu(defaultFooterLinks);
                setNewsItems(defaultNewsItems);
            }
        }

        loadHome();

        return () => {
            active = false;
        };
    }, []);

    const pageTitle = useMemo(
        () => settings.global_seo_title || settings.site_name || 'Veraguas United FC',
        [settings.global_seo_title, settings.site_name],
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
                mainClassName="pt-0"
            >
                <HomeHero hero={homeMock.hero} videoUrl={settings.hero_video_url ?? null} />
                <QuickMatchBar lastResult={lastResult} nextMatch={nextMatch} />

                <main className="relative z-10 mx-auto max-w-7xl px-margin-mobile py-24 md:px-margin-desktop">
                    <div className="grid grid-cols-1 gap-16 lg:grid-cols-12 lg:gap-16">
                        <div className="lg:col-span-8">
                            <HomeNewsSection articles={newsItems} />
                            <AcademyBlock academy={homeMock.academy} />
                        </div>

                        <aside className="space-y-16 lg:col-span-4 lg:pl-2">
                            <StandingsCard standings={standings} />
                            <ShopPreview products={homeMock.shopPreview} />
                            <MembershipBanner membership={homeMock.membership} />
                        </aside>
                    </div>
                </main>

                <PartnersCarousel partners={partners} />
            </AppLayout>
        </>
    );
}

function normalizeNews(news = []) {
    const normalized = news.map((article) => ({
        title: article.title,
        slug: article.slug,
        summary: article.summary,
        imageUrl:
            article.featured_image_url ??
            'https://images.unsplash.com/photo-1517649763962-0c623066013b?auto=format&fit=crop&w=1200&q=80',
        href: `/noticias/${article.slug}`,
        categoryLabel: article.category?.name ? `OFICIAL / ${article.category.name}` : 'OFICIAL / NOTICIAS',
    }));

    if (normalized.length >= 3) {
        return normalized.slice(0, 3);
    }

    return [...normalized, ...defaultNewsItems].slice(0, 3);
}

function toMenuLinks(items = [], fallback = [], activeUrl = '') {
    if (!items.length) {
        return fallback;
    }

    return items.map((item) => ({
        label: item.label,
        url: item.url,
        active: item.url ? item.url === activeUrl : false,
        children: toMenuLinks(item.children ?? [], [], activeUrl),
    }));
}

const defaultNewsItems = [
    {
        title: "EL 'GATO' SE UNE AL SUENO INDIO",
        slug: 'el-gato',
        summary:
            'El delantero internacional panameno llega a Santiago para reforzar el frente de ataque.',
        imageUrl:
            'https://lh3.googleusercontent.com/aida-public/AB6AXuAf7AkFgpKlqh0kbfgEJrn9D2moNnXvGMv27p92JIx4oxATSUgEtgtc781CrTwrXWgQy8vg425iDE1FICvBJbtNi0aQRpmF6r_Dp4geUcPfXZlIorQ43xeSaKO6eIj7cgnEt43nxZ3_cU90_Dm1jBopQhXb8D8ERsHTXvw_4zQEshl9jj548mLcAYxfYivTYHZDi3JrgvHUgh-lkIqoqiQxoOf_t1ou_pJVWNYexZpFbFrzDv_LlJt5i1u6WwLFm5PfHJjTHF_bBcm6',
        href: '/noticias/el-gato',
        categoryLabel: 'OFICIAL / FICHAJES',
    },
    {
        title: '3 PUNTOS DE ORO EN CHITRE',
        slug: 'chitre',
        summary:
            'Veraguas United domina el mediocampo y se lleva una victoria crucial ante Herrera.',
        imageUrl:
            'https://lh3.googleusercontent.com/aida-public/AB6AXuDyCfPgU4crg3IXP92SnIFelmVUhQ9CYVG01UYbIaBUwu3CT01KrkWbef7J-niV5Mn3bxXrbRGhukgDEg5ZqEOZGbKV9VSfc9aaDQ5I6twDbiITpHQ3WXFycYQDBbXarzrEtEaIGNSB2c7bjVeT8P3khnB4UvzbSlYgsmc8joEg5G4dHf5r5xf3kwvaI4BIprc__oK_l8_9xQZ2A_Uy-qwGaaNdgjSe_mt46WfKQUFQIzAdpSMIdu5MP0QMaIiZKa7lUhtEMoNxJyFp',
        href: '/noticias/chitre',
        categoryLabel: 'CRONICA',
    },
    {
        title: 'CLINICA EN SANTIAGO: SEMILLERO INDIO',
        slug: 'semillero',
        summary:
            'Mas de 300 ninos participaron en la jornada de integracion con el primer equipo.',
        imageUrl:
            'https://lh3.googleusercontent.com/aida-public/AB6AXuB9aeovhReGLhXmq0ggpaxXTjA3qCKdTsnMR9VZrf8LsJI-PCu1wB6WJubgLw1Wp2aSJTP4-E5c2K-xNChuDai-wCDqHttgeGMZBQThCyBeGAxjcJZEY07ILUTlqf8row1en2od0V0pV6BE2fsy8Kfi60BCcZRDvujXhzT0H1ld4FMHNdOLYYhok4M1-M0KQMkch_KZiQK6XdW1Pk8kcrqJleIrAOngL3MZXvqSRmngLJQGj2cxBtJmOb9MBPeoiXaax6U4tUwgp07n',
        href: '/noticias/semillero',
        categoryLabel: 'SOCIAL',
    },
];
