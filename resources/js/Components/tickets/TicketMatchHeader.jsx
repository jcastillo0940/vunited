export default function TicketMatchHeader({ match }) {
    return (
        <section className="mb-12">
            <div className="relative overflow-hidden rounded-xl bg-primary p-8 text-white shadow-panel md:p-12">
                <div className="flex flex-col items-center justify-between gap-8 md:flex-row">
                    <div className="text-center md:text-left">
                        <h1 className="font-display text-4xl font-black uppercase leading-tight text-accent md:text-6xl">
                            {match.competition}
                        </h1>
                        <div className="mt-5 flex flex-col items-center gap-4 text-sm font-bold uppercase tracking-[0.22em] text-white/90 md:flex-row md:justify-start">
                            <div className="flex items-center gap-2">
                                <span className="material-symbols-outlined text-accent">
                                    calendar_today
                                </span>
                                {match.dateLabel}
                            </div>
                            <div className="flex items-center gap-2">
                                <span className="material-symbols-outlined text-accent">
                                    schedule
                                </span>
                                {match.timeLabel}
                            </div>
                        </div>
                        <div className="mt-6 flex items-center justify-center gap-3 md:justify-start">
                            <span className="material-symbols-outlined text-3xl text-accent">
                                stadium
                            </span>
                            <span className="font-display text-xl uppercase md:text-2xl">
                                {match.stadium}
                            </span>
                        </div>
                        {match.status === 'finished' ? (
                            <div className="mt-6 inline-flex rounded-full bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.24em] text-accent">
                                Finalizado {match.homeScore} - {match.awayScore}
                            </div>
                        ) : null}
                    </div>

                    <div className="flex items-center gap-6 rounded-xl border border-white/10 bg-white/10 p-6 backdrop-blur-sm md:gap-12 md:p-8">
                        <div className="text-center">
                            <div className="mb-3 flex h-24 w-24 items-center justify-center rounded-full bg-white/10 text-xl font-display font-bold text-white shadow-lg">
                                {match.homeLogoLabel}
                            </div>
                            <p className="font-display text-sm font-bold tracking-tight">
                                {match.homeTeam}
                            </p>
                        </div>
                        <div className="font-display text-4xl font-black italic text-accent">VS</div>
                        <div className="text-center">
                            <div className="mb-3 flex h-24 w-24 items-center justify-center rounded-full bg-white/10 text-xl font-display font-bold text-white shadow-lg">
                                {match.awayLogoLabel}
                            </div>
                            <p className="font-display text-sm font-bold tracking-tight">
                                {match.awayTeam}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
