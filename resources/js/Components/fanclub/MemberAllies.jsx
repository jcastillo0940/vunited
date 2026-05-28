export default function MemberAllies({ allies }) {
    return (
        <section className="section-space bg-background">
            <div className="page-shell max-w-7xl">
                <div className="mb-14 flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                    <div>
                        <span className="mb-2 block text-sm font-bold uppercase tracking-[0.3em] text-accent">
                            Descuentos
                        </span>
                        <h2 className="font-display text-4xl font-black uppercase text-primary md:text-5xl">
                            ALIADOS DE LA TRIBU
                        </h2>
                    </div>
                    <p className="max-w-xl text-sm leading-relaxed text-gray-600">
                        Beneficios especiales en comercios seleccionados para socios del club.
                    </p>
                </div>

                <div className="grid grid-cols-2 gap-6 md:grid-cols-3 lg:grid-cols-6">
                    {allies.map((ally) => (
                        <article
                            key={ally.id}
                            className="flex min-h-36 flex-col items-center justify-center rounded-xl border border-gray-200 bg-surface p-6 text-center shadow-sm transition-all hover:bg-white hover:shadow-md"
                        >
                            <div className="flex h-16 w-16 items-center justify-center rounded-full bg-white text-lg font-black uppercase text-primary shadow-sm">
                                {ally.shortLabel}
                            </div>
                            <p className="mt-4 text-xs font-bold uppercase tracking-[0.18em] text-gray-600">
                                {ally.name}
                            </p>
                        </article>
                    ))}
                </div>
            </div>
        </section>
    );
}
