export default function WelcomeKit({ items }) {
    return (
        <section className="section-space bg-surface">
            <div className="page-shell max-w-7xl">
                <div className="mb-16 text-center">
                    <h2 className="font-display text-5xl font-black uppercase tracking-tight text-primary md:text-6xl">
                        KIT DE BIENVENIDA
                    </h2>
                    <p className="mt-4 text-sm font-bold uppercase tracking-[0.3em] text-accent">
                        RECIBE TU KIT DE BIENVENIDA AL UNIRTE
                    </p>
                </div>

                <div className="grid grid-cols-1 gap-10 md:grid-cols-3">
                    {items.map((item) => (
                        <article
                            key={item.id}
                            className="group flex flex-col items-center rounded-xl border border-gray-200 bg-white p-10 text-center transition-all hover:shadow-xl"
                        >
                            <div className="mb-8 transform transition-transform duration-500 group-hover:scale-110">
                                <img src={item.imageUrl} alt={item.title} className="h-64 w-48 object-contain" />
                            </div>
                            <h3 className="font-display text-2xl font-bold uppercase text-primary">
                                {item.title}
                            </h3>
                            <p className="mt-3 text-sm text-gray-500">{item.description}</p>
                        </article>
                    ))}
                </div>
            </div>
        </section>
    );
}
