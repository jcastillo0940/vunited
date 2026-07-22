import { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { Container, Card, Button, ErrorState, LoadingState } from '@veraguas/ui';
import { apiFetch, ApiError } from '../api/client';
import { addToCart } from '../api/cart';

interface ProductDetail {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    price: number;
    display_price: number;
    inventory: { available: number; reserved: number } | null;
}

export function Product() {
    const { slug } = useParams<{ slug: string }>();
    const navigate = useNavigate();
    const [product, setProduct] = useState<ProductDetail | null>(null);
    const [error, setError] = useState<string | null>(null);
    const [adding, setAdding] = useState(false);

    useEffect(() => {
        if (!slug) return;
        apiFetch<ProductDetail>(`/products/${slug}`)
            .then(setProduct)
            .catch(() => setError('No se pudo cargar el producto.'));
    }, [slug]);

    async function handleAddToCart() {
        if (!product) return;
        setAdding(true);
        setError(null);
        try {
            await addToCart(product.id, 1);
            navigate('/carrito');
        } catch (err) {
            setError(err instanceof ApiError ? err.message : 'No se pudo agregar al carrito.');
        } finally {
            setAdding(false);
        }
    }

    if (error && !product) return <ErrorState message={error} />;
    if (!product) return <LoadingState label="Cargando producto…" />;

    const available = product.inventory ? product.inventory.available - product.inventory.reserved : 0;

    return (
        <Container className="section-space">
            <div className="grid gap-10 md:grid-cols-2">
                <Card className="flex aspect-square items-center justify-center bg-surface text-text-main/40">
                    Imagen del producto
                </Card>
                <div>
                    <p className="display-kicker mb-2">Producto</p>
                    <h1 className="section-heading capitalize">{product.name}</h1>
                    <p className="mt-4 text-2xl font-semibold text-primary">₡{(product.display_price / 100).toFixed(2)}</p>
                    {product.description ? <p className="mt-4 text-text-main/70">{product.description}</p> : null}
                    {error ? <ErrorState message={error} /> : null}
                    <Button
                        className="mt-8"
                        size="lg"
                        pending={adding}
                        pendingLabel="Agregando…"
                        disabled={available <= 0}
                        onClick={handleAddToCart}
                    >
                        {available > 0 ? 'Agregar al carrito' : 'Sin stock'}
                    </Button>
                </div>
            </div>
        </Container>
    );
}
