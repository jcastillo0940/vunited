import { useParams } from 'react-router-dom';
import { Container, Card, Button, Select } from '@veraguas/ui';

export function Product() {
    const { slug } = useParams<{ slug: string }>();
    return (
        <Container className="section-space">
            <div className="grid gap-10 md:grid-cols-2">
                <Card className="flex aspect-square items-center justify-center bg-surface text-text-main/40">
                    Imagen del producto
                </Card>
                <div>
                    <p className="display-kicker mb-2">Producto</p>
                    <h1 className="section-heading capitalize">{slug?.replace(/-/g, ' ')}</h1>
                    <p className="mt-4 text-2xl font-semibold text-primary">B/. 45.00</p>
                    <label htmlFor="size" className="mt-8 block text-sm font-semibold text-text-main">
                        Talla
                    </label>
                    <Select id="size" className="mt-2 max-w-xs" defaultValue="M">
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                    </Select>
                    <Button className="mt-8" size="lg">
                        Agregar al carrito
                    </Button>
                </div>
            </div>
        </Container>
    );
}
