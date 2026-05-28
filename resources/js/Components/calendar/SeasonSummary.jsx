export default function SeasonSummary({ summary }) {
    return (
        <aside className="rounded-xl border border-slate-200 bg-white p-8 shadow-md lg:sticky lg:top-40">
            <div className="mb-8 flex items-center justify-between">
                <div>
                    <p className="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">
                        Resumen de temporada
                    </p>
                    <h3 className="mt-2 font-display text-3xl font-bold uppercase text-primary">
                        Panorama LPF
                    </h3>
                </div>
                <span className="material-symbols-outlined text-3xl text-accent">leaderboard</span>
            </div>

            <div className="space-y-4">
                {summary.map((item) => (
                    <div
                        key={item.label}
                        className="flex items-center justify-between rounded-lg border border-slate-100 bg-surface px-4 py-4"
                    >
                        <span className="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">
                            {item.label}
                        </span>
                        <span className="font-display text-2xl font-black text-primary">
                            {item.value}
                        </span>
                    </div>
                ))}
            </div>

        </aside>
    );
}
