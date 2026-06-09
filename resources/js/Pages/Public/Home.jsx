import homeMock from "@/mocks/homeMock";
import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import { useLayoutSettings } from '@/context/LayoutContext';
import HomeHero from '@/components/home/HomeHero';
import QuickMatchBar from '@/components/home/QuickMatchBar';
import HomeNewsSection from '@/components/home/HomeNewsSection';
import AcademyBlock from '@/components/home/AcademyBlock';
import ExportedTalents from '@/components/home/ExportedTalents';
import StandingsCard from '@/components/home/StandingsCard';
import ShopPreview from '@/components/home/ShopPreview';
import MembershipBanner from '@/components/home/MembershipBanner';
import PartnersCarousel from '@/components/home/PartnersCarousel';
import { fetchNews } from '@/services/newsService';
import matchService, { normalizeMatchForBar } from '@/services/matchService';
import standingService, { normalizeStandingsForCard } from '@/services/standingService';
import sponsorService from '@/services/sponsorService';
import playerService from '@/services/playerService';
import productService from '@/services/productService';

export default function Home() {
    const settings = useLayoutSettings();
    const [newsItems, setNewsItems] = useState([]);
    const [newsLoading, setNewsLoading] = useState(true);
    const [lastResult, setLastResult] = useState(homeMock.lastResult);
    const [nextMatch, setNextMatch] = useState(homeMock.nextMatch);
    const [standings, setStandings] = useState(homeMock.standings);
    const [partners, setPartners]   = useState(homeMock.partners);
    const [exportedPlayers, setExportedPlayers] = useState([]);
    const [shopProducts, setShopProducts] = useState([]);

    useEffect(() => {
        let active = true;

        async function loadHome() {
            try {
                const [news, featuredRes, finishedRes, standingsRes, sponsorsRes, exportedRes, productsRes] = await Promise.all([
                    fetchNews(),
                    matchService.getFeatured().catch(() => null),
                    matchService.getMatches({ status: 'finished' }).catch(() => null),
                    standingService.getStandings().catch(() => null),
                    sponsorService.getSponsors().catch(() => null),
                    playerService.getExportedPlayers().catch(() => null),
                    productService.getProducts({ limit: 5 }).catch(() => null),
                ]);

                if (!active) {
                    return;
                }

                setNewsItems(normalizeNews(news));
                setNewsLoading(false);

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

                const exportedRows = exportedRes?.data?.data ?? [];
                if (exportedRows.length) setExportedPlayers(exportedRows);

                const productRows = productsRes?.data?.data ?? [];
                if (productRows.length) setShopProducts(productRows);
            } catch {
                if (!active) {
                    return;
                }

                setNewsLoading(false);
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
                navbarBrandName="VERAGUAS UNITED"
                navbarCtaLabel="UNETE A LA TRIBU"
                navbarVariant="light"
                mainClassName="pt-0"
            >
                <HomeHero hero={homeMock.hero} />
                <QuickMatchBar lastResult={lastResult} nextMatch={nextMatch} />

                <main className="relative z-10 mx-auto max-w-7xl px-margin-mobile py-24 md:px-margin-desktop">
                    <div className="grid grid-cols-1 gap-16 lg:grid-cols-12 lg:gap-16">
                        <div className="lg:col-span-8">
                            <HomeNewsSection articles={newsItems} loading={newsLoading} />
                            <ExportedTalents players={exportedPlayers} />
                            <AcademyBlock academy={homeMock.academy} />
                        </div>

                        <aside className="flex flex-col gap-16 lg:col-span-4 lg:pl-2 lg:justify-between">
                            <StandingsCard standings={standings} />
                            <ShopPreview products={shopProducts} />
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
    return news.slice(0, 3).map((article) => ({
        title: article.title,
        slug: article.slug,
        summary: article.summary,
        imageUrl:
            article.featured_image_url ??
            'https://images.unsplash.com/photo-1517649763962-0c623066013b?auto=format&fit=crop&w=1200&q=80',
        href: `/noticias/${article.slug}`,
        categoryLabel: article.category?.name ?? 'NOTICIAS',
    }));
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
