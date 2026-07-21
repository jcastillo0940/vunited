import { Container, Spinner, Button } from '@veraguas/ui';

export function PaymentPending() {
    return (
        <Container className="flex min-h-[60vh] flex-col items-center justify-center text-center">
            <Spinner size="lg" />
            <p className="display-kicker mt-6">Pago</p>
            <h1 className="section-heading mt-2">Tu pago está en proceso</h1>
            <p className="mt-4 max-w-md text-text-main/70">
                Estamos confirmando tu pago con el procesador. Esto puede tardar unos minutos.
            </p>
            <Button as="a" href="/orden" variant="outline" className="mt-8">
                Consultar estado de mi orden
            </Button>
        </Container>
    );
}
