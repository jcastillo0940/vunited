export default function SponsorValueSection({ valueProps, imageUrl }) {
    return (
        <section className="section-space bg-surface">
            <div className="page-shell max-w-7xl">
                <div className="grid items-center gap-14 lg:grid-cols-[0.95fr_1.05fr]">
                    <div className="space-y-8">
                        <div className="space-y-4">
                            <p className="display-kicker">Propuesta de valor</p>
                            <h2 className="font-display text-4xl font-bold uppercase tracking-tight text-primary md:text-5xl">
                                ¿POR QUE UNIRSE AL UNITED?
                            </h2>
                        </div>

                        <div className="space-y-5">
                            {valueProps.map((item) => (
                                <article
                                    key={item.id}
                                    className="surface-card rounded-[24px] border border-primary/10 p-6"
                                >
                                    <div className="flex items-start gap-4">
                                        <div className="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-primary text-white shadow-lg shadow-primary/20">
                                            <span className="material-symbols-outlined text-2xl">
                                                {item.icon}
                                            </span>
                                        </div>
                                        <div className="space-y-2">
                                            <h3 className="font-display text-2xl font-bold uppercase leading-tight text-primary">
                                                {item.title}
                                            </h3>
                                            <p className="text-sm leading-relaxed text-gray-600">
                                                {item.description}
                                            </p>
                                        </div>
                                    </div>
                                </article>
                            ))}
                        </div>
                    </div>

                    <div className="relative min-h-[420px] overflow-hidden rounded-[32px] border border-primary/10 bg-white shadow-panel">
                        <div className="absolute -left-6 bottom-6 top-6 w-24 rounded-[32px] bg-accent/80" />
                        <img
                            src={imageUrl}
                            alt="Aliados del Indio"
                            className="absolute inset-y-4 right-4 h-[calc(100%-2rem)] w-[calc(100%-2rem)] rounded-[28px] object-cover"
                        />
                        <div className="absolute inset-0 bg-gradient-to-tr from-primary/70 via-primary/15 to-transparent" />
                        <div className="absolute bottom-0 left-0 right-0 p-8">
                            <div className="rounded-[24px] border border-white/20 bg-white/10 p-6 backdrop-blur-sm">
                                <p className="text-[10px] font-bold uppercase tracking-[0.35em] text-accent">
                                    Impacto de marca
                                </p>
                                <p className="mt-3 max-w-sm text-sm leading-relaxed text-white/85">
                                    Presencia premium en contenidos, experiencias y comunidad para crecer junto al club.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
