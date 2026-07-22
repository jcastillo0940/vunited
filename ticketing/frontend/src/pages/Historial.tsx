import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { Container, Card, Badge, EmptyState, LoadingState } from '@veraguas/ui';
import { getMyOrders, type OrderView } from '../api/ticketing';

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

const STATUS_TONE: Record<string, 'accent' | 'neutral' | 'danger'> = {
    paid: 'accent',
    tickets_issued: 'accent',
    hold_active: 'neutral',
    pending_payment: 'neutral',
    payment_processing: 'neutral',
    expired: 'danger',
    cancelled: 'danger',
    failed: 'danger',
    refund_pending: 'danger',
    refunded: 'danger',
};

export function Historial() {
    const [orders, setOrders] = useState<OrderView[] | null>(null);

    useEffect(() => {
        getMyOrders().then((res) => setOrders(res.data));
    }, []);

    if (orders === null) return <LoadingState label="Cargando tu historial…" />;

    return (
        <Container className="section-space">
            <h1 className="section-heading mb-10">Historial de compras</h1>
            {orders.length === 0 ? (
                <EmptyState icon="receipt_long" title="Todavía no tienes compras" message="Cuando compres boletos aparecerán aquí." />
            ) : (
                <div className="flex flex-col gap-4">
                    {orders.map((order) => (
                        <Link key={order.id} to={`/orden/${order.id}`}>
                            <Card className="flex items-center justify-between gap-4">
                                <div>
                                    <p className="font-semibold text-text-main">{order.event?.label ?? order.order_number}</p>
                                    <p className="text-sm text-text-main/60">
                                        Orden {order.order_number} · {order.currency} {order.total.toFixed(2)}
                                    </p>
                                </div>
                                <Badge tone={STATUS_TONE[order.status] ?? 'neutral'}>{STATUS_LABEL[order.status] ?? order.status}</Badge>
                            </Card>
                        </Link>
                    ))}
                </div>
            )}
        </Container>
    );
}
