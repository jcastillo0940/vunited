import { Link } from '@inertiajs/react';
import CartItem from '@/components/cart/CartItem';

export default function CartItemList({ items, onIncrease, onDecrease, onRemove }) {
    return (
        <div className="space-y-6">
            {items.map((item) => (
                <CartItem
                    key={item.id}
                    item={item}
                    onIncrease={onIncrease}
                    onDecrease={onDecrease}
                    onRemove={onRemove}
                />
            ))}

            <Link
                href="/tienda"
                className="inline-flex items-center gap-2 text-sm font-bold uppercase text-accent transition hover:text-primary"
            >
                <span className="material-symbols-outlined transition group-hover:-translate-x-1">
                    arrow_back
                </span>
                Seguir comprando
            </Link>
        </div>
    );
}
