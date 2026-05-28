import { useLayoutSettings } from "@/context/LayoutContext";
import { useEffect, useMemo, useState } from 'react';
import { Head } from '@inertiajs/react';
import AppLayout from '@/components/layout/AppLayout';
import MainNavbar from '@/components/layout/MainNavbar';
import TopTicker from '@/components/layout/TopTicker';
import HeroSection from '@/components/common/HeroSection';
import SectionTitle from '@/components/common/SectionTitle';
import CTAButton from '@/components/common/CTAButton';
import LoadingState from '@/components/common/LoadingState';
import ErrorState from '@/components/common/ErrorState';
import EmptyState from '@/components/common/EmptyState';
import NewsCard from '@/components/cards/NewsCard';
import ProductCard from '@/components/cards/ProductCard';
import PlayerCard from '@/components/cards/PlayerCard';
import TicketCard from '@/components/cards/TicketCard';
import MembershipCard from '@/components/cards/MembershipCard';
import FormInput from '@/components/forms/FormInput';
import FileUploadBox from '@/components/forms/FileUploadBox';
import homeMock from '@/mocks/homeMock';
import { getAllPlayers } from '@/mocks/playersMock';
import { getAllSponsors } from '@/mocks/sponsorsMock';
import productsMock from '@/mocks/productsMock';
import ticketsMock from '@/mocks/ticketsMock';
import membershipsMock from '@/mocks/membershipsMock';
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
    global_seo_title: 'Veraguas United FC',
    global_seo_description: 'Base visual publica Phase 2.',
    maintenance_mode: false,
};

