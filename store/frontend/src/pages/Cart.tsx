import { Container, Table, type Column, Button, EmptyState } from '@veraguas/ui';

interface CartLine {
    id: string;
    product: string;
    size: string;
    quantity: number;
    subtotal: string;
}

const MOCK_CART: CartLine[] = [];

const columns: Column<CartLine>[] = [
    { key: 'product', header: 'Producto', render: (row) => row.product },
    { key: 'size', header: 'Talla', render: (row) => row.size, align: 'center' },
    { key: 'quantity', header: 'Cantidad', render: (row) => row.quantity, align: 'center' },
    { key: 'subtotal', header: 'Subtotal', render: (row) => row.subtotal, align: 'right' },
];

export function Cart() {
    return (
        <Container className="section-space">
            <h1 className="section-heading mb-10">Carrito</h1>
            {MOCK_CART.length === 0 ? (
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
                    <Table columns={columns} rows={MOCK_CART} rowKey={(row) => row.id} />
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
