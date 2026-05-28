import { Head, Link } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import LoadingState from '@/components/common/LoadingState';
import ErrorState from '@/components/common/ErrorState';
import { fetchNewsBySlug } from '@/services/newsService';

export default function NewsShow({ slug }) {
    const [article, setArticle] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(false);

    useEffect(() => {
        let active = true;

        async function loadPage() {
            try {
                setLoading(true);
                const [siteSettings, header, footer, news] = await Promise.all([
                    fetchNewsBySlug(slug),
                ]);

                if (!active) {
                    return;
                }

                setArticle(normalizeArticle(news));
                setError(false);
            } catch {
                if (!active) {
                    return;
                }

                setArticle(null);
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
    }, [slug]);

    const pageTitle = useMemo(() => {
        if (article?.seoTitle) {
            return article.seoTitle;
        }

        return article?.title ? `${article.title} | Veraguas United FC` : 'Noticias | Veraguas United FC';
    }, [article]);

    return (
        <>
            <Head title={pageTitle} />
            <AppLayout
                navbarBrandName="VERAGUAS UNITED"
                navbarCtaLabel="UNETE A LA TRIBU"
                navbarVariant="light"
            >
                <section className="bg-surface pb-12 pt-40">
                    <div className="page-shell max-w-5xl">
                        <Link
                            href="/noticias"
                            className="inline-flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-accent transition-colors hover:text-primary"
                        >
                            <span className="material-symbols-outlined text-sm">arrow_back</span>
                            Volver a noticias
                        </Link>
                    </div>
                </section>

                <section className="pb-24 pt-10">
                    <div className="page-shell max-w-5xl">
                        {loading ? <LoadingState title="Cargando noticia" /> : null}

                        {!loading && error ? (
                            <ErrorState
                                title="No se pudo cargar esta noticia"
                                description="La noticia solicitada no esta disponible o la API devolvio un error."
                            />
                        ) : null}

                        {!loading && !error && article ? (
                            <article className="space-y-10">
                                <header className="space-y-5 border-b border-gray-200 pb-8">
                                    <div className="flex flex-wrap items-center gap-3">
                                        {article.categoryLabel ? (
                                            <span className="rounded-full bg-surface px-3 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-primary">
                                                {article.categoryLabel}
                                            </span>
                                        ) : null}
                                        {article.publishedAt ? (
                                            <span className="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">
                                                {article.publishedAt}
                                            </span>
                                        ) : null}
                                    </div>
                                    <h1 className="font-display text-5xl font-bold uppercase leading-tight tracking-tight text-primary md:text-7xl">
                                        {article.title}
                                    </h1>
                                    {article.summary ? (
                                        <p className="max-w-3xl text-xl leading-relaxed text-gray-600">
                                            {article.summary}
                                        </p>
                                    ) : null}
                                </header>

                                {article.imageUrl ? (
                                    <div className="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-md">
                                        <img
                                            src={article.imageUrl}
                                            alt={article.title}
                                            className="h-full max-h-[36rem] w-full object-cover"
                                        />
                                    </div>
                                ) : null}

                                <div className="rounded-lg border border-gray-200 bg-white p-8 shadow-sm md:p-12">
                                    <div className="space-y-6 text-lg leading-relaxed text-gray-700">
                                        {article.body.split('\n').filter(Boolean).map((paragraph, index) => (
                                            <p key={`${article.slug}-paragraph-${index}`}>{paragraph}</p>
                                        ))}
                                    </div>
                                </div>
                            </article>
                        ) : null}
                    </div>
                </section>
            </AppLayout>
        </>
    );
}

function normalizeArticle(article) {
    return {
        title: article.title,
        slug: article.slug,
        summary: article.summary,
        body: article.body ?? '',
        imageUrl: article.featured_image_url ?? null,
        categoryLabel: article.category?.name ? `OFICIAL / ${article.category.name}` : null,
        publishedAt: formatPublishedAt(article.published_at),
        seoTitle: article.seo_title,
    };
}

function formatPublishedAt(value) {
    if (!value) {
        return null;
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return new Intl.DateTimeFormat('es-PA', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    }).format(date);
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
