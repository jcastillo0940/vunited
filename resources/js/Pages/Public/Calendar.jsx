import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import CalendarHero from '@/components/calendar/CalendarHero';
import NextMatchCard from '@/components/calendar/NextMatchCard';
import MatchFilters from '@/components/calendar/MatchFilters';
import MatchList from '@/components/calendar/MatchList';
import SeasonSummary from '@/components/calendar/SeasonSummary';
import calendarMock from '@/mocks/calendarMock';
import homeMock from '@/mocks/homeMock';
import { fetchSiteSettings } from '@/services/siteService';
import { fetchMenu } from '@/services/menuService';
import matchService, { normalizeMatchForCalendar } from '@/services/matchService';
import {
    buildPublicFooterLinks,
    buildPublicHeaderLinks,
    publicLegalLinks,
    publicPrimaryCta,
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
    global_seo_title: 'Calendario | Veraguas United FC',
    global_seo_description: 'Calendario de partidos del Veraguas United FC. Próximos encuentros y resultados.',
    maintenance_mode: false,
};

export default function Calendar() {
    const defaultHeaderLinks = useMemo(() => buildPublicHeaderLinks('/calendario'), []);
    const defaultFooterLinks = useMemo(() => buildPublicFooterLinks(), []);
    const [settings, setSettings] = useState(fallbackSettings);
    const [headerMenu, setHeaderMenu] = useState(defaultHeaderLinks);
    const [footerMenu, setFooterMenu] = useState(defaultFooterLinks);
    const [selectedFilter, setSelectedFilter] = useState('Todos');
    const [matches, setMatches] = useState(calendarMock.matches);
    const [nextMatch, setNextMatch] = useState(calendarMock.nextMatch);
    const [seasonSummary, setSeasonSummary] = useState(calendarMock.seasonSummary);

    useEffect(() => {
        let active = true;

        async function loadShell() {
            try {
                const [siteSettings, header, footer] = await Promise.all([
                    fetchSiteSettings(),
                    fetchMenu('header'),
                    fetchMenu('footer'),
                ]);

                if (!active) {
                    return;
                }

                setSettings(siteSettings ?? fallbackSettings);
                setHeaderMenu(toMenuLinks(header?.items ?? [], defaultHeaderLinks, '/calendario'));
                setFooterMenu(toMenuLinks(footer?.items ?? [], defaultFooterLinks));

                const matchesRes = await matchService.getMatches().catch(() => null);
                const allMatches = matchesRes?.data?.data ?? [];
                if (allMatches.length) {
                    setMatches(allMatches.map(normalizeMatchForCalendar));
                    const upcoming = allMatches.find((m) => m.status === 'scheduled' || m.status === 'live');
                    if (upcoming) setNextMatch(normalizeMatchForCalendar(upcoming));
                    setSeasonSummary(buildSeasonSummary(allMatches));
                }
            } catch {
                if (!active) {
                    return;
                }

                setSettings(fallbackSettings);
                setHeaderMenu(defaultHeaderLinks);
                setFooterMenu(defaultFooterLinks);
            }
        }

        loadShell();

        return () => {
            active = false;
        };
    }, [defaultFooterLinks, defaultHeaderLinks]);

    const pageTitle = useMemo(
        () => settings.global_seo_title || 'Calendario | Veraguas United FC',
        [settings.global_seo_title],
    );

    const filteredMatches = useMemo(() => {
        switch (selectedFilter) {
            case 'Proximos':
                return matches.filter((m) => m.status === 'proximo');
            case 'Resultados':
                return matches.filter((m) => m.status === 'finalizado');
            case 'Local':
                return matches.filter((m) => m.venueType === 'Local');
            case 'Visitante':
                return matches.filter((m) => m.venueType === 'Visitante');
            default:
                return matches;
        }
    }, [selectedFilter, matches]);

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
                <CalendarHero hero={calendarMock.hero} />
                <NextMatchCard match={nextMatch} />

                <section className="pb-24 pt-16">
                    <div className="mx-auto max-w-7xl px-margin-mobile md:px-margin-desktop">
                        <div className="mb-12 flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                            <div>
                                <p className="text-sm font-bold uppercase tracking-[0.3em] text-accent">
                                    Agenda competitiva
                                </p>
                                <h2 className="mt-2 font-display text-5xl font-black uppercase text-primary">
                                    Partidos del torneo
                                </h2>
                                <p className="mt-4 max-w-2xl text-base leading-7 text-slate-500">
                                    Explora el ritmo de la temporada entre partidos en casa, salidas exigentes y resultados que marcan el camino del club.
                                </p>
                            </div>
                            <MatchFilters
                                filters={calendarMock.filters}
                                selectedFilter={selectedFilter}
                                onSelect={setSelectedFilter}
                            />
                        </div>

                        <div className="grid grid-cols-1 gap-10 lg:grid-cols-12">
                            <div className="lg:col-span-8">
                                <MatchList matches={filteredMatches} />
                            </div>
                            <div className="lg:col-span-4">
                                <SeasonSummary summary={seasonSummary} />
                            </div>
                        </div>
                    </div>
                </section>
            </AppLayout>
        </>
    );
}

function buildSeasonSummary(matches) {
    const VU = (m) => (m.home_team ?? '').toUpperCase().includes('VERAGUAS')
        || m.home_club?.slug?.includes('veraguas')
        || m.away_club?.slug?.includes('veraguas');

    const finished = matches.filter((m) => m.status === 'finished');
    const upcoming = matches.filter((m) => m.status === 'scheduled' || m.status === 'live');

    let wins = 0, draws = 0, losses = 0;
    finished.forEach((m) => {
        const isHome = (m.home_team ?? '').toUpperCase().includes('VERAGUAS') || m.home_club?.slug?.includes('veraguas');
        const vuG  = isHome ? (m.home_score ?? 0) : (m.away_score ?? 0);
        const rivG = isHome ? (m.away_score ?? 0) : (m.home_score ?? 0);
        if (vuG > rivG) wins++;
        else if (vuG === rivG) draws++;
        else losses++;
    });

    return [
        { label: 'Partidos jugados',    value: String(finished.length) },
        { label: 'Victorias',           value: String(wins) },
        { label: 'Empates',             value: String(draws) },
        { label: 'Derrotas',            value: String(losses) },
        { label: 'Próximos encuentros', value: String(upcoming.length) },
    ];
}

function toMenuLinks(items = [], fallback = [], activeUrl = '') {
    if (!items.length) {
        return fallback;
    }

    return items.map((item) => ({
        ...item,
        active: item.url ? item.url === activeUrl : false,
        children: toMenuLinks(item.children ?? [], [], activeUrl),
    }));
}
