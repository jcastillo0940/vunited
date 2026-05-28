import { Link } from '@inertiajs/react';
import EmptyState from '@/components/common/EmptyState';

export default function CartEmptyState() {
    return (
        <div className="rounded-xl border border-slate-100 bg-white p-8 shadow-card">
            <EmptyState
                title="Tu carrito esta vacio"
                description="Todavia no has agregado productos. Explora la tienda oficial y arma tu pedido visual."
            />

            <div className="mt-6 text-center">
                <Link
                    href="/tienda"
                    className="inline-flex items-center justify-center rounded-md bg-accent px-6 py-3 text-sm font-bold uppercase tracking-[0.18em] text-white transition hover:bg-primary"
                >
                    Volver a tienda
                </Link>
            </div>
        </div>
    );
}
