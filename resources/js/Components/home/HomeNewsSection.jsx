export default function HomeNewsSection({ articles, loading = false }) {
    const [main, ...rest] = articles;
    const side = rest.slice(0, 2);

    return (
        <section className="lg:col-span-8">
            <div className="mb-8 flex items-end justify-between border-b border-gray-200 pb-6">
                <h2 className="font-display text-2xl font-bold uppercase tracking-tight text-primary">
                    CENTRO DE NOTICIAS
                </h2>
                <a
                    href="/noticias"
                    className="flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-accent transition-colors hover:text-primary"
                >
                    VER TODAS
                    <span className="material-symbols-outlined text-sm">open_in_new</span>
                </a>
            </div>

            {loading && (
                <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
                    <div className="h-72 animate-pulse rounded-xl bg-gray-100 lg:col-span-2" />
                    <div className="flex flex-col gap-4">
                        <div className="h-[136px] animate-pulse rounded-lg bg-gray-100" />
                        <div className="h-[136px] animate-pulse rounded-lg bg-gray-100" />
                    </div>
                </div>
            )}

            {!loading && articles.length === 0 && (
                <p className="text-sm text-gray-400">No hay noticias publicadas aún.</p>
            )}

            {!loading && articles.length > 0 && (
                <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
                    {/* artículo principal — ocupa 2/3 */}
                    {main && (
                        <a
                            href={main.href}
                            className="group relative overflow-hidden rounded-xl shadow-md lg:col-span-2"
                        >
                            <div className="aspect-[4/3] w-full lg:aspect-[16/11]">
                                <img
                                    src={main.imageUrl}
                                    alt={main.title}
                                    className="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                                />
                            </div>
                            <div className="absolute inset-0 bg-gradient-to-t from-primary/90 via-primary/30 to-transparent" />
                            <div className="absolute inset-x-0 bottom-0 p-5 md:p-7">
                                <span className="mb-2 inline-block rounded-full bg-accent px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-widest text-white">
                                    {main.categoryLabel}
                                </span>
                                <h3 className="font-display text-lg font-bold uppercase leading-tight text-white md:text-2xl">
                                    {main.title}
                                </h3>
                                <p className="mt-2 hidden text-xs leading-relaxed text-white/75 md:line-clamp-2">
                                    {main.summary}
                                </p>
                            </div>
                        </a>
                    )}

                    {/* dos artículos secundarios — 1/3, grid de 2 filas iguales */}
                    <div className="grid grid-rows-2 gap-4">
                        {side.map((article) => (
                            <a
                                key={article.slug}
                                href={article.href}
                                className="group flex flex-col overflow-hidden rounded-lg border border-gray-100 bg-white shadow-sm transition-shadow hover:shadow-md"
                            >
                                <div className="h-32 w-full overflow-hidden shrink-0">
                                    <img
                                        src={article.imageUrl}
                                        alt={article.title}
                                        className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    />
                                </div>
                                <div className="flex flex-1 flex-col justify-between p-4">
                                    <div>
                                        <span className="text-[9px] font-bold uppercase tracking-widest text-accent">
                                            {article.categoryLabel}
                                        </span>
                                        <h4 className="mt-1 font-display text-sm font-bold uppercase leading-snug text-primary transition-colors group-hover:text-accent line-clamp-3">
                                            {article.title}
                                        </h4>
                                    </div>
                                    <span className="mt-3 text-[10px] font-bold uppercase tracking-wider text-accent">
                                        Leer más →
                                    </span>
                                </div>
                            </a>
                        ))}
                    </div>
                </div>
            )}
        </section>
    );
}
