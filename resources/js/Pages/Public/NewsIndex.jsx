import { Head } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import NewsHero from '@/components/news/NewsHero';
import NewsCard from '@/components/cards/NewsCard';
import LoadingState from '@/components/common/LoadingState';
import ErrorState from '@/components/common/ErrorState';
import EmptyState from '@/components/common/EmptyState';
import { fetchNews } from '@/services/newsService';

export default function NewsIndex() {
    const [articles, setArticles] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(false);

    useEffect(() => {
        let active = true;

        async function loadPage() {
            try {
                setLoading(true);
                const news = await fetchNews();

                if (!active) {
                    return;
                }

                setArticles(normalizeNews(news));
                setError(false);
            } catch {
                if (!active) {
                    return;
                }

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

    const pageTitle = 'Noticias | Veraguas United FC';

    const featuredArticle = articles.find((article) => article.isFeatured) ?? articles[0] ?? null;
    const regularArticles = featuredArticle
        ? articles.filter((article) => article.slug !== featuredArticle.slug)
        : [];

    return (
        <>
            <Head title={pageTitle} />
            <AppLayout
                navbarBrandName="VERAGUAS UNITED"
                navbarCtaLabel="UNETE A LA TRIBU"
                navbarVariant="light"
            >
                <NewsHero />

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
                                    <div className="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
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
