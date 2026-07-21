import { Container, Card, Icon, Button, Badge } from '@veraguas/ui';

export function Confirmation() {
    return (
        <Container className="section-space max-w-xl text-center">
            <Icon name="check_circle" size="lg" className="mx-auto text-emerald-500" />
            <h1 className="section-heading mt-4">¡Compra confirmada!</h1>
            <p className="mt-2 text-text-main/70">Tus entradas ya están en tu wallet.</p>
            <Card className="mt-8 flex items-center justify-between">
                <span className="text-sm text-text-main/60">Número de orden</span>
                <Badge tone="success">TK-2026-000456</Badge>
            </Card>
            <Button as="a" href="/wallet" className="mt-8">
                Ver mis entradas
            </Button>
        </Container>
    );
}
