import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import homeMock from '@/mocks/homeMock';
import standingService from '@/services/standingService';
import { fetchSiteSettings } from '@/services/siteService';
import { fetchMenu } from '@/services/menuService';
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
    global_seo_title: 'Tabla de Posiciones | Veraguas United FC',
    global_seo_description: 'Tabla de posiciones LPF. Sigue la clasificación del Veraguas United FC en el torneo.',
    maintenance_mode: false,
};

const OWN_CLUB_SLUGS = ['veraguas-united-fc', 'veraguas-united'];

export default function Standings() {
    const defaultHeaderLinks = useMemo(() => buildPublicHeaderLinks(''), []);
    const defaultFooterLinks = useMemo(() => buildPublicFooterLinks(), []);
    const [settings, setSettings]     = useState(fallbackSettings);
    const [headerMenu, setHeaderMenu] = useState(defaultHeaderLinks);
    const [footerMenu, setFooterMenu] = useState(defaultFooterLinks);
    const [rows, setRows]             = useState([]);
    const [loading, setLoading]       = useState(true);
    const [competition, setCompetition] = useState('LPF');
    const [season, setSeason]           = useState(String(new Date().getFullYear()));

    useEffect(() => {
        let active = true;

        async function load() {
            try {
                const [siteSettings, header, footer, standingsRes] = await Promise.all([
                    fetchSiteSettings(),
                    fetchMenu('header'),
                    fetchMenu('footer'),
                    standingService.getStandings().catch(() => null),
                ]);

                if (!active) return;

                setSettings(siteSettings ?? fallbackSettings);
                setHeaderMenu(toMenuLinks(header?.items ?? [], defaultHeaderLinks));
                setFooterMenu(toMenuLinks(footer?.items ?? [], defaultFooterLinks));

                const data = standingsRes?.data?.data ?? [];
                setRows(data);
                if (data.length) {
                    setCompetition(data[0].competition ?? 'LPF');
                    setSeason(data[0].season ?? String(new Date().getFullYear()));
                }
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
        () => settings.global_seo_title || 'Tabla de Posiciones | Veraguas United FC',
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
            >
                <section className="section-space">
                    <div className="page-shell max-w-5xl">
                        <div className="mb-10">
                            <p className="text-sm font-bold uppercase tracking-[0.3em] text-accent">
                                {competition} — Temporada {season}
                            </p>
                            <h1 className="mt-2 font-display text-5xl font-black uppercase text-primary">
                                Tabla de Posiciones
                            </h1>
                        </div>

                        {loading ? (
                            <div className="py-20 text-center text-slate-400">Cargando...</div>
                        ) : rows.length === 0 ? (
                            <div className="py-20 text-center text-slate-400">
                                Sin datos disponibles para esta temporada.
                            </div>
                        ) : (
                            <div className="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-md">
                                <table className="w-full border-collapse text-sm">
                                    <thead>
                                        <tr className="border-b border-slate-100 bg-slate-50 text-left text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">
                                            <th className="px-4 py-4 text-center">POS</th>
                                            <th className="px-4 py-4">Club</th>
                                            <th className="px-4 py-4 text-center">PJ</th>
                                            <th className="px-4 py-4 text-center">G</th>
                                            <th className="px-4 py-4 text-center">E</th>
                                            <th className="px-4 py-4 text-center">P</th>
                                            <th className="px-4 py-4 text-center">GF</th>
                                            <th className="px-4 py-4 text-center">GC</th>
                                            <th className="px-4 py-4 text-center">DIF</th>
                                            <th className="px-4 py-4 text-center font-black text-primary">PTS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {rows.map((row) => {
                                            const isOwn = OWN_CLUB_SLUGS.includes(row.club?.slug ?? '');
                                            const diff  = row.goal_difference >= 0
                                                ? `+${row.goal_difference}`
                                                : String(row.goal_difference);

                                            return (
                                                <tr
                                                    key={`${row.position}-${row.club?.slug}`}
                                                    className={[
                                                        'border-b border-slate-100 transition-colors',
                                                        isOwn
                                                            ? 'bg-primary font-bold text-white'
                                                            : 'hover:bg-slate-50',
                                                    ].join(' ')}
                                                >
                                                    <td className={['px-4 py-4 text-center font-display text-lg font-black', isOwn ? 'text-accent' : 'text-slate-400'].join(' ')}>
                                                        {row.position}
                                                    </td>
                                                    <td className="px-4 py-4 font-semibold">
                                                        {row.club?.name ?? '—'}
                                                    </td>
                                                    <td className="px-4 py-4 text-center">{row.played}</td>
                                                    <td className="px-4 py-4 text-center">{row.won}</td>
                                                    <td className="px-4 py-4 text-center">{row.drawn}</td>
                                                    <td className="px-4 py-4 text-center">{row.lost}</td>
                                                    <td className="px-4 py-4 text-center">{row.goals_for}</td>
                                                    <td className="px-4 py-4 text-center">{row.goals_against}</td>
                                                    <td className={['px-4 py-4 text-center font-semibold', isOwn ? 'text-white/80' : row.goal_difference > 0 ? 'text-green-600' : row.goal_difference < 0 ? 'text-red-500' : 'text-slate-400'].join(' ')}>
                                                        {diff}
                                                    </td>
                                                    <td className={['px-4 py-4 text-center font-display text-lg font-black', isOwn ? 'text-accent' : 'text-primary'].join(' ')}>
                                                        {row.points}
                                                    </td>
                                                </tr>
                                            );
                                        })}
                                    </tbody>
                                </table>
                            </div>
                        )}
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
        active:   item.url ? item.url === activeUrl : false,
        children: toMenuLinks(item.children ?? [], [], activeUrl),
    }));
}
