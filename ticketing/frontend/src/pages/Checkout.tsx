import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Container, Card, Button, LoadingState, ErrorState, Alert } from '@veraguas/ui';
import { getOrder, requestPayment, type OrderView } from '../api/ticketing';
import { useOrderFlow } from '../context/OrderFlowContext';
import { ApiError } from '../api/client';

export function Checkout() {
    const { orderId } = useOrderFlow();
    const navigate = useNavigate();
    const [order, setOrder] = useState<OrderView | null>(null);
    const [submitting, setSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        if (!orderId) {
            navigate('/');

            return;
        }
        getOrder(orderId).then((res) => setOrder(res.data));
    }, [orderId, navigate]);

    async function handlePay() {
        if (!orderId) return;
        setSubmitting(true);
        setError(null);
        try {
            const res = await requestPayment(orderId);
            if (res.data.payment_redirect_url) {
                window.location.href = res.data.payment_redirect_url;
            } else {
                navigate('/confirmacion');
            }
        } catch (err) {
            setError(err instanceof ApiError ? err.message : 'No se pudo iniciar el pago.');
            setSubmitting(false);
        }
    }

    if (!order) return <LoadingState label="Cargando checkout…" />;

    return (
        <Container className="section-space max-w-xl">
            <h1 className="section-heading mb-8">Checkout</h1>
            <Card className="flex flex-col gap-4">
                <p className="text-sm text-text-main/60">Orden {order.order_number}</p>
                <p className="text-2xl font-bold text-primary">
                    {order.currency} {order.total.toFixed(2)}
                </p>
                {order.hold_expires_at ? (
                    <Alert tone="info">
                        Tu cupo está reservado hasta {new Date(order.hold_expires_at).toLocaleTimeString('es-PA')}. Completa el pago
                        antes de que expire.
                    </Alert>
                ) : null}
                {error ? <ErrorState message={error} /> : null}
                <Button size="lg" pending={submitting} pendingLabel="Redirigiendo a pago…" onClick={handlePay} className="w-full">
                    Pagar con TiloPay
                </Button>
            </Card>
        </Container>
    );
}
