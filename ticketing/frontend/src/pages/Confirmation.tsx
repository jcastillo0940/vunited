import { useEffect, useState } from 'react';
import { Container, Card, Icon, Button, Badge, LoadingState } from '@veraguas/ui';
import { getOrder, type OrderView } from '../api/ticketing';
import { useOrderFlow } from '../context/OrderFlowContext';

export function Confirmation() {
    const { orderId } = useOrderFlow();
    const [order, setOrder] = useState<OrderView | null>(null);

    useEffect(() => {
        if (!orderId) return;
        getOrder(orderId).then((res) => setOrder(res.data));
    }, [orderId]);

    if (!order) return <LoadingState label="Confirmando tu orden…" />;

    const success = ['paid', 'tickets_issued'].includes(order.status);

    return (
        <Container className="section-space max-w-xl text-center">
            <Icon name={success ? 'check_circle' : 'hourglass_top'} size="lg" className={success ? 'mx-auto text-emerald-500' : 'mx-auto text-amber-500'} />
            <h1 className="section-heading mt-4">{success ? '¡Compra confirmada!' : 'Procesando tu compra'}</h1>
            <Card className="mt-8 flex items-center justify-between">
                <span className="text-sm text-text-main/60">Número de orden</span>
                <Badge tone={success ? 'success' : 'warning'}>{order.order_number}</Badge>
            </Card>
            {success ? (
                <Button as="a" href="/wallet" className="mt-8">
                    Ver mis entradas
                </Button>
            ) : (
                <Button as="a" href={`/orden/${order.id}`} className="mt-8">
                    Ver estado de mi orden
                </Button>
            )}
        </Container>
    );
}
