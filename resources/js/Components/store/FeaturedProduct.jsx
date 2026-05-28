export default function FeaturedProduct({ product, onAddToCart }) {
    return (
        <section className="grid grid-cols-1 gap-gutter md:grid-cols-2">
            <article className="group relative h-[520px] overflow-hidden rounded-xl border border-slate-100 bg-surface shadow-panel">
                <img
                    src={product.imageUrl}
                    alt={product.name}
                    className="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-primary/85 via-primary/25 to-transparent" />
                <div className="absolute bottom-0 left-0 w-full p-8">
                    <span className="inline-flex rounded-sm bg-accent px-3 py-1 text-[10px] font-bold uppercase tracking-[0.32em] text-white">
                        {product.badge}
                    </span>
                    <h2 className="mt-4 font-display text-4xl font-bold uppercase text-white md:text-5xl">
                        {product.name}
                    </h2>
                    <p className="mt-2 text-base italic text-white/80">{product.subtitle}</p>
                    <div className="mt-6 flex items-center justify-between">
                        <span className="font-display text-3xl font-bold text-white">
                            {product.price}
                        </span>
                        <button
                            type="button"
                            onClick={() => onAddToCart(product)}
                            className="rounded-md bg-accent p-4 text-white shadow-lg transition-all hover:bg-white hover:text-accent"
                            aria-label={`Agregar ${product.name} al carrito`}
                        >
                            <span className="material-symbols-outlined">add_shopping_cart</span>
                        </button>
                    </div>
                </div>
            </article>

            <article className="group relative h-[520px] overflow-hidden rounded-xl border border-slate-100 bg-surface shadow-panel">
                <img
                    src={product.imageUrl}
                    alt={`${product.name} detalle`}
                    className="absolute inset-0 h-full w-full object-cover object-top opacity-90 transition-transform duration-700 group-hover:scale-105"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-primary/80 via-transparent to-transparent" />
                <div className="absolute bottom-0 left-0 w-full p-8">
                    <span className="inline-flex rounded-sm border border-white/30 bg-primary px-3 py-1 text-[10px] font-bold uppercase tracking-[0.32em] text-white">
                        {product.category}
                    </span>
                    <h3 className="mt-4 font-display text-4xl font-bold uppercase text-white md:text-5xl">
                        JERSEY LOCAL
                    </h3>
                    <p className="mt-2 text-base italic text-white/80">
                        Listo para tribuna, cancha y coleccion.
                    </p>
                    <div className="mt-6 flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <span className="font-display text-3xl font-bold text-white">
                                {product.salePrice ?? product.price}
                            </span>
                            {product.compareAtPrice ? (
                                <span className="text-sm text-white/60 line-through">
                                    {product.compareAtPrice}
                                </span>
                            ) : null}
                        </div>
                        <button
                            type="button"
                            onClick={() => onAddToCart(product)}
                            className="rounded-md bg-accent p-4 text-white shadow-lg transition-all hover:bg-white hover:text-accent"
                            aria-label={`Agregar ${product.name} al carrito`}
                        >
                            <span className="material-symbols-outlined">add_shopping_cart</span>
                        </button>
                    </div>
                </div>
            </article>
        </section>
    );
}
