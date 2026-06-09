import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import { publicPrimaryCta } from '@/config/publicNavigation';
import fanFestService from '@/services/fanFestService';
import FanFestHero from '@/components/fanfest/FanFestHero';

const FALLBACK_EVENT = {
    title:       'FanFest Veraguas United 2026',
    description: 'La gran fiesta del fútbol veragüense. Una jornada llena de música, gastronomía, deporte y la identidad de los Indios en su máxima expresión. Únete a la tribu y vive el FanFest.',
    event_date:  null,
    location:    'Estadio Agustín Muquita Sánchez, Santiago de Veraguas',
    hero_image_path: null,
    schedule: [
        { time: '15:00', activity: 'Apertura de puertas' },
        { time: '16:00', activity: 'Shows artísticos y activaciones de marca' },
        { time: '18:30', activity: 'Presentación del plantel' },
        { time: '19:00', activity: 'Partido oficial — Veraguas United' },
        { time: '21:00', activity: 'Cierre FanFest' },
    ],
    zones: [
        { id: 1, name: 'Zona Familiar',      description: 'Área recreativa con juegos y actividades para toda la familia.',  icon: 'family_restroom', sort_order: 1 },
        { id: 2, name: 'Zona Gastronómica',  description: 'Lo mejor de la gastronomía veragüense. Comida típica y bebidas.', icon: 'restaurant',      sort_order: 2 },
        { id: 3, name: 'Zona Deportiva',     description: 'Minifútbol, torneos de habilidad y retos deportivos en vivo.',    icon: 'sports_soccer',   sort_order: 3 },
        { id: 4, name: 'Zona Cultural',      description: 'Expresiones artísticas, música en vivo y folclore regional.',     icon: 'music_note',      sort_order: 4 },
    ],
};

export default function FanFest() {
    const [event, setEvent]           = useState(FALLBACK_EVENT);
    const [loading, setLoading]       = useState(true);
    const [noEvent, setNoEvent]       = useState(false);

    useEffect(() => {
        let active = true;

        async function load() {
            try {
                const [siteSettings, header, footer, fanfestRes] = await Promise.all([
                    fanFestService.getEvent(),
                ]);
                if (!active) return;

                const apiEvent = fanfestRes.data?.data ?? null;
                if (apiEvent && apiEvent.id) {
                    setEvent(apiEvent);
                    setNoEvent(false);
                } else {
                    setNoEvent(true);
                }
            } catch {
                if (!active) return;
            } finally {
                if (active) setLoading(false);
            }
        }

        load();
        return () => { active = false; };
    }, []);

    const pageTitle = 'FanFest | Veraguas United FC';

    const dateLabel = useMemo(() => {
        if (!event.event_date) return null;
        return new Intl.DateTimeFormat('es-PA', {
            weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit',
        }).format(new Date(event.event_date)).toUpperCase();
    }, [event.event_date]);

    return (
        <>
            <Head title={pageTitle} />
            <AppLayout
                navbarBrandName="VERAGUAS UNITED"
                navbarCtaLabel={publicPrimaryCta.label}
                navbarCtaHref={publicPrimaryCta.url}
                navbarCtaPending={publicPrimaryCta.pending}
                navbarCtaPendingLabel={publicPrimaryCta.pendingLabel}
                navbarVariant="light"
                mainClassName="pt-0"
            >
                <FanFestHero event={event} dateLabel={dateLabel} />

                {noEvent && !loading && (
                    <section className="section-space bg-surface">
                        <div className="page-shell max-w-7xl text-center">
                            <span className="material-symbols-outlined text-6xl text-accent/40">stadium</span>
                            <h2 className="mt-4 font-display text-3xl font-bold uppercase text-primary">
                                Próximamente
                            </h2>
                            <p className="mt-4 text-lg text-slate-500">
                                El próximo FanFest está en preparación. ¡Mantente atento a nuestras redes!
                            </p>
                        </div>
                    </section>
                )}

                {/* Zonas */}
                {event.zones?.length > 0 && (
                    <section className="section-space bg-white">
                        <div className="page-shell max-w-7xl">
                            <div className="mb-16">
                                <p className="text-lg font-bold uppercase tracking-[0.3em] text-accent">
                                    Experiencias del evento
                                </p>
                                <h2 className="mt-3 font-display text-4xl font-bold uppercase text-primary">
                                    Zonas FanFest
                                </h2>
                                <div className="mt-4 h-1.5 w-24 bg-accent" />
                            </div>

                            <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                                {event.zones.map((zone) => (
                                    <article
                                        key={zone.id}
                                        className="group rounded-2xl border border-slate-100 bg-surface p-8 transition-all hover:-translate-y-1 hover:bg-white hover:shadow-lg"
                                    >
                                        <div className="mb-6 flex h-14 w-14 items-center justify-center rounded-xl bg-primary/10 transition group-hover:bg-accent/10">
                                            <span className="material-symbols-outlined text-3xl text-primary group-hover:text-accent">
                                                {zone.icon ?? 'stadium'}
                                            </span>
                                        </div>
                                        <h3 className="font-display text-xl font-bold uppercase text-primary">
                                            {zone.name}
                                        </h3>
                                        {zone.description && (
                                            <p className="mt-3 text-sm leading-relaxed text-slate-600">
                                                {zone.description}
                                            </p>
                                        )}
                                    </article>
                                ))}
                            </div>
                        </div>
                    </section>
                )}

                {/* Programa */}
                {event.schedule?.length > 0 && (
                    <section className="section-space bg-surface">
                        <div className="page-shell max-w-7xl">
                            <div className="mb-16 text-center">
                                <h2 className="font-display text-4xl font-bold uppercase text-primary">
                                    Programa del evento
                                </h2>
                                <p className="mt-4 text-lg text-slate-500">
                                    Horario oficial de actividades
                                </p>
                            </div>

                            <div className="mx-auto max-w-2xl space-y-0">
                                {event.schedule.map((item, i) => (
                                    <div
                                        key={i}
                                        className="flex items-start gap-6 border-b border-slate-100 py-6 last:border-0"
                                    >
                                        <span className="min-w-[4.5rem] font-display text-2xl font-black text-accent">
                                            {item.time}
                                        </span>
                                        <p className="text-base font-semibold uppercase tracking-[0.1em] text-primary">
                                            {item.activity}
                                        </p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>
                )}

                {/* CTA */}
                <section className="section-space bg-primary text-white">
                    <div className="page-shell max-w-7xl text-center">
                        <h2 className="font-display text-4xl font-bold uppercase md:text-5xl">
                            ¿LISTO PARA EL FANFEST?
                        </h2>
                        <p className="mx-auto mt-6 max-w-xl text-lg leading-relaxed text-white/75">
                            Únete a La Tribu y vive la experiencia del fútbol veragüense en su máxima expresión.
                        </p>
                        <div className="mt-10 flex flex-wrap justify-center gap-4">
                            <a
                                href="/registro-tribu"
                                className="inline-flex items-center gap-2 rounded-md bg-accent px-8 py-4 font-display text-lg font-bold uppercase tracking-[0.15em] text-white transition hover:bg-white hover:text-primary"
                            >
                                Hacerse socio La Tribu
                            </a>
                            <a
                                href="/boletos"
                                className="inline-flex items-center gap-2 rounded-md border-2 border-white px-8 py-4 font-display text-lg font-bold uppercase tracking-[0.15em] text-white transition hover:bg-white hover:text-primary"
                            >
                                Ver boletos
                            </a>
                        </div>
                    </div>
                </section>
            </AppLayout>
        </>
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
