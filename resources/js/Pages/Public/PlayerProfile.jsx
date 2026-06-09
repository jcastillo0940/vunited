import { Head, Link } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import EmptyState from '@/components/common/EmptyState';
import ErrorState from '@/components/common/ErrorState';
import PlayerHero from '@/components/player/PlayerHero';
import PlayerStats from '@/components/player/PlayerStats';
import PlayerAttributeProfile from '@/components/player/PlayerAttributeProfile';
import PlayerBioCard from '@/components/player/PlayerBioCard';
import PlayerGallery from '@/components/player/PlayerGallery';
import playerService from '@/services/playerService';

export default function PlayerProfile({ slug }) {
    const [player, setPlayer]     = useState(null);
    const [loading, setLoading]   = useState(true);
    const [notFound, setNotFound] = useState(false);

    useEffect(() => {
        if (!slug) return;
        let active = true;
        setLoading(true);

        playerService
            .getPlayer(slug)
            .then((res) => {
                if (!active) return;
                const data = res.data?.data ?? res.data;
                if (data?.id) {
                    setPlayer(normalizeApiPlayer(data));
                    setNotFound(false);
                } else {
                    setNotFound(true);
                }
            })
            .catch((err) => {
                if (!active) return;
                if (err?.response?.status === 404) setNotFound(true);
            })
            .finally(() => {
                if (active) setLoading(false);
            });

        return () => { active = false; };
    }, [slug]);

    const pageTitle = useMemo(() => {
        if (!player) return 'Jugador | Veraguas United FC';
        return `${player.name} | Veraguas United FC`;
    }, [player]);

    return (
        <>
            <Head title={pageTitle} />
            <AppLayout
                navbarBrandName="VERAGUAS UNITED"
                navbarCtaLabel="SOCIO INDIO"
                navbarVariant="light"
                mainClassName="pt-28"
            >
                {notFound ? (
                    <section className="mx-auto max-w-7xl px-margin-mobile py-24 md:px-margin-desktop">
                        <ErrorState
                            title="Jugador no encontrado"
                            description="Este perfil no existe en la plantilla del club."
                        />
                    </section>
                ) : loading ? (
                    <section className="flex min-h-[60vh] items-center justify-center">
                        <span className="material-symbols-outlined animate-spin text-4xl text-accent">autorenew</span>
                    </section>
                ) : player ? (
                    <PlayerProfileContent player={player} />
                ) : null}
            </AppLayout>
        </>
    );
}

function PlayerProfileContent({ player }) {
    const { profile } = player;
    const hasStats      = Array.isArray(profile.stats) && profile.stats.length > 0;
    const hasAttributes = Array.isArray(profile.attributes) && profile.attributes.length > 0;
    const hasGallery    = Array.isArray(profile.gallery) && profile.gallery.length > 0;
    const hasAchievements = Array.isArray(profile.achievements) && profile.achievements.length > 0;

    return (
        <>
            <PlayerHero player={player} />

            <section className="mx-auto max-w-7xl px-margin-mobile py-20 md:px-margin-desktop">
                <div className="mb-8">
                    <Link
                        href="/plantilla"
                        className="inline-flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-accent transition-colors hover:text-primary"
                    >
                        <span className="material-symbols-outlined text-lg">arrow_back</span>
                        Volver a plantilla
                    </Link>
                </div>

                <div className="grid grid-cols-1 gap-12 lg:grid-cols-12">
                    {/* Columna principal */}
                    <div className="space-y-12 lg:col-span-8">
                        {hasStats && (
                            <>
                                <SectionTitle>Estadísticas de Temporada</SectionTitle>
                                <PlayerStats stats={profile.stats} />
                            </>
                        )}

                        {hasAttributes && (
                            <PlayerAttributeProfile attributes={profile.attributes} />
                        )}

                        {hasAchievements && (
                            <AchievementsSection achievements={profile.achievements} />
                        )}

                        {!hasStats && !hasAttributes && !hasAchievements && (
                            <EmptyState
                                title="Sin estadísticas disponibles"
                                description="Las estadísticas de este jugador aún no han sido cargadas."
                            />
                        )}
                    </div>

                    {/* Sidebar */}
                    <div className="lg:col-span-4">
                        <PlayerBioCard player={player} />
                    </div>
                </div>
            </section>

            {hasGallery ? (
                <PlayerGallery gallery={profile.gallery} />
            ) : null}
        </>
    );
}

function SectionTitle({ children }) {
    return (
        <div className="flex items-center gap-4 border-b border-gray-200 pb-4">
            <h2 className="font-display text-3xl font-bold uppercase tracking-tight text-primary">
                {children}
            </h2>
        </div>
    );
}

