export default function ShopPreview({ products }) {
    const featured = products.find((product) => product.type === 'featured');
    const compact = products.find((product) => product.type === 'compact');

    return (
        <section>
            <div className="mb-8 flex items-center justify-between">
                <h2 className="font-display text-2xl font-bold uppercase tracking-tight text-primary">
                    TIENDA OFICIAL
                </h2>
                <span className="cursor-pointer text-xs font-bold uppercase tracking-wider text-accent transition-colors hover:text-primary">
                    IR A TIENDA
                </span>
            </div>
            <div className="space-y-6">
                {featured ? (
                    <div className="group cursor-pointer overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition-shadow hover:shadow-md">
                        <div className="relative flex aspect-square items-center justify-center bg-surface">
                            <span className="material-symbols-outlined text-6xl text-gray-200">
                                {featured.icon}
                            </span>
                            <div className="absolute bottom-0 left-0 right-0 translate-y-full bg-primary/90 p-4 transition-transform group-hover:translate-y-0">
                                <button className="w-full rounded-md bg-accent py-2 text-[10px] font-bold uppercase tracking-wider text-white">
                                    Anadir al Carrito
                                </button>
                            </div>
                        </div>
                        <div className="flex items-start justify-between p-6">
                            <div>
                                <h4 className="font-display text-lg font-bold uppercase text-primary">
                                    {featured.title}
                                </h4>
                                <p className="text-xs font-medium text-gray-500">{featured.subtitle}</p>
                            </div>
                            <span className="font-display text-xl font-bold text-primary">{featured.price}</span>
                        </div>
                    </div>
                ) : null}

                {compact ? (
                    <div className="flex items-center justify-between rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition-colors hover:bg-surface">
                        <div className="flex items-center gap-4">
                            <div className="flex h-12 w-12 items-center justify-center rounded-md bg-surface">
                                <span className="material-symbols-outlined text-gray-400">{compact.icon}</span>
                            </div>
                            <div>
                                <h4 className="text-sm font-bold uppercase text-primary">{compact.title}</h4>
                                <p className="text-[10px] font-medium text-gray-500">{compact.subtitle}</p>
                            </div>
                        </div>
                        <span className="font-display text-lg font-bold text-primary">{compact.price}</span>
                    </div>
                ) : null}
            </div>
        </section>
    );
}
