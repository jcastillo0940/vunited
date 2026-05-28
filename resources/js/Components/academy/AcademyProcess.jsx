export default function AcademyProcess({ steps }) {
    return (
        <section className="section-space bg-surface">
            <div className="page-shell max-w-7xl">
                <div className="mb-12 space-y-4">
                    <p className="display-kicker">Metodologia y valores</p>
                    <h2 className="font-display text-4xl font-bold uppercase tracking-tight text-primary md:text-5xl">
                        FORMACION CON RUTA CLARA
                    </h2>
                </div>
                <div className="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                    {steps.map((step) => (
                        <article
                            key={step.id}
                            className="rounded-2xl border border-outline bg-white p-8 shadow-sm"
                        >
                            <div className="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary text-white">
                                <span className="material-symbols-outlined">{step.icon}</span>
                            </div>
                            <h3 className="mt-6 font-display text-2xl font-bold uppercase text-primary">
                                {step.title}
                            </h3>
                            <p className="mt-4 text-sm leading-relaxed text-gray-600">
                                {step.description}
                            </p>
                        </article>
                    ))}
                </div>
            </div>
        </section>
    );
}