export default function StyleGuide() {
    const settings = useLayoutSettings();
    const defaultHeaderLinks = useMemo(() => buildPublicHeaderLinks(), []);
    const defaultFooterLinks = useMemo(() => buildPublicFooterLinks(), []);
    const [settings, setSettings] = useState(fallbackSettings);
    const [headerMenu, setHeaderMenu] = useState([]);
    const [footerMenu, setFooterMenu] = useState([]);
    const [newsItems, setNewsItems] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        let active = true;

        async function loadPublicData() {
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

                setSettings(siteSettings);
                setHeaderMenu(toMenuLinks(header?.items ?? []));
                setFooterMenu(toMenuLinks(footer?.items ?? []));
                setNewsItems(news.map(toNewsCardModel));
                setError(null);
            } catch (requestError) {
                if (!active) {
                    return;
                }

                setError(requestError);
                setHeaderMenu(defaultHeaderLinks);
                setFooterMenu(defaultFooterLinks);
                setNewsItems(defaultNewsItems);
            } finally {
                if (active) {
                    setLoading(false);
                }
            }
        }

        loadPublicData();

        return () => {
            active = false;
        };
    }, []);

    const pageTitle = useMemo(
        () => `${settings.site_name} - Style Guide`,
        [settings.site_name],
    );

    return (
        <>
            <Head title={pageTitle} />
            <AppLayout
                settings={settings}
                headerMenu={headerMenu.length ? headerMenu : defaultHeaderLinks}
                footerMenu={footerMenu.length ? footerMenu : defaultFooterLinks}
                legalMenu={publicLegalLinks}
                ticker={homeMock.ticker}
                navbarVariant="light"
            >
                <HeroSection {...homeMock.hero} />

                <section className="section-space">
                    <div className="page-shell space-y-16">
                        <SectionTitle
                            eyebrow="Phase 2A / 2B"
                            title="Sistema visual publico"
                            action={
                                <a
                                    href="#componentes"
                                    className="font-body text-sm font-bold uppercase tracking-athletic text-accent transition-colors hover:text-primary"
                                >
                                    Ver componentes
                                </a>
                            }
                        />

                        {loading ? <LoadingState title="Cargando datos publicos" /> : null}
                        {error ? (
                            <ErrorState
                                title="API publica no disponible"
                                description="Se activo el fallback mock para seguir validando la base visual."
                            />
                        ) : null}

                        <div className="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                            <TokenCard label="Primary" value="#1D428A" swatchClass="bg-primary" />
                            <TokenCard label="Accent" value="#5BC2E7" swatchClass="bg-accent" />
                            <TokenCard label="Surface" value="#F4F6F9" swatchClass="bg-surface" />
                            <TokenCard label="Text Main" value="#2B2B2B" swatchClass="bg-text-main" />
                        </div>
                    </div>
                </section>

                <section id="componentes" className="section-space bg-surface">
                    <div className="page-shell space-y-16">
                        <SectionTitle title="Shell variants" />
                        <div className="grid gap-10">
                            <div className="surface-panel overflow-hidden p-0">
                                <TopTicker
                                    fixed={false}
                                    clubLabel={settings.site_name}
                                    tickerLabel="Mock"
                                    tickerText="VS Atletico Chiriqui"
                                />
                                <MainNavbar
                                    fixed={false}
                                    variant="light"
                                    logoUrl={settings.primary_logo_url}
                                    brandName={settings.site_name}
                                    links={headerMenu.length ? headerMenu : defaultHeaderLinks}
                                />
                            </div>
                            <div className="surface-panel overflow-hidden p-0">
                                <TopTicker
                                    fixed={false}
                                    clubLabel={settings.site_name}
                                    tickerLabel="Mock"
                                    tickerText="VS Atletico Chiriqui"
                                />
                                <MainNavbar
                                    fixed={false}
                                    variant="solid"
                                    logoUrl={settings.primary_logo_url}
                                    brandName={settings.site_name}
                                    links={headerMenu.length ? headerMenu : defaultHeaderLinks}
                                />
                            </div>
                        </div>

                        <SectionTitle title="Botones y estados" />
                        <div className="surface-panel space-y-8 p-8">
                            <div className="flex flex-wrap gap-4">
                                <CTAButton variant="primary">Comprar boletos</CTAButton>
                                <CTAButton variant="secondary">Hazte miembro</CTAButton>
                                <CTAButton variant="outline">Ver plantilla</CTAButton>
                            </div>
                            <div className="grid gap-6 md:grid-cols-3">
                                <LoadingState title="Cargando noticias" />
                                <ErrorState />
                                <EmptyState />
                            </div>
                        </div>

                        <SectionTitle title="Cards editoriales y de negocio" />
                        <div className="grid gap-8 md:grid-cols-2">
                            <NewsCard article={newsItems[0] ?? defaultNewsItems[0]} variant="featured" />
                            <div className="grid gap-8 md:grid-cols-2">
                                {(newsItems.slice(1, 3).length
                                    ? newsItems.slice(1, 3)
                                    : defaultNewsItems.slice(1, 3)
                                ).map((article) => (
                                    <NewsCard key={article.slug} article={article} />
                                ))}
                            </div>
                        </div>
                        <div className="grid gap-8 md:grid-cols-2 xl:grid-cols-4">
                            {productsMock.map((product) => (
                                <ProductCard key={product.id} product={product} />
                            ))}
                            {getAllPlayers().slice(0, 1).map((player) => (
                                <PlayerCard key={player.id} player={player} />
                            ))}
                            {ticketsMock.slice(0, 1).map((ticket) => (
                                <TicketCard key={ticket.id} ticket={ticket} />
                            ))}
                            {membershipsMock.map((membership) => (
                                <MembershipCard key={membership.id} membership={membership} />
                            ))}
                        </div>

                        <SectionTitle title="Formulario base" />
                        <div className="grid gap-10 lg:grid-cols-[1.2fr_0.8fr]">
                            <div className="surface-panel space-y-8 p-8">
                                <div className="grid gap-8 md:grid-cols-2">
                                    <FormInput label="Nombre del contacto" placeholder="Ej. Juan Perez" />
                                    <FormInput
                                        label="Correo electronico"
                                        type="email"
                                        placeholder="hola@veraguasunited.test"
                                    />
                                </div>
                                <div className="grid gap-8 md:grid-cols-2">
                                    <FormInput
                                        label="Nivel de interes"
                                        type="select"
                                        options={[
                                            { label: 'Main Partner', value: 'main' },
                                            { label: 'Official Sponsor', value: 'official' },
                                            { label: 'Alianza Estrategica', value: 'alliance' },
                                        ]}
                                    />
                                    <FormInput
                                        label="Telefono"
                                        type="tel"
                                        placeholder="+507 6000-0000"
                                    />
                                </div>
                                <FormInput
                                    label="Mensaje"
                                    textarea
                                    rows={5}
                                    placeholder="Describe tu solicitud o patrocinio ideal."
                                />
                                <FileUploadBox />
                            </div>
                            <div className="surface-panel p-8">
                                <p className="display-kicker">Mock data</p>
                                <h3 className="mt-3 font-display text-3xl font-bold uppercase text-primary">
                                    Modulos pendientes de backend
                                </h3>
                                <ul className="mt-6 space-y-4 text-sm leading-relaxed text-gray-600">
                                    <li>Plantilla y perfiles de jugador</li>
                                    <li>Patrocinadores por tier</li>
                                    <li>Tienda y carrito</li>
                                    <li>Boletos y checkout visual</li>
                                    <li>Membresias y FanClub</li>
                                    <li>FanFest, buses y pruebas</li>
                                </ul>
                                <div className="mt-8 flex flex-wrap gap-3">
                                    {getAllSponsors().map((sponsor) => (
                                        <span
                                            key={sponsor.id}
                                            className="rounded-full bg-surface px-4 py-2 text-[10px] font-bold uppercase tracking-athletic text-primary"
                                        >
                                            {sponsor.name}
                                        </span>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </AppLayout>
        </>
    );
}

function TokenCard({ label, value, swatchClass }) {
    return (
        <div className="surface-card p-6">
            <div className={`h-20 rounded-lg ${swatchClass}`} />
            <div className="mt-5">
                <p className="display-kicker">{label}</p>
                <p className="mt-2 font-display text-2xl font-bold uppercase text-primary">
                    {value}
                </p>
            </div>
        </div>
    );
}

function toMenuLinks(items) {
    return items.map((item, index) => ({
        label: item.label,
        url: item.url,
        active: index === 0,
        children: toMenuLinks(item.children ?? []),
    }));
}

function toNewsCardModel(article) {
    return {
        title: article.title,
        slug: article.slug,
        summary: article.summary,
        imageUrl:
            article.featured_image_url ??
            'https://images.unsplash.com/photo-1517649763962-0c623066013b?auto=format&fit=crop&w=1200&q=80',
        href: `/noticias/${article.slug}`,
        categoryLabel: article.category?.name
            ? `OFICIAL / ${article.category.name}`
            : 'OFICIAL / NOTICIAS',
    };
}

const defaultNewsItems = [
    {
        title: "El 'Gato' se une al sueno indio",
        slug: 'el-gato',
        summary:
            'El delantero internacional llega a Santiago para reforzar el ataque en esta base visual.',
        imageUrl:
            'https://images.unsplash.com/photo-1543326727-cf6c39e8f84c?auto=format&fit=crop&w=1200&q=80',
        href: '/noticias/el-gato',
        categoryLabel: 'OFICIAL / FICHAJES',
    },
    {
        title: '3 puntos de oro en Chitre',
        slug: 'chitre',
        summary: 'Victoria crucial en condicion de visita con lectura tactica superior.',
        imageUrl:
            'https://images.unsplash.com/photo-1518604666860-9ed391f76460?auto=format&fit=crop&w=1200&q=80',
        href: '/noticias/chitre',
        categoryLabel: 'CRONICA',
    },
    {
        title: 'Semillero indio en Santiago',
        slug: 'semillero',
        summary: 'Mas de 300 ninos participaron en una clinica con el primer equipo.',
        imageUrl:
            'https://images.unsplash.com/photo-1517927033932-b3d18e61fb3a?auto=format&fit=crop&w=1200&q=80',
        href: '/noticias/semillero',
        categoryLabel: 'SOCIAL',
    },
];
