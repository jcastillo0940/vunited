import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { Container, Card, Badge, Button, LoadingState, ErrorState } from '@veraguas/ui';
import { getOrder, type OrderView } from '../api/ticketing';

const STATUS_LABEL: Record<string, string> = {
    draft: 'Iniciando',
    hold_active: 'Cupo reservado',
    pending_payment: 'Esperando pago',
    payment_processing: 'Procesando pago',
    paid: 'Pagado',
    tickets_issued: 'Entradas emitidas',
    expired: 'Reserva expirada',
    cancelled: 'Cancelada',
    refund_pending: 'Reembolso en proceso',
    refunded: 'Reembolsada',
    failed: 'Fallida',
};

export function OrderStatus() {
    const { orderId } = useParams<{ orderId: string }>();
    const [order, setOrder] = useState<OrderView | null>(null);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        if (!orderId) return;
        getOrder(orderId)
            .then((res) => setOrder(res.data))
            .catch(() => setError('No encontramos esa orden.'));
    }, [orderId]);

    if (error) return <ErrorState message={error} />;
    if (!order) return <LoadingState label="Consultando tu orden…" />;

    return (
        <Container className="section-space max-w-xl">
            <h1 className="section-heading mb-8">Estado de tu orden</h1>
            <Card className="flex flex-col gap-4">
                <div className="flex items-center justify-between">
                    <span className="text-sm text-text-main/60">Orden</span>
                    <span className="font-semibold">{order.order_number}</span>
                </div>
                <div className="flex items-center justify-between">
                    <span className="text-sm text-text-main/60">Estado</span>
                    <Badge tone={order.status === 'tickets_issued' || order.status === 'paid' ? 'success' : 'neutral'}>
                        {STATUS_LABEL[order.status] ?? order.status}
                    </Badge>
                </div>
                <div className="flex items-center justify-between">
                    <span className="text-sm text-text-main/60">Total</span>
                    <span className="font-semibold">
                        {order.currency} {order.total.toFixed(2)}
                    </span>
                </div>
                {order.status === 'tickets_issued' ? (
                    <Button as="a" href="/wallet">
                        Ver mis entradas
                    </Button>
                ) : null}
            </Card>
        </Container>
    );
}
