export default function NewsCard({ article, variant = 'compact' }) {
    if (variant === 'featured') {
        return (
            <article className="group flex flex-col overflow-hidden rounded-lg border border-gray-100 bg-white shadow-md md:col-span-2 md:flex-row">
                <div className="overflow-hidden md:w-3/5">
                    <img
                        src={article.imageUrl}
                        alt={article.title}
                        className="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                    />
                </div>
                <div className="flex flex-1 flex-col justify-between p-10">
                    <div>
                        <span className="mb-6 inline-block rounded-full bg-surface px-3 py-1 text-[10px] font-bold uppercase text-primary">
                            {article.categoryLabel}
                        </span>
                        <h3 className="font-display text-3xl font-bold uppercase leading-tight text-primary transition-colors group-hover:text-accent">
                            {article.title}
                        </h3>
                        <p className="mt-6 font-body leading-relaxed text-gray-600">
                            {article.summary}
                        </p>
                    </div>
                    <a
                        href={article.href}
                        className="pt-6 text-sm font-bold uppercase tracking-wider text-accent transition-colors hover:text-primary"
                    >
                        Leer articulo completo
                    </a>
                </div>
            </article>
        );
    }

    return (
        <article className="group overflow-hidden rounded-lg border border-gray-100 bg-white shadow-md">
            <div className="aspect-video overflow-hidden">
                <img
                    src={article.imageUrl}
                    alt={article.title}
                    className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                />
            </div>
            <div className="p-8">
                <span className="block text-[10px] font-bold uppercase text-accent">
                    {article.categoryLabel}
                </span>
                <h3 className="mt-3 font-display text-xl font-bold uppercase text-primary transition-colors group-hover:text-accent">
                    {article.title}
                </h3>
                <p className="mt-4 text-sm leading-relaxed text-gray-600">
                    {article.summary}
                </p>
            </div>
        </article>
    );
}
