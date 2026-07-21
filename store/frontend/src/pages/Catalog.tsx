import { Link } from 'react-router-dom';
import { Container, Grid, Card, Badge } from '@veraguas/ui';

const MOCK_PRODUCTS = [
    { slug: 'camiseta-local-2026', name: 'Camiseta local 2026', price: 'B/. 45.00', category: 'Camisetas' },
    { slug: 'camiseta-visita-2026', name: 'Camiseta visita 2026', price: 'B/. 45.00', category: 'Camisetas' },
    { slug: 'gorra-oficial', name: 'Gorra oficial', price: 'B/. 18.00', category: 'Accesorios' },
];

export function Catalog() {
    return (
        <Container className="section-space">
            <p className="display-kicker mb-2">Tienda oficial</p>
            <h1 className="section-heading mb-10">Catálogo</h1>
            <Grid cols={3}>
                {MOCK_PRODUCTS.map((product) => (
                    <Link key={product.slug} to={`/producto/${product.slug}`}>
                        <Card className="flex h-full flex-col gap-3">
                            <Badge tone="accent" className="w-fit">
                                {product.category}
                            </Badge>
                            <h2 className="font-display text-lg font-bold uppercase text-primary">{product.name}</h2>
                            <p className="mt-auto text-lg font-semibold text-text-main">{product.price}</p>
                        </Card>
                    </Link>
                ))}
            </Grid>
        </Container>
    );
}
