import { useEffect, useState } from 'react';
import { Container, Table, type Column, Button, EmptyState, LoadingState } from '@veraguas/ui';
import { fetchCart, type CartItemView } from '../api/cart';

const columns: Column<CartItemView>[] = [
    { key: 'product', header: 'Producto', render: (row) => row.product.name },
    { key: 'quantity', header: 'Cantidad', render: (row) => row.quantity, align: 'center' },
    { key: 'subtotal', header: 'Subtotal', render: (row) => `₡${((row.unit_price * row.quantity) / 100).toFixed(2)}`, align: 'right' },
];

export function Cart() {
    const [items, setItems] = useState<CartItemView[] | null>(null);

    useEffect(() => {
        fetchCart().then((cart) => setItems(cart.items));
    }, []);

    if (items === null) return <LoadingState label="Cargando carrito…" />;

    return (
        <Container className="section-space">
            <h1 className="section-heading mb-10">Carrito</h1>
            {items.length === 0 ? (
                <EmptyState
                    icon="shopping_cart"
                    title="Tu carrito está vacío"
                    message="Agrega productos desde el catálogo para continuar."
                    action={
                        <Button as="a" href="/">
                            Ir al catálogo
                        </Button>
                    }
                />
            ) : (
                <>
                    <Table columns={columns} rows={items} rowKey={(row) => String(row.id)} />
                    <div className="mt-8 flex justify-end">
                        <Button as="a" href="/checkout" size="lg">
                            Continuar a checkout
                        </Button>
                    </div>
                </>
            )}
        </Container>
    );
}
