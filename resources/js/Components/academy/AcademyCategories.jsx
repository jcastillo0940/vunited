import { Link } from '@inertiajs/react';

export default function AcademyCategories({
    categories,
    activeCategoryId,
    onCategoryChange,
}) {
    const activeCategory =
        categories.find((category) => category.id === activeCategoryId) ?? categories[0];

    return (
        <section className="section-space pt-10">
            <div className="page-shell max-w-7xl">
                <div className="mb-16 flex flex-col items-end justify-between gap-8 md:flex-row">
                    <div>
                        <h2 className="mb-2 font-display text-xl font-bold uppercase tracking-widest text-accent">
                            Descubre el Talento
                        </h2>
                        <h3 className="font-display text-5xl font-bold uppercase text-primary">
                            Nuestras Categorias
                        </h3>
                    </div>
                    <div className="flex flex-wrap gap-3">
                        {categories.map((category) => {
                            const active = category.id === activeCategoryId;

                            return (
                                <button
                                    key={category.id}
                                    type="button"
                                    onClick={() => onCategoryChange(category.id)}
                                    className={[
                                        'rounded-md px-6 py-3 text-xs font-bold uppercase tracking-wide transition-colors',
                                        active
                                            ? 'bg-accent text-white shadow-md'
                                            : 'border border-outline bg-surface text-text-main/60 hover:bg-primary hover:text-white',
                                    ].join(' ')}
                                >
                                    {category.label}
                                </button>
                            );
                        })}
                    </div>
                </div>

                <div className="mb-10 grid gap-6 md:grid-cols-3">
                    {categories.map((category) => (
                        <article
                            key={category.id}
                            className={[
                                'rounded-2xl border p-6 shadow-sm transition-all',
                                category.id === activeCategoryId
                                    ? 'border-accent bg-white shadow-panel'
                                    : 'border-outline bg-surface',
                            ].join(' ')}
                        >
                            <div className="flex items-center justify-between">
                                <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-primary text-white">
                                    <span className="material-symbols-outlined">{category.icon}</span>
                                </div>
                                <span className="font-display text-3xl font-bold text-primary">
                                    {category.statValue}
                                </span>
                            </div>
                            <h4 className="mt-6 font-display text-2xl font-bold uppercase text-primary">
                                {category.name}
                            </h4>
                            <p className="mt-1 text-xs font-bold uppercase tracking-[0.22em] text-accent">
                                {category.ageRange}
                            </p>
                            <p className="mt-4 text-sm leading-relaxed text-gray-600">
                                {category.description}
                            </p>
                        </article>
                    ))}
                </div>

                {activeCategory?.players?.length ? (
                    <div className="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                        {activeCategory.players.map((player) => (
                            <article
                                key={`${activeCategory.id}-${player.name}`}
                                className="group flex flex-col overflow-hidden rounded-xl border border-outline bg-white shadow-md"
                            >
                                <div className="relative aspect-[3/4] overflow-hidden">
                                    <img
                                        alt={player.name}
                                        className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                        src={player.imageUrl}
                                    />
                                    <div className="absolute left-4 top-4 rounded bg-primary px-3 py-1 font-display text-xl text-white shadow-sm">
                                        {player.number}
                                    </div>
                                </div>
                                <div className="p-6">
                                    <h4 className="font-display text-2xl font-bold uppercase tracking-tighter text-primary">
                                        {player.name}
                                    </h4>
                                    <p className="mb-6 mt-1 text-xs font-bold uppercase tracking-widest text-accent">
                                        {player.position}
                                    </p>
                                    <div className="mb-6 grid grid-cols-3 gap-2 border-t border-outline pt-4">
                                        {player.stats.map((stat, index) => (
                                            <div
                                                key={stat.label}
                                                className={[
                                                    'text-center',
                                                    index === 1 ? 'border-x border-outline' : '',
                                                ].join(' ')}
                                            >
                                                <p className="text-[10px] font-bold uppercase text-text-main/40">
                                                    {stat.label}
                                                </p>
                                                <p className="font-bold text-primary">{stat.value}</p>
                                            </div>
                                        ))}
                                    </div>
                                    <Link
                                        href={`/jugadores/${player.slug}`}
                                        className="block w-full rounded-md border border-primary bg-surface py-2 text-center text-xs font-bold uppercase text-primary transition-colors hover:bg-primary hover:text-white"
                                    >
                                        Ver perfil completo
                                    </Link>
                                </div>
                            </article>
                        ))}
                    </div>
                ) : null}
            </div>
        </section>
    );
}
