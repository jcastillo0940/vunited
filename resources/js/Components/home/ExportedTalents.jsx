import { useRef } from 'react';

export default function ExportedTalents({ players = [] }) {
    const trackRef = useRef(null);

    const scroll = (dir) => {
        trackRef.current?.scrollBy({ left: dir * 220, behavior: 'smooth' });
    };

    if (!players.length) return null;

    return (
        <div className="relative mt-16 overflow-hidden rounded-lg bg-primary px-8 pb-8 pt-8">
            {/* Header */}
            <div className="mb-6 flex items-end justify-between">
                <div>
                    <span className="text-[10px] font-bold uppercase tracking-[0.3em] text-accent">
                        Academia · Cantera India
                    </span>
                    <h2 className="mt-1 font-display text-2xl font-bold uppercase tracking-tight text-white">
                        TALENTOS QUE <span className="text-accent">EXPORTAMOS</span>
                    </h2>
                </div>
                <a
                    href="/fuerzas-basicas"
                    className="flex items-center gap-1 text-[10px] font-bold uppercase tracking-widest text-white/50 transition-colors hover:text-accent"
                >
                    Ver Academia
                    <span className="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>

            {/* Carousel track */}
            <div className="relative">
                <button
                    onClick={() => scroll(-1)}
                    className="absolute -left-3 top-1/2 z-10 -translate-y-1/2 rounded-full bg-white/10 p-1 text-white transition-colors hover:bg-accent"
                    aria-label="Anterior"
                >
                    <span className="material-symbols-outlined text-base leading-none">chevron_left</span>
                </button>
                <button
                    onClick={() => scroll(1)}
                    className="absolute -right-3 top-1/2 z-10 -translate-y-1/2 rounded-full bg-white/10 p-1 text-white transition-colors hover:bg-accent"
                    aria-label="Siguiente"
                >
                    <span className="material-symbols-outlined text-base leading-none">chevron_right</span>
                </button>

                <div
                    ref={trackRef}
                    className="flex gap-3 overflow-x-auto pb-1 [&::-webkit-scrollbar]:hidden [scroll-snap-type:x_mandatory]"
                >
                    {players.map((player) => (
                        <PlayerCard key={player.id} player={player} />
                    ))}
                </div>
            </div>
        </div>
    );
}

function PlayerCard({ player }) {
    const photoUrl = player.photo_path ?? null;
    const achievements = player.achievements ?? [];
    const firstAchievement = achievements[0] ?? null;

    const card = (
        <div
            className="w-40 flex-shrink-0 rounded-lg p-4 text-center transition-colors [scroll-snap-align:start] hover:bg-white/10"
            style={{ background: 'rgba(255,255,255,0.07)' }}
        >
            {/* Avatar */}
            <div className="relative mx-auto mb-4 h-14 w-14">
                {photoUrl ? (
                    <img
                        src={photoUrl}
                        alt={player.name}
                        onError={(e) => {
                            e.target.style.display = 'none';
                            e.target.nextSibling.style.display = 'flex';
                        }}
                        className="h-full w-full rounded-full border-2 border-accent object-cover"
                    />
                ) : null}
                <div
                    className="h-full w-full items-center justify-center rounded-full border-2 border-accent bg-white/10"
                    style={{ display: photoUrl ? 'none' : 'flex' }}
                >
                    <span className="material-symbols-outlined text-2xl text-white/40">person</span>
                </div>
                {/* Club logo overlay */}
                {player.foreign_club_logo && (
                    <img
                        src={player.foreign_club_logo}
                        alt={player.foreign_club}
                        className="absolute -bottom-2 -right-2 h-6 w-6 rounded-full border border-white/20 bg-white object-contain p-0.5"
                    />
                )}
                {/* Position badge (only when no club logo) */}
                {!player.foreign_club_logo && player.position && (
                    <span className="absolute -bottom-2 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-full bg-accent px-2 py-0.5 text-[8px] font-bold uppercase leading-tight text-white">
                        {player.position}
                    </span>
                )}
            </div>

            {/* Name */}
            <h4 className="mt-1 font-display text-sm font-bold uppercase leading-snug text-white">
                {player.name}
            </h4>

            {/* Position (when club logo is shown, position goes here as text) */}
            {player.foreign_club_logo && player.position && (
                <p className="text-[9px] text-white/40 uppercase tracking-wider">{player.position}</p>
            )}

            {/* Foreign club */}
            {player.foreign_club && (
                <p className="mt-1.5 text-[11px] font-bold text-accent">{player.foreign_club}</p>
            )}
            {(player.foreign_league || player.foreign_country) && (
                <p className="text-[9px] text-white/45">
                    {[player.foreign_league, player.foreign_country].filter(Boolean).join(' · ')}
                </p>
            )}

            {/* Top achievement */}
            {firstAchievement && (
                <p className="mt-2 border-t border-white/10 pt-2 text-[9px] leading-relaxed text-white/55">
                    {firstAchievement}
                </p>
            )}
        </div>
    );

    if (player.slug) {
        return (
            <a href={`/plantilla/${player.slug}`} className="block no-underline">
                {card}
            </a>
        );
    }

    return card;
}
