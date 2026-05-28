import { Head, Link } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import NewsCard from '@/components/cards/NewsCard';
import LoadingState from '@/components/common/LoadingState';
import ErrorState from '@/components/common/ErrorState';
import EmptyState from '@/components/common/EmptyState';
import homeMock from '@/mocks/homeMock';
import { fetchSiteSettings } from '@/services/siteService';
import { fetchMenu } from '@/services/menuService';
import { fetchNews } from '@/services/newsService';
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
    global_seo_title: 'Noticias | Veraguas United FC',
    global_seo_description: 'Ultimas noticias de Veraguas United FC.',
    maintenance_mode: false,
};

export default function NewsIndex() {
    const defaultHeaderLinks = useMemo(() => buildPublicHeaderLinks('/noticias'), []);
    const defaultFooterLinks = useMemo(() => buildPublicFooterLinks(), []);
    const [settings, setSettings] = useState(fallbackSettings);
    const [headerMenu, setHeaderMenu] = useState(defaultHeaderLinks);
    const [footerMenu, setFooterMenu] = useState(defaultFooterLinks);
    const [articles, setArticles] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(false);

    useEffect(() => {
        let active = true;

        async function loadPage() {
            try {
                setLoading(true);
                const [siteSettings, header, footer, news] = await Promise.all([
                    fetchSiteSettings(),
                    fetchMenu('header'),
                    fetchMenu('footer'),
                    fetchNews(),
                ]);

                if (!active) {
                    return;
                }

                setSettings(siteSettings ?? fallbackSettings);
                setHeaderMenu(toMenuLinks(header?.items ?? [], defaultHeaderLinks));
                setFooterMenu(toMenuLinks(footer?.items ?? [], defaultFooterLinks));
                setArticles(normalizeNews(news));
                setError(false);
            } catch {
                if (!active) {
                    return;
                }

                setSettings(fallbackSettings);
                setHeaderMenu(defaultHeaderLinks);
                setFooterMenu(defaultFooterLinks);
                setArticles([]);
                setError(true);
            } finally {
                if (active) {
                    setLoading(false);
                }
            }
        }

        loadPage();

        return () => {
            active = false;
        };
    }, []);

    const pageTitle = useMemo(
        () => settings.global_seo_title || 'Noticias | Veraguas United FC',
        [settings.global_seo_title],
    );

    const featuredArticle = articles.find((article) => article.isFeatured) ?? articles[0] ?? null;
    const regularArticles = featuredArticle
        ? articles.filter((article) => article.slug !== featuredArticle.slug)
        : [];

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
            >
                <section className="bg-surface pb-12 pt-40">
                    <div className="page-shell max-w-7xl">
                        <p className="text-xs font-bold uppercase tracking-[0.3em] text-accent">
                            newsroom
                        </p>
                        <div className="mt-5 flex flex-col gap-6 border-b border-gray-200 pb-8 lg:flex-row lg:items-end lg:justify-between">
                            <div className="max-w-3xl">
                                <h1 className="font-display text-5xl font-bold uppercase tracking-tight text-primary md:text-7xl">
                                    CENTRO DE NOTICIAS
                                </h1>
                                <p className="mt-4 text-lg leading-relaxed text-gray-600">
                                    Actualidad, fichajes, cronicas y vida institucional del club en una sola portada editorial.
                                </p>
                            </div>
                            <Link
                                href="/"
                                className="inline-flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-accent transition-colors hover:text-primary"
                            >
                                Volver al inicio
                                <span className="material-symbols-outlined text-sm">arrow_back</span>
                            </Link>
                        </div>
                    </div>
                </section>

                <section className="pb-24 pt-12">
                    <div className="page-shell max-w-7xl">
                        {loading ? <LoadingState title="Cargando noticias" /> : null}

                        {!loading && error ? (
                            <ErrorState
                                title="No se pudieron cargar las noticias"
                                description="La API publica no devolvio contenido en este momento."
                            />
                        ) : null}

                        {!loading && !error && !articles.length ? (
                            <EmptyState
                                title="Sin noticias publicadas"
                                description="Todavia no hay articulos publicos disponibles."
                            />
                        ) : null}

                        {!loading && !error && articles.length ? (
                            <div className="space-y-10">
                                {featuredArticle ? (
                                    <NewsCard article={featuredArticle} variant="featured" />
                                ) : null}

                                {regularArticles.length ? (
                                    <div className="grid grid-cols-1 gap-8 md:grid-cols-2 xl:grid-cols-3">
                                        {regularArticles.map((article) => (
                                            <NewsCard key={article.slug} article={article} />
                                        ))}
                                    </div>
                                ) : null}
                            </div>
                        ) : null}
                    </div>
                </section>
            </AppLayout>
        </>
    );
}

function normalizeNews(news = []) {
    return news.map((article) => ({
        title: article.title,
        slug: article.slug,
        summary: article.summary,
        imageUrl:
            article.featured_image_url ??
            'https://images.unsplash.com/photo-1517649763962-0c623066013b?auto=format&fit=crop&w=1200&q=80',
        href: `/noticias/${article.slug}`,
        categoryLabel: article.category?.name ? `OFICIAL / ${article.category.name}` : 'OFICIAL / NOTICIAS',
        isFeatured: Boolean(article.is_featured),
        publishedAt: article.published_at,
    }));
}

function toMenuLinks(items = [], fallback = []) {
    if (!items.length) {
        return fallback;
    }

    return items.map((item) => ({
        label: item.label,
        url: item.url,
        active: item.url === '/noticias',
        children: toMenuLinks(item.children ?? []),
    }));
}
