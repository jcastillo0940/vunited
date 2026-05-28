import { Head, Link } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import HeroSection from '@/components/common/HeroSection';
import SectionTitle from '@/components/common/SectionTitle';
import LoadingState from '@/components/common/LoadingState';
import ErrorState from '@/components/common/ErrorState';
import EmptyState from '@/components/common/EmptyState';
import CTAButton from '@/components/common/CTAButton';
import homeMock from '@/mocks/homeMock';
import { fetchSiteSettings } from '@/services/siteService';
import { fetchMenu } from '@/services/menuService';
import { fetchPageBySlug } from '@/services/pageService';
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
    global_seo_title: 'Pagina | Veraguas United FC',
    global_seo_description: 'Contenido dinamico del CMS de Veraguas United FC.',
    maintenance_mode: false,
};

export default function CmsPage({ slug }) {
    const defaultHeaderLinks = useMemo(() => buildPublicHeaderLinks(), []);
    const defaultFooterLinks = useMemo(() => buildPublicFooterLinks(), []);
    const [settings, setSettings] = useState(fallbackSettings);
    const [headerMenu, setHeaderMenu] = useState(defaultHeaderLinks);
    const [footerMenu, setFooterMenu] = useState(defaultFooterLinks);
    const [page, setPage] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(false);

    useEffect(() => {
        let active = true;

        async function loadPage() {
            try {
                setLoading(true);

                const [siteSettings, header, footer, cmsPage] = await Promise.all([
                    fetchSiteSettings(),
                    fetchMenu('header'),
                    fetchMenu('footer'),
                    fetchPageBySlug(slug),
                ]);

                if (!active) {
                    return;
                }

                setSettings(siteSettings ?? fallbackSettings);
                setHeaderMenu(toMenuLinks(header?.items ?? [], defaultHeaderLinks, slug));
                setFooterMenu(toMenuLinks(footer?.items ?? [], defaultFooterLinks));
                setPage(normalizePage(cmsPage));
                setError(false);
            } catch {
                if (!active) {
                    return;
                }

                setSettings(fallbackSettings);
                setHeaderMenu(defaultHeaderLinks);
                setFooterMenu(defaultFooterLinks);
                setPage(null);
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
        if (page?.seoTitle) {
            return page.seoTitle;
        }

        return page?.title ? `${page.title} | Veraguas United FC` : 'Pagina | Veraguas United FC';
    }, [page]);

    const heroSection = page?.sections.find((section) => section.type === 'hero') ?? null;
    const contentSections = page?.sections.filter((section) => section.type !== 'hero') ?? [];

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
                {loading ? (
                    <section className="pt-40">
                        <div className="page-shell max-w-7xl">
                            <LoadingState title="Cargando pagina" />
                        </div>
                    </section>
                ) : null}

                {!loading && error ? (
                    <section className="pt-40">
                        <div className="page-shell max-w-7xl">
                            <ErrorState
                                title="No se pudo cargar esta pagina"
                                description="El slug solicitado no esta disponible o la API no devolvio contenido."
                            />
                        </div>
                    </section>
                ) : null}

                {!loading && !error && page ? (
                    <>
                        {heroSection ? (
                            <HeroSection
                                eyebrow={heroSection.payload?.eyebrow ?? page.publishedAtLabel ?? page.slug}
                                title={heroSection.title ?? page.title}
                                highlight={heroSection.payload?.highlight}
                                description={heroSection.body ?? page.excerpt}
                                imageUrl={heroSection.imageUrl}
                                primaryAction={toAction(heroSection.payload?.primary_cta)}
                                secondaryAction={toAction(heroSection.payload?.secondary_cta)}
                            />
                        ) : (
                            <section className="bg-surface pb-12 pt-40">
                                <div className="page-shell max-w-7xl">
                                    <div className="max-w-4xl border-b border-gray-200 pb-8">
                                        <p className="text-xs font-bold uppercase tracking-[0.3em] text-accent">
                                            pagina cms
                                        </p>
                                        <h1 className="mt-5 font-display text-5xl font-bold uppercase tracking-tight text-primary md:text-7xl">
                                            {page.title}
                                        </h1>
                                        {page.excerpt ? (
                                            <p className="mt-4 text-lg leading-relaxed text-gray-600">
                                                {page.excerpt}
                                            </p>
                                        ) : null}
                                    </div>
                                </div>
                            </section>
                        )}

                        <section className="pb-24 pt-12">
                            <div className="page-shell max-w-7xl">
                                <div className="mb-10">
                                    <Link
                                        href="/"
                                        className="inline-flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-accent transition-colors hover:text-primary"
                                    >
                                        <span className="material-symbols-outlined text-sm">arrow_back</span>
                                        Volver al inicio
                                    </Link>
                                </div>

                                {!contentSections.length ? (
                                    <EmptyState
                                        title="Sin secciones publicadas"
                                        description="Esta pagina existe, pero aun no tiene bloques visibles."
                                    />
                                ) : (
                                    <div className="space-y-20">
                                        {contentSections.map((section) => (
                                            <CmsSectionBlock key={`${section.sectionKey}-${section.sortOrder}`} section={section} />
                                        ))}
                                    </div>
                                )}
                            </div>
                        </section>
                    </>
                ) : null}
            </AppLayout>
        </>
    );
}

function CmsSectionBlock({ section }) {
    switch (section.type) {
        case 'text':
            return (
                <section className="rounded-lg border border-gray-200 bg-white p-8 shadow-sm md:p-12">
                    {section.title ? <SectionTitle title={section.title} align="stacked" /> : null}
                    {section.body ? (
                        <div className="mt-6 space-y-6 text-lg leading-relaxed text-gray-700">
                            {splitParagraphs(section.body).map((paragraph, index) => (
                                <p key={`${section.sectionKey}-text-${index}`}>{paragraph}</p>
                            ))}
                        </div>
                    ) : null}
                </section>
            );

        case 'text_image':
            return (
                <section className="grid gap-10 rounded-lg border border-gray-200 bg-white p-8 shadow-sm md:grid-cols-2 md:p-12">
                    <div className="space-y-6">
                        {section.title ? <SectionTitle title={section.title} align="stacked" /> : null}
                        {section.body ? (
                            <div className="space-y-6 text-lg leading-relaxed text-gray-700">
                                {splitParagraphs(section.body).map((paragraph, index) => (
                                    <p key={`${section.sectionKey}-text-image-${index}`}>{paragraph}</p>
                                ))}
                            </div>
                        ) : null}
                    </div>
                    <div className="overflow-hidden rounded-lg border border-gray-100 bg-surface">
                        {section.imageUrl ? (
                            <img
                                src={section.imageUrl}
                                alt={section.title ?? section.sectionKey}
                                className="h-full w-full object-cover"
                            />
                        ) : (
                            <div className="flex min-h-80 items-center justify-center text-primary">
                                <span className="material-symbols-outlined text-5xl">image</span>
                            </div>
                        )}
                    </div>
                </section>
            );

        case 'image':
            return (
                <section className="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                    {section.imageUrl ? (
                        <img
                            src={section.imageUrl}
                            alt={section.title ?? section.sectionKey}
                            className="h-full max-h-[40rem] w-full object-cover"
                        />
                    ) : (
                        <div className="flex min-h-96 items-center justify-center bg-surface text-primary">
                            <span className="material-symbols-outlined text-6xl">image</span>
                        </div>
                    )}
                </section>
            );

        case 'cta': {
            const cta = toAction(section.payload?.primary_cta) ?? { label: 'Conocer mas', href: null };

            return (
                <section className="rounded-lg bg-primary p-10 text-white shadow-xl md:p-12">
                    {section.title ? (
                        <h2 className="font-display text-4xl font-bold uppercase tracking-tight md:text-5xl">
                            {section.title}
                        </h2>
                    ) : null}
                    {section.body ? (
                        <p className="mt-5 max-w-3xl text-lg leading-relaxed text-white/85">
                            {section.body}
                        </p>
                    ) : null}
                    <div className="mt-8">
                        <CTAButton as={cta.href ? 'a' : 'button'} href={cta.href ?? undefined} type={cta.href ? undefined : 'button'} size="lg">
                            {cta.label}
                        </CTAButton>
                    </div>
                </section>
            );
        }

        case 'stats': {
            const stats = toArray(section.payload?.items);

            return (
                <section className="rounded-lg border border-gray-200 bg-surface p-8 md:p-12">
                    {section.title ? <SectionTitle title={section.title} align="stacked" /> : null}
                    {section.body ? (
                        <p className="mt-6 max-w-3xl text-lg leading-relaxed text-gray-600">
                            {section.body}
                        </p>
                    ) : null}
                    <div className="mt-10 grid grid-cols-2 gap-6 md:grid-cols-4">
                        {stats.map((item, index) => (
                            <div
                                key={`${section.sectionKey}-stat-${index}`}
                                className="rounded-lg border border-gray-100 bg-white p-6 text-center shadow-sm"
                            >
                                <div className="font-display text-4xl font-bold text-primary">
                                    {item.value ?? item.number ?? '--'}
                                </div>
                                <div className="text-[10px] font-bold uppercase tracking-wider text-gray-500">
                                    {item.label ?? item.title ?? 'Dato'}
                                </div>
                            </div>
                        ))}
                    </div>
                </section>
            );
        }

        case 'grid': {
            const items = toArray(section.payload?.items);

            return (
                <section className="space-y-8">
                    {section.title ? <SectionTitle title={section.title} align="stacked" /> : null}
                    {section.body ? (
                        <p className="max-w-3xl text-lg leading-relaxed text-gray-600">
                            {section.body}
                        </p>
                    ) : null}
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                        {items.map((item, index) => (
                            <div
                                key={`${section.sectionKey}-grid-${index}`}
                                className="rounded-lg border border-gray-200 bg-white p-8 shadow-sm"
                            >
                                <h3 className="font-display text-2xl font-bold uppercase text-primary">
                                    {item.title ?? item.label ?? `Bloque ${index + 1}`}
                                </h3>
                                {item.body ?? item.description ? (
                                    <p className="mt-4 text-sm leading-relaxed text-gray-600">
                                        {item.body ?? item.description}
                                    </p>
                                ) : null}
                            </div>
                        ))}
                    </div>
                </section>
            );
        }

        default:
            return (
                <section className="rounded-lg border border-dashed border-gray-300 bg-white p-8 shadow-sm">
                    <div className="flex items-start gap-4">
                        <div className="flex h-12 w-12 items-center justify-center rounded-full bg-surface text-primary">
                            <span className="material-symbols-outlined">view_compact_alt</span>
                        </div>
                        <div className="space-y-3">
                            <p className="text-xs font-bold uppercase tracking-[0.2em] text-accent">
                                seccion sin plantilla
                            </p>
                            <h3 className="font-display text-2xl font-bold uppercase text-primary">
                                {section.title ?? section.sectionKey}
                            </h3>
                            <p className="text-sm leading-relaxed text-gray-600">
                                Tipo recibido: <span className="font-bold text-primary">{section.type || 'desconocido'}</span>
                            </p>
                            {section.body ? (
                                <p className="text-sm leading-relaxed text-gray-600">{section.body}</p>
                            ) : null}
                        </div>
                    </div>
                </section>
            );
    }
}

