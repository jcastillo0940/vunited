import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import { publicPrimaryCta } from '@/config/publicNavigation';
import standingService from '@/services/standingService';

const OWN_CLUB_SLUGS = ['veraguas-united-fc', 'veraguas-united'];

export default function Standings() {
    const [rows, setRows]             = useState([]);
    const [loading, setLoading]       = useState(true);
    const [competition, setCompetition] = useState('LPF');
    const [season, setSeason]           = useState(String(new Date().getFullYear()));

    useEffect(() => {
        let active = true;

        async function load() {
            try {
                const [siteSettings, header, footer, standingsRes] = await Promise.all([
                    standingService.getStandings().catch(() => null),
                ]);

                if (!active) return;

                const data = standingsRes?.data?.data ?? [];
                setRows(data);
                if (data.length) {
                    setCompetition(data[0].competition ?? 'LPF');
                    setSeason(data[0].season ?? String(new Date().getFullYear()));
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

    const pageTitle = useMemo(
        () => settings.global_seo_title || 'Tabla de Posiciones | Veraguas United FC',
        [settings.global_seo_title],
    );

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
