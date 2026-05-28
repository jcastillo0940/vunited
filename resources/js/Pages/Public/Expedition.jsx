import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import homeMock from '@/mocks/homeMock';
import expeditionService from '@/services/expeditionService';
import { fetchSiteSettings } from '@/services/siteService';
import { fetchMenu } from '@/services/menuService';
import {
    buildPublicFooterLinks,
    buildPublicHeaderLinks,
    publicLegalLinks,
    publicPrimaryCta,
} from '@/config/publicNavigation';

const FALLBACK_TRIPS = [
    {
        id: 1,
        title: 'Expedición India — Copa LPF',
        departure_location: 'Santiago de Veraguas, Terminal David',
        departure_time: null,
        return_time: null,
        price: '12.00',
        currency: 'USD',
        capacity: 45,
        available_seats: 28,
        is_available: true,
        metadata: { includes: 'Transporte ida y vuelta en bus con A/C', contact: 'hola@veraguasunited.test' },
        match: null,
    },
];

const fallbackSettings = {
    site_name: 'Veraguas United FC',
    site_tagline: 'Orgullo de Veraguas',
    primary_logo_url: null,
    secondary_logo_url: null,
    primary_color: '#1D428A',
    accent_color: '#5BC2E7',
    contact_email: 'hola@veraguasunited.test',
    contact_phone: '+507 6000-0000',
    global_seo_title: 'Expedición India | Veraguas United FC',
    global_seo_description: 'Viajes organizados para acompañar a los Indios.',
    maintenance_mode: false,
};

