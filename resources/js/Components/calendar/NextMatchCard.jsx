import CTAButton from '@/components/common/CTAButton';

export default function NextMatchCard({ match }) {
    return (
        <section className="relative z-20 -mt-12 px-margin-mobile md:px-margin-desktop">
            <div className="mx-auto max-w-7xl rounded-xl bg-primary px-8 py-10 text-white shadow-2xl md:px-12 md:py-12">
                <div className="grid grid-cols-1 items-center gap-10 lg:grid-cols-[minmax(0,1fr)_340px]">
                    <div>
                        <p className="mb-4 text-xs font-bold uppercase tracking-[0.28em] text-accent">
                            Proximo partido destacado
                        </p>
                        <h2 className="font-display text-4xl font-black uppercase text-accent md:text-5xl">
                            {match.competition}
                        </h2>
                        <div className="mt-5 flex flex-wrap items-center gap-6 text-sm font-bold uppercase tracking-wider text-white/90">
                            <span className="flex items-center gap-2">
                                <span className="material-symbols-outlined text-accent">calendar_today</span>
                                {match.dateLabel}
                            </span>
                            <span className="flex items-center gap-2">
                                <span className="material-symbols-outlined text-accent">schedule</span>
                                {match.timeLabel}
                            </span>
                            <span className="flex items-center gap-2">
                                <span className="material-symbols-outlined text-accent">stadium</span>
                                {match.stadium}
                            </span>
                        </div>
                    </div>

                    <div className="rounded-xl border border-white/10 bg-white/10 p-6 backdrop-blur-sm">
                        <div className="flex items-center justify-between gap-4">
                            <div className="text-center">
                                <div className="mb-3 grid h-20 w-20 place-items-center rounded-full bg-white/10 font-display text-2xl font-bold text-white">
                                    {match.homeShort}
                                </div>
                                <p className="font-display text-sm font-bold uppercase tracking-tight">
                                    {match.homeTeam}
                                </p>
                            </div>

                            <div className="text-center">
                                <p className="font-display text-4xl font-black italic text-accent">VS</p>
                                <p className="mt-2 text-[10px] font-bold uppercase tracking-[0.22em] text-white/60">
                                    {match.venueType}
                                </p>
                            </div>

                            <div className="text-center">
                                <div className="mb-3 grid h-20 w-20 place-items-center rounded-full bg-white/10 font-display text-2xl font-bold text-white">
                                    {match.awayShort}
                                </div>
                                <p className="font-display text-sm font-bold uppercase tracking-tight">
                                    {match.awayTeam}
                                </p>
                            </div>
                        </div>

                        {match.ctaHref ? (
                            <CTAButton
                                as="a"
                                href={match.ctaHref}
                                size="lg"
                                className="mt-8 w-full justify-center font-display text-lg tracking-[0.16em]"
                            >
                                {match.ctaLabel}
                            </CTAButton>
                        ) : null}
                    </div>
                </div>
            </div>
        </section>
    );
}
