export default function MembershipBenefits({ benefits }) {
    return (
        <section id="beneficios" className="section-space bg-background">
            <div className="page-shell max-w-7xl">
                <div className="mb-16 flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                    <div>
                        <span className="mb-2 block text-sm font-bold uppercase tracking-[0.4em] text-accent">
                            Ventajas
                        </span>
                        <h2 className="font-display text-5xl font-black uppercase tracking-tight text-primary">
                            BENEFICIOS DE MIEMBRO
                        </h2>
                    </div>
                    <div className="h-1 w-full rounded-full bg-gray-100 md:w-1/3">
                        <div className="h-1 w-24 rounded-full bg-accent" />
                    </div>
                </div>

                <div className="grid grid-cols-1 gap-10 md:grid-cols-2">
                    {benefits.map((benefit) => (
                        <article
                            key={benefit.id}
                            className="group flex gap-8 rounded-xl border border-gray-100 bg-surface p-10 transition-all hover:bg-white hover:shadow-xl"
                        >
                            <div className="flex h-20 w-20 flex-shrink-0 items-center justify-center rounded-lg bg-accent shadow-lg">
                                <span className="material-symbols-outlined text-4xl text-white">
                                    {benefit.icon}
                                </span>
                            </div>
                            <div>
                                <h3 className="mb-3 font-display text-2xl font-bold uppercase text-primary">
                                    {benefit.title}
                                </h3>
                                <p className="leading-relaxed text-gray-600">{benefit.description}</p>
                            </div>
                        </article>
                    ))}
                </div>
            </div>
        </section>
    );
}
