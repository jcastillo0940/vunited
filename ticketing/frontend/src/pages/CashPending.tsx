import { Container, Alert, Button } from '@veraguas/ui';
import { useLocation } from 'react-router-dom';
import type { OrderView } from '../api/ticketing';

export function CashPending() {
    const location = useLocation();
    const order = (location.state as { order?: OrderView } | null)?.order;

    return (
        <Container className="flex min-h-[60vh] flex-col items-center justify-center text-center">
            <p className="display-kicker mt-6">Pago en efectivo</p>
            <h1 className="section-heading mt-2">Tu cupo está reservado</h1>
            {/* Texto placeholder: reemplazar con direccion/horario reales de pago en efectivo. */}
            <p className="mt-4 max-w-md text-text-main/70">
                Acércate a pagar en efectivo en nuestras oficinas antes de que expire tu reserva. Un miembro del staff confirmará tu
                pago y recibirás tus boletos por este medio.
            </p>
            {order ? (
                <Alert tone="info" className="mt-6">
                    Orden {order.order_number} — {order.currency} {order.total.toFixed(2)}
                </Alert>
            ) : null}
            <Button as="a" href={order ? `/orden/${order.id}` : '/orden'} variant="outline" className="mt-8">
                Consultar estado de mi orden
            </Button>
        </Container>
    );
}