function normalizePage(page) {
    return {
        title: page.title,
        slug: page.slug,
        excerpt: page.excerpt,
        seoTitle: page.seo_title,
        seoDescription: page.seo_description,
        publishedAtLabel: formatPublishedAt(page.published_at),
        sections: (page.sections ?? []).map((section) => ({
            sectionKey: section.section_key,
            type: section.type ?? inferType(section.section_key),
            title: section.title,
            body: section.body,
            payload: section.payload ?? {},
            sortOrder: section.sort_order ?? 0,
            imageUrl: section.image_url ?? null,
        })),
    };
}

function inferType(sectionKey = '') {
    const normalized = sectionKey.toLowerCase();

    if (normalized.includes('hero')) return 'hero';
    if (normalized.includes('stats')) return 'stats';
    if (normalized.includes('cta')) return 'cta';
    if (normalized.includes('image')) return 'image';
    if (normalized.includes('grid')) return 'grid';

    return 'text';
}

function toAction(input) {
    if (!input || typeof input !== 'object') {
        return null;
    }

    return {
        label: input.label ?? input.text ?? 'Ver mas',
        href: input.href ?? input.url ?? '#',
    };
}

function toArray(value) {
    return Array.isArray(value) ? value : [];
}

function splitParagraphs(text = '') {
    return text.split('\n').filter(Boolean);
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

function toMenuLinks(items = [], fallback = [], currentSlug = '') {
    if (!items.length) {
        return fallback;
    }

    return items.map((item) => ({
        label: item.label,
        url: item.url,
        active: item.url === `/pagina/${currentSlug}`,
        children: toMenuLinks(item.children ?? [], [], currentSlug),
    }));
}
