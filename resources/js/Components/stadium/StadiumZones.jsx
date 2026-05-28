export default function StadiumZones({ zones }) {
    return (
        <section className="section-space bg-surface">
            <div className="page-shell max-w-7xl">
                <div className="mb-12 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p className="text-sm font-bold uppercase tracking-[0.3em] text-accent">
                            Recorrido del recinto
                        </p>
                        <h2 className="mt-2 font-display text-5xl font-black uppercase text-primary">
                            Zonas del estadio
                        </h2>
                    </div>
                    <p className="max-w-xl text-base leading-7 text-slate-500">
                        Conoce las zonas del recinto, accesos y la experiencia de cada tribuna.
                    </p>
                </div>

                <div className="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
                    {zones.map((zone) => (
                        <article key={zone.id} className="rounded-xl border border-slate-200 bg-white p-8 shadow-md">
                            <div className="mb-6 flex items-start justify-between gap-4">
                                <span className="rounded-full bg-surface px-3 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">
                                    {zone.badge}
                                </span>
                                <span className="material-symbols-outlined text-accent">event_seat</span>
                            </div>
                            <h3 className="font-display text-2xl font-black uppercase text-primary">
                                {zone.name}
                            </h3>
                            <p className="mt-4 text-sm leading-6 text-slate-500">
                                {zone.description}
                            </p>
                            <div className="mt-6 border-t border-slate-100 pt-5">
                                <p className="text-[11px] font-bold uppercase tracking-[0.22em] text-accent">
                                    {zone.feature}
                                </p>
                            </div>
                        </article>
                    ))}
                </div>
            </div>
        </section>
    );
}
