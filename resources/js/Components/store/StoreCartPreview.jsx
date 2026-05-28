import CTAButton from '@/components/common/CTAButton';

export default function StoreCartPreview({ cartItems }) {
    const itemCount = cartItems.reduce((total, item) => total + item.quantity, 0);

    return (
        <aside className="rounded-xl border border-slate-100 bg-white p-6 shadow-card">
            <div className="flex items-center justify-between">
                <div>
                    <p className="text-xs font-semibold uppercase tracking-[0.32em] text-slate-400">
                        Tu carrito
                    </p>
                    <h3 className="mt-2 font-display text-3xl font-bold uppercase text-primary">
                        {itemCount} item{itemCount === 1 ? '' : 's'}
                    </h3>
                </div>
                <span className="material-symbols-outlined rounded-full bg-accent/10 p-3 text-accent">
                    shopping_cart
                </span>
            </div>

            <div className="mt-6 space-y-4">
                {cartItems.length ? (
                    cartItems.map((item) => (
                        <div
                            key={item.id}
                            className="flex items-center justify-between gap-4 border-b border-slate-100 pb-4"
                        >
                            <div>
                                <p className="text-sm font-bold uppercase text-text-main">
                                    {item.name}
                                </p>
                                <p className="text-xs uppercase tracking-[0.2em] text-slate-400">
                                    x{item.quantity}
                                </p>
                            </div>
                            <span className="font-display text-lg font-bold text-primary">
                                {item.priceLabel ?? `$${Number(item.price ?? 0).toFixed(2)}`}
                            </span>
                        </div>
                    ))
                ) : (
                    <p className="text-sm leading-6 text-slate-500">
                        Aun no has agregado productos. Explora la tienda y agrega los tuyos.
                    </p>
                )}
            </div>

            <CTAButton href="/carrito" variant="primary" className="mt-6 w-full">
                IR AL CARRITO
            </CTAButton>
        </aside>
    );
}
