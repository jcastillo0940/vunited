export default function MatchdayExperience({ items }) {
    return (
        <section className="section-space bg-background">
            <div className="page-shell max-w-7xl">
                <div className="mb-12">
                    <p className="text-sm font-bold uppercase tracking-[0.3em] text-accent">
                        Experiencia matchday
                    </p>
                    <h2 className="mt-2 font-display text-5xl font-black uppercase text-primary">
                        Vive la jornada completa
                    </h2>
                </div>

                <div className="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-5">
                    {items.map((item) => (
                        <article key={item.id} className="rounded-xl border border-slate-200 bg-white p-8 text-center shadow-md">
                            <div className="mx-auto mb-6 grid h-16 w-16 place-items-center rounded-full bg-accent text-white shadow-md">
                                <span className="material-symbols-outlined text-3xl">{item.icon}</span>
                            </div>
                            <h3 className="font-display text-2xl font-black uppercase text-primary">
                                {item.title}
                            </h3>
                            <p className="mt-4 text-sm leading-6 text-slate-500">{item.description}</p>
                        </article>
                    ))}
                </div>
            </div>
        </section>
    );
}