export default function Expedition() {
    const defaultHeaderLinks = useMemo(() => buildPublicHeaderLinks('/expedicion-india'), []);
    const defaultFooterLinks = useMemo(() => buildPublicFooterLinks(), []);
    const [settings, setSettings]     = useState(fallbackSettings);
    const [headerMenu, setHeaderMenu] = useState(defaultHeaderLinks);
    const [footerMenu, setFooterMenu] = useState(defaultFooterLinks);
    const [trips, setTrips]           = useState(FALLBACK_TRIPS);
    const [loading, setLoading]       = useState(true);

    useEffect(() => {
        let active = true;

        async function load() {
            try {
                const [siteSettings, header, footer, tripsRes] = await Promise.all([
                    fetchSiteSettings(),
                    fetchMenu('header'),
                    fetchMenu('footer'),
                    expeditionService.getTrips(),
                ]);
                if (!active) return;

                setSettings(siteSettings ?? fallbackSettings);
                setHeaderMenu(toMenuLinks(header?.items ?? [], defaultHeaderLinks, '/expedicion-india'));
                setFooterMenu(toMenuLinks(footer?.items ?? [], defaultFooterLinks));

                const apiTrips = tripsRes.data?.data ?? [];
                if (apiTrips.length) setTrips(apiTrips);
            } catch {
                if (!active) return;
                setSettings(fallbackSettings);
            } finally {
                if (active) setLoading(false);
            }
        }

        load();
        return () => { active = false; };
    }, []);

    const pageTitle = useMemo(
        () => settings.global_seo_title || 'Expedición India | Veraguas United FC',
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
                {/* Hero */}
                <section className="relative overflow-hidden bg-primary pb-20 pt-40 md:pb-28 md:pt-52">
                    <div className="absolute inset-0 bg-[linear-gradient(120deg,rgba(29,66,138,0.97)_0%,rgba(29,66,138,0.88)_100%)]" />

                    <div className="page-shell relative z-10 max-w-7xl">
                        <span className="inline-flex rounded-sm bg-accent px-4 py-2 text-xs font-bold uppercase tracking-[0.3em] text-white">
                            EXPEDICIÓN INDIANA
                        </span>

                        <h1 className="mt-6 font-display text-5xl font-bold uppercase leading-[0.9] text-white md:text-7xl lg:text-8xl">
                            VIAJA CON<br />
                            <span className="text-accent">LA TRIBU</span>
                        </h1>

                        <p className="mt-8 max-w-2xl border-l-2 border-accent/80 pl-6 text-base leading-8 text-white/80">
                            Buses organizados por el club para acompañar a los Indios en cada partido de visitante. Únete a la caravana india y vive el fútbol en su máxima expresión.
                        </p>

                        <div className="mt-8 flex flex-wrap gap-4 text-sm font-bold uppercase tracking-[0.2em] text-white/60">
                            <span className="flex items-center gap-2">
                                <span className="material-symbols-outlined text-accent text-lg">directions_bus</span>
                                Transporte ida y vuelta
                            </span>
                            <span className="flex items-center gap-2">
                                <span className="material-symbols-outlined text-accent text-lg">ac_unit</span>
                                Bus con A/C
                            </span>
                            <span className="flex items-center gap-2">
                                <span className="material-symbols-outlined text-accent text-lg">groups</span>
                                Comunidad india
                            </span>
                        </div>
                    </div>
                </section>

                {/* Trips list */}
                <section className="section-space bg-white">
                    <div className="page-shell max-w-7xl">
                        <div className="mb-16">
                            <p className="text-lg font-bold uppercase tracking-[0.3em] text-accent">
                                Próximas salidas
                            </p>
                            <h2 className="mt-3 font-display text-4xl font-bold uppercase text-primary">
                                Viajes Disponibles
                            </h2>
                            <div className="mt-4 h-1.5 w-24 bg-accent" />
                        </div>

                        {loading ? (
                            <div className="flex justify-center py-20">
                                <span className="material-symbols-outlined animate-spin text-4xl text-accent">autorenew</span>
                            </div>
                        ) : trips.length === 0 ? (
                            <div className="rounded-2xl border border-slate-100 bg-surface p-16 text-center">
                                <span className="material-symbols-outlined text-6xl text-accent/40">directions_bus</span>
                                <h3 className="mt-4 font-display text-2xl font-bold uppercase text-primary">
                                    Sin viajes programados
                                </h3>
                                <p className="mt-2 text-slate-500">
                                    Próximamente anunciaremos las salidas de la Expedición Indiana. ¡Mantente atento!
                                </p>
                            </div>
                        ) : (
                            <div className="grid gap-8 md:grid-cols-2">
                                {trips.map((trip) => (
                                    <TripCard key={trip.id} trip={trip} />
                                ))}
                            </div>
                        )}
                    </div>
                </section>

                {/* CTA */}
                <section className="section-space bg-surface">
                    <div className="page-shell max-w-7xl">
                        <div className="grid items-center gap-10 md:grid-cols-2">
                            <div>
                                <p className="text-lg font-bold uppercase tracking-[0.3em] text-accent">
                                    Reserva tu cupo
                                </p>
                                <h2 className="mt-3 font-display text-4xl font-bold uppercase text-primary">
                                    ¿LISTO PARA LA EXPEDICIÓN?
                                </h2>
                                <p className="mt-6 text-lg leading-relaxed text-slate-600">
                                    Contáctanos para reservar tu lugar en el próximo viaje de la Tribu Indiana. Cupos limitados.
                                </p>
                            </div>
                            <div className="flex flex-col gap-4">
                                <a
                                    href={`mailto:${settings.contact_email}`}
                                    className="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-8 py-4 font-display text-lg font-bold uppercase tracking-[0.15em] text-white transition hover:bg-accent"
                                >
                                    <span className="material-symbols-outlined text-xl">email</span>
                                    Contactar al club
                                </a>
                                <a
                                    href="/fanclub"
                                    className="inline-flex items-center justify-center gap-2 rounded-md border-2 border-primary px-8 py-4 font-display text-lg font-bold uppercase tracking-[0.15em] text-primary transition hover:bg-primary hover:text-white"
                                >
                                    Hacerse socio La Tribu
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
            </AppLayout>
        </>
    );
}