function AchievementsSection({ achievements }) {
    return (
        <section>
            <div className="mb-6 flex items-center gap-4 border-b border-gray-200 pb-4">
                <h2 className="font-display text-3xl font-bold uppercase tracking-tight text-primary">
                    Palmarés
                </h2>
                <span className="material-symbols-outlined text-accent">emoji_events</span>
            </div>
            <div className="rounded-xl border border-gray-200 bg-white shadow-md">
                <ul className="divide-y divide-gray-100">
                    {achievements.map((item, i) => {
                        const [title, detail] = item.split(' — ');
                        return (
                            <li key={i} className="flex items-center gap-4 px-6 py-4">
                                <span className="material-symbols-outlined text-lg text-accent">
                                    military_tech
                                </span>
                                <div>
                                    <p className="font-semibold text-primary">{title}</p>
                                    {detail && (
                                        <p className="text-sm text-gray-500">{detail}</p>
                                    )}
                                </div>
                            </li>
                        );
                    })}
                </ul>
            </div>
        </section>
    );
}

// ── Normalización desde la API ────────────────────────────────────────────────

function normalizeApiPlayer(p) {
    const parts = p.name.split(' ');

    // stats en DB es un objeto TM (tm_id, market_value, etc.) — convertir a array display
    const displayStats = buildDisplayStats(p);

    return {
        id:          p.id,
        slug:        p.slug,
        name:        p.name,
        firstName:   p.first_name  ?? parts[0] ?? p.name,
        lastName:    p.last_name   ?? parts.slice(1).join(' ') ?? '',
        position:    p.position    ?? '',
        positionKey: p.position_key ?? 'midfielder',
        number:      p.number      ?? '',
        nationality: p.nationality ?? '',
        imageUrl:    p.photo_path  ?? null,
        profile: {
            team:          categoryLabel(p.category),
            age:           p.birth_date ? calculateAge(p.birth_date) : null,
            height:        p.height        ?? null,
            weight:        p.weight        ?? null,
            dominantFoot:  p.dominant_foot ?? null,
            biography:     buildBiography(p),
            stats:         displayStats,
            attributes:    Array.isArray(p.attributes) ? p.attributes : [],
            achievements:  Array.isArray(p.achievements) ? p.achievements : [],
            gallery:       Array.isArray(p.gallery) ? p.gallery : [],
            socialActions: [
                { id: 'share',    icon: 'share',    label: 'Compartir' },
                { id: 'favorite', icon: 'favorite', label: 'Favorito' },
            ],
        },
    };
}

function buildDisplayStats(p) {
    // Si el admin cargó stats como array manual, úsalas directamente
    if (Array.isArray(p.stats) && p.stats.length) return p.stats;

    // Para jugadores TM: construir tarjetas de stats desde metadata
    const tmStats = typeof p.stats === 'object' && p.stats !== null ? p.stats : {};
    const result  = [];

    if (tmStats.market_value) {
        const k = tmStats.market_value >= 1_000_000
            ? `€${(tmStats.market_value / 1_000_000).toFixed(1)}M`
            : `€${Math.round(tmStats.market_value / 1_000)}K`;
        result.push({ key: 'market_value', label: 'Valor de Mercado', value: k, tone: 'accent' });
    }

    if (tmStats.contract) {
        const year = tmStats.contract.split('-')[0];
        result.push({ key: 'contract', label: 'Contrato hasta', value: year, tone: 'primary' });
    }

    if (p.birth_date) {
        result.push({ key: 'age', label: 'Edad', value: String(calculateAge(p.birth_date)), tone: 'neutral' });
    }

    if (tmStats.joined_on) {
        const year = tmStats.joined_on.split('-')[0];
        result.push({ key: 'joined', label: 'En el club desde', value: year, tone: 'neutral' });
    }

    return result;
}

function buildBiography(p) {
    // Si hay una biografía manual del admin, usarla (puede tener \n)
    if (p.biography) return p.biography;

    // Construir desde datos TM disponibles
    const tmStats = typeof p.stats === 'object' && p.stats !== null ? p.stats : {};
    const lines   = [];

    if (tmStats.signed_from && !tmStats.signed_from.includes('free transfer') && tmStats.signed_from !== 'Without Club') {
        lines.push(`Fichado de: ${tmStats.signed_from}`);
    }
    if (tmStats.joined_on) {
        lines.push(`Se incorporó el: ${formatDate(tmStats.joined_on)}`);
    }

    return lines.join('\n') || null;
}

function categoryLabel(cat) {
    return { 'first-team': 'Veraguas United FC', 'academy': 'Veraguas CD II', 'women-team': 'Equipo Femenino' }[cat] ?? 'Veraguas United FC';
}

function formatDate(dateStr) {
    if (!dateStr) return dateStr;
    const [y, m, d] = dateStr.split('-');
    const months = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    return `${parseInt(d)} ${months[parseInt(m) - 1]} ${y}`;
}

function calculateAge(birthDateStr) {
    const diff = Date.now() - new Date(birthDateStr).getTime();
    return Math.floor(diff / (1000 * 60 * 60 * 24 * 365.25));
}
