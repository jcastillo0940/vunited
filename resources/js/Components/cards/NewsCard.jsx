export default function NewsCard({ article, variant = 'compact' }) {
    if (variant === 'featured') {
        return (
            <a
                href={article.href}
                className="group relative block w-full overflow-hidden rounded-xl shadow-lg"
            >
                {/* imagen siempre recortada al mismo tamaño */}
                <div className="aspect-[21/9] w-full">
                    <img
                        src={article.imageUrl}
                        alt={article.title}
                        className="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                    />
                </div>

                {/* overlay degradado */}
                <div className="absolute inset-0 bg-gradient-to-t from-primary/90 via-primary/40 to-transparent" />

                {/* contenido sobre la imagen */}
                <div className="absolute inset-x-0 bottom-0 p-6 md:p-10">
                    <span className="mb-3 inline-block rounded-full bg-accent px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-white">
                        {article.categoryLabel}
                    </span>
                    <h2 className="font-display text-2xl font-bold uppercase leading-tight text-white md:text-4xl">
                        {article.title}
                    </h2>
                    {article.summary ? (
                        <p className="mt-3 hidden max-w-2xl text-sm leading-relaxed text-white/80 md:block">
                            {article.summary}
                        </p>
                    ) : null}
                    <span className="mt-4 inline-block text-xs font-bold uppercase tracking-wider text-accent">
                        Leer artículo completo →
                    </span>
                </div>
            </a>
        );
    }

    return (
        <a
            href={article.href}
            className="group flex flex-col overflow-hidden rounded-lg border border-gray-100 bg-white shadow-md transition-shadow hover:shadow-xl"
        >
            {/* imagen siempre en la misma proporción */}
            <div className="aspect-[4/3] w-full overflow-hidden">
                <img
                    src={article.imageUrl}
                    alt={article.title}
                    className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                />
            </div>
            <div className="flex flex-1 flex-col p-6">
                <span className="text-[10px] font-bold uppercase tracking-widest text-accent">
                    {article.categoryLabel}
                </span>
                <h3 className="mt-2 font-display text-lg font-bold uppercase leading-tight text-primary transition-colors group-hover:text-accent">
                    {article.title}
                </h3>
                {article.summary ? (
                    <p className="mt-3 flex-1 text-sm leading-relaxed text-gray-500">
                        {article.summary}
                    </p>
                ) : null}
                <span className="mt-4 text-xs font-bold uppercase tracking-wider text-accent">
                    Leer más →
                </span>
            </div>
        </a>
    );
}
