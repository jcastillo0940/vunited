import NewsCard from '@/components/cards/NewsCard';

export default function HomeNewsSection({ articles }) {
    const mainArticle = articles[0];
    const secondaryArticles = articles.slice(1, 3);

    return (
        <section className="lg:col-span-8">
            <div className="mb-12 flex items-end justify-between border-b border-gray-200 pb-6">
                <h2 className="font-display text-4xl font-bold uppercase tracking-tight text-primary">
                    CENTRO DE NOTICIAS
                </h2>
                <a
                    href="#"
                    className="flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-accent transition-colors hover:text-primary"
                >
                    VER TODAS
                    <span className="material-symbols-outlined text-sm">open_in_new</span>
                </a>
            </div>
            <div className="grid grid-cols-1 gap-10 md:grid-cols-2">
                {mainArticle ? <NewsCard article={mainArticle} variant="featured" /> : null}
                {secondaryArticles.map((article) => (
                    <NewsCard key={article.slug} article={article} />
                ))}
            </div>
        </section>
    );
}
