export default function ExecutiveStaffGrid({ executives }) {
    return (
        <section className="section-space bg-surface">
            <div className="page-shell max-w-7xl">
                <div className="mb-16">
                    <p className="text-lg font-bold uppercase tracking-[0.3em] text-accent">Staff Ejecutivo</p>
                    <h2 className="mt-3 font-display text-4xl font-bold uppercase text-primary">
                        Cuerpo Directivo
                    </h2>
                    <div className="mt-4 h-1.5 w-24 bg-accent" />
                </div>

                <div className="grid grid-cols-1 gap-10 md:grid-cols-3">
                    {executives.map((executive) => (
                        <article
                            key={executive.id}
                            className="group overflow-hidden rounded-[24px] bg-white shadow-md transition-all hover:-translate-y-1 hover:shadow-xl"
                        >
                            <div className="relative aspect-[4/5] overflow-hidden">
                                <img
                                    src={executive.imageUrl}
                                    alt={executive.name}
                                    className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                />
                                <div
                                    className={`absolute bottom-0 left-0 px-4 py-2 text-[10px] font-bold uppercase tracking-[0.25em] text-white ${
                                        executive.tone === 'accent' ? 'bg-accent' : 'bg-primary'
                                    }`}
                                >
                                    {executive.role}
                                </div>
                            </div>
                            <div className="p-8">
                                <h3 className="font-display text-2xl font-bold uppercase text-primary">
                                    {executive.name}
                                </h3>
                                <p className="mt-1 text-xs font-bold uppercase tracking-[0.22em] text-accent">
                                    {executive.area}
                                </p>
                                <p className="mt-5 text-sm leading-relaxed text-gray-600">
                                    {executive.description}
                                </p>
                                <div className="mt-6 flex gap-4 border-t border-gray-100 pt-6 text-gray-400">
                                    {executive.icons.map((icon) => (
                                        <span key={icon} className="material-symbols-outlined text-lg">
                                            {icon}
                                        </span>
                                    ))}
                                </div>
                            </div>
                        </article>
                    ))}
                </div>
            </div>
        </section>
    );
}
