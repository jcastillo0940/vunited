import { Container, Button, Icon } from '@veraguas/ui';

export function PaymentError() {
    return (
        <Container className="flex min-h-[60vh] flex-col items-center justify-center text-center">
            <Icon name="error" size="lg" className="text-red-500" />
            <p className="display-kicker mt-4">Pago</p>
            <h1 className="section-heading mt-2">No pudimos procesar tu pago</h1>
            <p className="mt-4 max-w-md text-text-main/70">
                El pago fue rechazado o cancelado. No se realizó ningún cargo. Puedes intentar de nuevo.
            </p>
            <Button as="a" href="/checkout" className="mt-8">
                Reintentar pago
            </Button>
        </Container>
    );
}
