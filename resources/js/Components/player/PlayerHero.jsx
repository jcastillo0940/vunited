import CTAButton from '@/components/common/CTAButton';

export default function PlayerHero({ player }) {
    return (
        <section className="relative flex min-h-[60vh] items-end overflow-hidden bg-primary">
            {/* Background */}
            <div className="absolute inset-0 z-0">
                {player.imageUrl ? (
                    <img
                        alt={player.name}
                        src={player.imageUrl}
                        className="h-full w-full object-cover object-top opacity-60"
                        onError={(e) => { e.target.style.display = 'none'; }}
                    />
                ) : (
                    <div className="flex h-full w-full items-end justify-end pr-12 pb-0 opacity-10">
                        <span className="material-symbols-outlined text-[28rem] text-white leading-none">person</span>
                    </div>
                )}
                <div className="absolute inset-0 bg-[linear-gradient(to_top,rgba(8,30,75,0.98)_0%,rgba(8,30,75,0.7)_50%,rgba(8,30,75,0.3)_100%)]" />
            </div>

            {/* Content */}
            <div className="relative z-10 mx-auto w-full max-w-7xl px-margin-mobile pb-14 md:px-margin-desktop">
                <div className="flex flex-col gap-8 md:flex-row md:items-end md:justify-between">
                    <div className="space-y-3">
                        <div className="flex items-center gap-3">
                            <span className="inline-block rounded-sm bg-accent px-4 py-1 font-display text-base font-bold uppercase text-white">
                                {player.position}
                            </span>
                            {player.number && (
                                <span className="font-display text-2xl font-black italic text-white/40">
                                    #{player.number}
                                </span>
                            )}
                        </div>
                        <h1 className="font-display text-6xl font-black uppercase leading-none tracking-tight text-white md:text-8xl">
                            {player.firstName}
                            <br />
                            <span className="text-white/70">{player.lastName}</span>
                        </h1>
                        <div className="flex items-center gap-3 font-display text-lg text-white/60">
                            <span className="material-symbols-outlined text-accent text-base">flag</span>
                            <span>{player.nationality}</span>
                            {player.profile.team && (
                                <>
                                    <span className="h-[2px] w-8 bg-accent/40" />
                                    <span>{player.profile.team}</span>
                                </>
                            )}
                        </div>
                    </div>

                    <div className="flex flex-wrap gap-3">
                        <CTAButton variant="primary" size="md" className="gap-2">
                            <span className="material-symbols-outlined text-base">share</span>
                            Compartir perfil
                        </CTAButton>
                    </div>
                </div>
            </div>
        </section>
    );
}
