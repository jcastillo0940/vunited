import { Head, Link } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import EmptyState from '@/components/common/EmptyState';
import ErrorState from '@/components/common/ErrorState';
import { getPlayerBySlug } from '@/mocks/playersMock';
import PlayerHero from '@/components/player/PlayerHero';
import PlayerStats from '@/components/player/PlayerStats';
import PlayerAttributeProfile from '@/components/player/PlayerAttributeProfile';
import PlayerBioCard from '@/components/player/PlayerBioCard';
import PlayerGallery from '@/components/player/PlayerGallery';
import playerService from '@/services/playerService';

export default function PlayerProfile({ slug }) {
    const [player, setPlayer] = useState(() => {
        const mock = getPlayerBySlug(slug);
        return mock ?? null;
    });
    const [notFound, setNotFound] = useState(false);

    useEffect(() => {
        let active = true;

        async function loadShell() {
            try {
                const [siteSettings, header, footer] = await Promise.all([
                ]);
                if (!active) return;
            } catch {
                if (!active) return;
            }
        }

        loadShell();
        return () => { active = false; };
    }, []);

    useEffect(() => {
        if (!slug) return;
        let active = true;

        playerService
            .getPlayer(slug)
            .then((res) => {
                if (!active) return;
                const data = res.data?.data ?? res.data;
                if (data && data.id) {
                    setPlayer(normalizeApiPlayer(data));
                    setNotFound(false);
                }
            })
            .catch((err) => {
                if (!active) return;
                // 404 from API: if we have a mock fallback keep it, otherwise mark not found
                if (err?.response?.status === 404 && !getPlayerBySlug(slug)) {
                    setNotFound(true);
                    setPlayer(null);
                }
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
                {notFound || (!player && !getPlayerBySlug(slug)) ? (
                    <section className="mx-auto max-w-7xl px-margin-mobile py-24 md:px-margin-desktop">
                        <ErrorState
                            title="Jugador no encontrado"
                            description="Este perfil no existe en la plantilla del club."
                        />
                    </section>
                ) : player ? (
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
                                <div className="space-y-12 lg:col-span-8">
                                    <div className="flex items-center gap-4 border-b border-gray-200 pb-4">
                                        <h2 className="font-display text-3xl font-bold uppercase tracking-tight text-primary">
                                            Estadísticas de Temporada
                                        </h2>
                                    </div>
                                    <PlayerStats stats={player.profile.stats} />
                                    <PlayerAttributeProfile attributes={player.profile.attributes} />
                                </div>

                                <div className="lg:col-span-4">
                                    <PlayerBioCard player={player} />
                                </div>
                            </div>
                        </section>

                        {player.profile.gallery?.length ? (
                            <PlayerGallery gallery={player.profile.gallery} />
                        ) : (
                            <section className="mx-auto max-w-7xl px-margin-mobile py-24 md:px-margin-desktop">
                                <EmptyState
                                    title="Sin multimedia disponible"
                                    description="Este jugador aún no tiene una galería asociada."
                                />
                            </section>
                        )}
                    </>
                ) : (
                    <section className="mx-auto max-w-7xl px-margin-mobile py-24 md:px-margin-desktop">
                        <div className="flex justify-center">
                            <span className="material-symbols-outlined animate-spin text-4xl text-accent">autorenew</span>
                        </div>
                    </section>
                )}
            </AppLayout>
        </>
    );
}

function normalizeApiPlayer(p) {
    const parts = p.name.split(' ');
    return {
        id:          p.id,
        slug:        p.slug,
        name:        p.name,
        firstName:   p.first_name ?? parts[0] ?? p.name,
        lastName:    p.last_name  ?? parts.slice(1).join(' ') ?? '',
        position:    p.position ?? '',
        positionKey: p.position_key ?? 'midfielder',
        number:      p.number ?? '',
        nationality: p.nationality ?? '',
        imageUrl:    p.photo_path ?? null,
        profile: {
            team:          'Veraguas United FC',
            age:           p.birth_date ? calculateAge(p.birth_date) : null,
            height:        p.height ?? null,
            weight:        p.weight ?? null,
            dominantFoot:  p.dominant_foot ?? null,
            biography:     p.biography ?? '',
            stats:         p.stats ?? [],
            attributes:    p.attributes ?? [],
            gallery:       p.gallery ?? [],
            socialActions: [
                { id: 'share', icon: 'share', label: 'Compartir' },
                { id: 'favorite', icon: 'favorite', label: 'Favorito' },
            ],
        },
    };
}

function calculateAge(birthDateStr) {
    const diff = Date.now() - new Date(birthDateStr).getTime();
    return Math.floor(diff / (1000 * 60 * 60 * 24 * 365.25));
}

function toMenuLinks(items = [], fallback = []) {
    if (!items.length) return fallback;
    return items.map((item) => ({
        label:    item.label,
        url:      item.url,
        active:   item.url === '/plantilla',
        children: toMenuLinks(item.children ?? []),
    }));
}
