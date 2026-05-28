export default function QuickMatchBar({ lastResult, nextMatch }) {
    return (
        <section className="relative z-20 -mt-12 px-margin-mobile md:px-margin-desktop">
            <div className="mx-auto grid max-w-7xl grid-cols-1 gap-px overflow-hidden rounded-lg bg-gray-200 shadow-xl md:grid-cols-2">
                <div className="group flex cursor-pointer items-center justify-between bg-white p-8 transition-colors hover:bg-surface">
                    <div>
                        <span className="mb-3 block text-[11px] font-bold uppercase tracking-wider text-primary">
                            {lastResult.label}
                        </span>
                        <div className="flex items-center gap-8">
                            <div className="text-center">
                                <div className="font-display text-2xl text-primary">{lastResult.homeCode}</div>
                                <div className="text-4xl font-bold text-text-main">{lastResult.homeScore}</div>
                            </div>
                            <div className="font-bold text-gray-300">VS</div>
                            <div className="text-center">
                                <div className="font-display text-2xl text-gray-400">{lastResult.awayCode}</div>
                                <div className="text-4xl font-bold text-gray-400">{lastResult.awayScore}</div>
                            </div>
                        </div>
                    </div>
                    <div className="text-right">
                        <p className="text-sm font-bold uppercase italic text-accent">{lastResult.note}</p>
                        <p className="text-xs font-semibold text-gray-500">{lastResult.date}</p>
                    </div>
                </div>

                <div className="flex items-center justify-between bg-primary p-8 text-white">
                    <div>
                        <span className="mb-3 block text-[11px] font-bold uppercase tracking-wider text-accent">
                            {nextMatch.label}
                        </span>
                        <div className="flex items-center gap-8">
                            <div className="text-center">
                                <div className="font-display text-2xl">{nextMatch.homeCode}</div>
                                <div className="text-xs font-bold uppercase opacity-70">{nextMatch.homeName}</div>
                            </div>
                            <div className="text-4xl font-bold text-accent">VS</div>
                            <div className="text-center">
                                <div className="font-display text-2xl">{nextMatch.awayCode}</div>
                                <div className="text-xs font-bold uppercase opacity-70">{nextMatch.awayName}</div>
                            </div>
                        </div>
                    </div>
                    <div className="text-right">
                        <p className="text-sm font-bold uppercase text-accent">{nextMatch.note}</p>
                        <p className="text-sm font-bold">{nextMatch.date}</p>
                    </div>
                </div>
            </div>
        </section>
    );
}
