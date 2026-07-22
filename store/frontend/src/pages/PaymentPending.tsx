import { Container, Spinner, Button } from '@veraguas/ui';

export function PaymentPending() {
    return (
        <Container className="flex min-h-[60vh] flex-col items-center justify-center text-center">
            <Spinner size="lg" />
            <p className="display-kicker mt-6">Pago en efectivo</p>
            <h1 className="section-heading mt-2">Tu pedido está reservado</h1>
            {/* Texto placeholder: reemplazar con direccion/horario reales de pago en efectivo. */}
            <p className="mt-4 max-w-md text-text-main/70">
                Acércate a pagar en efectivo en nuestras oficinas. Un miembro del staff confirmará tu pago y tu pedido pasará a
                preparación.
            </p>
            <Button as="a" href="/orden" variant="outline" className="mt-8">
                Consultar estado de mi orden
            </Button>
        </Container>
    );
}
