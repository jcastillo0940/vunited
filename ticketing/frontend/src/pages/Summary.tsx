import { useEffect, useState, type FormEvent } from 'react';
import { useNavigate } from 'react-router-dom';
import { Container, Card, FormField, Input, Button, ErrorState, LoadingState } from '@veraguas/ui';
import { listZones, createOrder, type ZoneSummary } from '../api/ticketing';
import { useOrderFlow } from '../context/OrderFlowContext';
import { useCustomerAuth } from '../context/CustomerAuthContext';
import { ApiError } from '../api/client';

export function Summary() {
    const { eventId, zoneId, quantity, setOrderId } = useOrderFlow();
    const { customer } = useCustomerAuth();
    const navigate = useNavigate();
    const [zone, setZone] = useState<ZoneSummary | null>(null);
    const [name, setName] = useState(customer?.name ?? '');
    const [submitting, setSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        if (!eventId || !zoneId) {
            navigate('/zona');

            return;
        }
        listZones(eventId).then((res) => {
            setZone(res.data.find((z) => z.id === zoneId) ?? null);
        });
    }, [eventId, zoneId, navigate]);

    async function handleSubmit(event: FormEvent) {
        event.preventDefault();
        if (!eventId || !zoneId || !customer) return;
        setSubmitting(true);
        setError(null);

        try {
            const idempotencyKey = crypto.randomUUID();
            const res = await createOrder(eventId, {
                customer_email: customer.email,
                customer_name: name || undefined,
                idempotency_key: idempotencyKey,
                items: [{ zone_id: zoneId, quantity }],
            });
            setOrderId(res.data.id);
            navigate('/checkout');
        } catch (err) {
            setError(err instanceof ApiError ? err.message : 'No se pudo reservar el cupo. Intenta de nuevo.');
        } finally {
            setSubmitting(false);
        }
    }

    if (!zone) return <LoadingState label="Cargando resumen…" />;

    const total = zone.price * quantity;

    return (
        <Container className="section-space max-w-xl">
            <h1 className="section-heading mb-8">Resumen de tu orden</h1>
            <Card className="mb-6 flex items-center justify-between">
                <div>
                    <p className="font-semibold text-text-main">
                        {zone.name} × {quantity}
                    </p>
                    <p className="text-sm text-text-main/60">
                        {zone.currency} {zone.price.toFixed(2)} c/u
                    </p>
                </div>
                <span className="text-xl font-bold text-primary">
                    {zone.currency} {total.toFixed(2)}
                </span>
            </Card>

            {error ? <ErrorState message={error} /> : null}

            <form onSubmit={handleSubmit}>
                <Card>
                    <FormField htmlFor="email" label="Correo de tu cuenta">
                        <Input id="email" type="email" disabled value={customer?.email ?? ''} />
                    </FormField>
                    <div className="mt-4">
                        <FormField htmlFor="name" label="Nombre completo">
                            <Input id="name" value={name} onChange={(e) => setName(e.target.value)} />
                        </FormField>
                    </div>
                    <Button type="submit" size="lg" pending={submitting} pendingLabel="Reservando…" className="mt-6 w-full">
                        Reservar cupo y continuar
                    </Button>
                </Card>
            </form>
        </Container>
    );
}
