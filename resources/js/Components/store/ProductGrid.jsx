export default function ProductGrid({ products, onAddToCart }) {
    return (
        <div className="grid grid-cols-1 gap-gutter sm:grid-cols-2 xl:grid-cols-4">
            {products.map((product) => (
                <article
                    key={product.id}
                    className="group rounded-xl border border-slate-100 bg-white p-4 shadow-card transition hover:-translate-y-1 hover:shadow-panel"
                >
                    <div className="relative mb-4 aspect-square overflow-hidden rounded-lg bg-surface">
                        <img
                            src={product.imageUrl}
                            alt={product.name}
                            className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                        <div className="absolute left-3 top-3 flex items-center gap-2">
                            <span className="rounded-sm bg-accent px-2 py-1 text-[10px] font-bold uppercase tracking-[0.24em] text-white">
                                {product.badge}
                            </span>
                        </div>
                    </div>

                    <p className="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">
                        {product.category}
                    </p>
                    <h3 className="mt-2 font-body text-sm font-bold uppercase text-text-main">
                        {product.name}
                    </h3>
                    <p className="mt-1 min-h-[40px] text-sm text-slate-500">{product.subtitle}</p>

                    <div className="mt-4 flex items-end justify-between gap-4">
                        <div>
                            {product.compareAtPrice ? (
                                <div className="flex items-center gap-2">
                                    <span className="font-display text-xl font-bold text-primary">
                                        {product.salePrice ?? product.price}
                                    </span>
                                    <span className="text-sm text-slate-400 line-through">
                                        {product.compareAtPrice}
                                    </span>
                                </div>
                            ) : (
                                <span className="font-display text-xl font-bold text-primary">
                                    {product.price}
                                </span>
                            )}
                        </div>
                        <button
                            type="button"
                            onClick={() => onAddToCart(product)}
                            disabled={product.outOfStock}
                            className="rounded-md border-2 border-accent px-4 py-2 text-xs font-bold uppercase tracking-[0.24em] text-accent transition hover:bg-accent hover:text-white"
                        >
                            {product.outOfStock ? 'Agotado' : 'Anadir'}
                        </button>
                    </div>
                </article>
            ))}
        </div>
    );
}
