import { useState, useEffect, useCallback } from 'react';

export default function ShopPreview({ products = [] }) {
    const [current, setCurrent] = useState(0);
    const [paused, setPaused] = useState(false);

    const total = products.length;
    const next = useCallback(() => setCurrent((i) => (i + 1) % total), [total]);
    const prev = () => setCurrent((i) => (i - 1 + total) % total);

    useEffect(() => {
        if (paused || total <= 1) return;
        const t = setInterval(next, 4000);
        return () => clearInterval(t);
    }, [paused, total, next]);

    const product = products[current];

    return (
        <section>
            <div className="mb-8 flex items-center justify-between">
                <h2 className="font-display text-2xl font-bold uppercase tracking-tight text-primary">
                    TIENDA OFICIAL
                </h2>
                <a
                    href="/tienda"
                    className="text-xs font-bold uppercase tracking-wider text-accent transition-colors hover:text-primary"
                >
                    IR A TIENDA →
                </a>
            </div>

            {!product ? (
                <div className="rounded-lg border border-gray-100 bg-surface p-8 text-center">
                    <span className="material-symbols-outlined text-4xl text-gray-300">storefront</span>
                    <p className="mt-3 text-sm text-gray-400">Próximamente productos disponibles</p>
                </div>
            ) : (
                <a
                    href={`/tienda/${product.slug}`}
                    className="group relative block overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition-shadow hover:shadow-md"
                    onMouseEnter={() => setPaused(true)}
                    onMouseLeave={() => setPaused(false)}
                >
                    {/* imagen del producto */}
                    <div className="relative flex aspect-square items-center justify-center overflow-hidden bg-surface">
                        {product.image_url ? (
                            <img
                                src={product.image_url}
                                alt={product.name}
                                className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                            />
                        ) : (
                            <span className="material-symbols-outlined text-6xl text-gray-200">
                                storefront
                            </span>
                        )}

                        {product.badge ? (
                            <span className="absolute left-3 top-3 rounded-full bg-accent px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider text-white">
                                {product.badge}
                            </span>
                        ) : null}

                        {/* overlay añadir al carrito */}
                        <div className="absolute bottom-0 left-0 right-0 translate-y-full bg-primary/90 p-4 transition-transform duration-300 group-hover:translate-y-0">
                            <span className="block w-full text-center text-[10px] font-bold uppercase tracking-wider text-white">
                                Ver producto →
                            </span>
                        </div>

                        {total > 1 && (
                            <>
                                <button
                                    onClick={(e) => { e.preventDefault(); e.stopPropagation(); prev(); }}
                                    className="absolute left-2 top-1/2 -translate-y-1/2 rounded-full bg-white/80 p-1 opacity-0 shadow transition-opacity group-hover:opacity-100 hover:bg-white"
                                    aria-label="Anterior"
                                >
                                    <span className="material-symbols-outlined text-base text-primary">chevron_left</span>
                                </button>
                                <button
                                    onClick={(e) => { e.preventDefault(); e.stopPropagation(); next(); }}
                                    className="absolute right-2 top-1/2 -translate-y-1/2 rounded-full bg-white/80 p-1 opacity-0 shadow transition-opacity group-hover:opacity-100 hover:bg-white"
                                    aria-label="Siguiente"
                                >
                                    <span className="material-symbols-outlined text-base text-primary">chevron_right</span>
                                </button>
                            </>
                        )}
                    </div>

                    {/* info */}
                    <div className="flex items-start justify-between p-5">
                        <div>
                            <h4 className="font-display text-base font-bold uppercase text-primary">
                                {product.name}
                            </h4>
                            <p className="text-xs font-medium text-gray-500">
                                {product.category?.name ?? product.short_description ?? ''}
                            </p>
                        </div>
                        <span className="font-display text-lg font-bold text-primary">
                            ${Number(product.price).toFixed(2)}
                        </span>
                    </div>

                    {total > 1 && (
                        <div className="flex justify-center gap-1.5 pb-4">
                            {products.map((_, i) => (
                                <button
                                    key={i}
                                    onClick={(e) => { e.preventDefault(); setCurrent(i); }}
                                    className={`h-1.5 rounded-full transition-all duration-300 ${
                                        i === current ? 'w-4 bg-primary' : 'w-1.5 bg-gray-300 hover:bg-gray-400'
                                    }`}
                                    aria-label={`Producto ${i + 1}`}
                                />
                            ))}
                        </div>
                    )}
                </a>
            )}
        </section>
    );
}