function TripCard({ trip }) {
    const dateLabel = trip.departure_time
        ? new Intl.DateTimeFormat('es-PA', {
            weekday: 'long', day: 'numeric', month: 'long', hour: '2-digit', minute: '2-digit',
          }).format(new Date(trip.departure_time)).toUpperCase()
        : null;

    const returnLabel = trip.return_time
        ? new Intl.DateTimeFormat('es-PA', { hour: '2-digit', minute: '2-digit' }).format(new Date(trip.return_time))
        : null;

    const soldOut = !trip.is_available && trip.available_seats === 0;
    const pctFull = trip.capacity > 0 ? Math.round(((trip.capacity - trip.available_seats) / trip.capacity) * 100) : 0;

    return (
        <article className="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-md transition-all hover:-translate-y-0.5 hover:shadow-xl">
            {/* Header */}
            <div className="bg-primary px-8 py-6">
                <div className="flex items-start justify-between gap-4">
                    <div>
                        <p className="text-xs font-bold uppercase tracking-[0.25em] text-accent">
                            {trip.match ? `${trip.match.home_team} vs ${trip.match.away_team}` : 'Expedición Indiana'}
                        </p>
                        <h3 className="mt-2 font-display text-2xl font-bold uppercase text-white">
                            {trip.title}
                        </h3>
                    </div>
                    <div className="text-right">
                        <span className="font-display text-3xl font-black text-accent">
                            ${Number(trip.price ?? 0).toFixed(2)}
                        </span>
                        <p className="text-xs text-white/60">{trip.currency}</p>
                    </div>
                </div>
            </div>

            {/* Body */}
            <div className="p-8 space-y-5">
                <div className="flex items-start gap-3">
                    <span className="material-symbols-outlined text-xl text-accent mt-0.5">location_on</span>
                    <div>
                        <p className="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Salida desde</p>
                        <p className="text-sm font-semibold text-text-main">{trip.departure_location}</p>
                    </div>
                </div>

                {dateLabel && (
                    <div className="flex items-start gap-3">
                        <span className="material-symbols-outlined text-xl text-accent mt-0.5">schedule</span>
                        <div>
                            <p className="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Fecha y hora</p>
                            <p className="text-sm font-semibold text-text-main">{dateLabel}</p>
                            {returnLabel && (
                                <p className="text-xs text-slate-500">Regreso estimado: {returnLabel}</p>
                            )}
                        </div>
                    </div>
                )}

                {trip.metadata?.includes && (
                    <div className="flex items-start gap-3">
                        <span className="material-symbols-outlined text-xl text-accent mt-0.5">check_circle</span>
                        <p className="text-sm text-slate-600">{trip.metadata.includes}</p>
                    </div>
                )}

                {/* Availability bar */}
                <div>
                    <div className="mb-2 flex items-center justify-between text-xs font-bold uppercase tracking-[0.15em]">
                        <span className="text-slate-500">Cupos</span>
                        <span className={soldOut ? 'text-red-600' : 'text-green-700'}>
                            {soldOut ? 'AGOTADO' : `${trip.available_seats} disponibles`}
                        </span>
                    </div>
                    <div className="h-2 w-full overflow-hidden rounded-full bg-slate-100">
                        <div
                            className="h-full rounded-full bg-accent transition-all"
                            style={{ width: `${pctFull}%` }}
                        />
                    </div>
                    <p className="mt-1 text-right text-[10px] text-slate-400">
                        {pctFull}% ocupado · {trip.capacity} total
                    </p>
                </div>

                {trip.metadata?.contact && (
                    <a
                        href={`mailto:${trip.metadata.contact}`}
                        className="inline-flex w-full items-center justify-center gap-2 rounded-md bg-primary px-6 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white transition hover:bg-accent"
                    >
                        <span className="material-symbols-outlined text-base">confirmation_number</span>
                        Reservar cupo
                    </a>
                )}
            </div>
        </article>
    );
}

function toMenuLinks(items = [], fallback = [], activeUrl = '') {
    if (!items.length) return fallback;
    return items.map((item) => ({
        ...item,
        active: item.url ? item.url === activeUrl : false,
        children: toMenuLinks(item.children ?? [], [], activeUrl),
    }));
}
