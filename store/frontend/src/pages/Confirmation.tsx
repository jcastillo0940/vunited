import { Container, Card, Icon, Button, Badge } from '@veraguas/ui';

export function Confirmation() {
    const orderNumber = 'ST-2026-000123';
    return (
        <Container className="section-space max-w-xl text-center">
            <Icon name="check_circle" size="lg" className="mx-auto text-emerald-500" />
            <h1 className="section-heading mt-4">¡Orden confirmada!</h1>
            <p className="mt-2 text-text-main/70">Te enviamos el comprobante por correo.</p>
            <Card className="mt-8 flex items-center justify-between">
                <span className="text-sm text-text-main/60">Número de orden</span>
                <Badge tone="success">{orderNumber}</Badge>
            </Card>
            <Button as="a" href="/" className="mt-8">
                Volver a la tienda
            </Button>
        </Container>
    );
}
