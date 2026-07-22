import { useState, type FormEvent } from 'react';
import { useNavigate } from 'react-router-dom';
import { Container, Card, FormField, Input, Button, Radio, Checkbox, ErrorState } from '@veraguas/ui';
import { apiFetch, ApiError } from '../api/client';
import { getStoredCartToken } from '../api/cart';

interface CheckoutResponse {
    order: { public_id: string; total: number; currency: string };
    payment: { checkout_url: string | null };
}

export function Checkout() {
    const navigate = useNavigate();
    const [submitting, setSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [paymentMethod, setPaymentMethod] = useState<'tilopay' | 'cash'>('tilopay');

    async function handleSubmit(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        setSubmitting(true);
        setError(null);

        const cartToken = getStoredCartToken();
        if (!cartToken) {
            setError('Tu carrito expiró. Vuelve al catálogo.');
            setSubmitting(false);

            return;
        }

        const form = new FormData(event.currentTarget);
        const idempotencyKey = crypto.randomUUID();

        try {
            const res = await apiFetch<CheckoutResponse>('/checkout', {
                method: 'POST',
                headers: { 'Idempotency-Key': idempotencyKey },
                body: {
                    cart_token: cartToken,
                    email: form.get('email'),
                    payment_method: paymentMethod,
                    shipping: {
                        name: form.get('name'),
                        phone: form.get('phone'),
                        address: form.get('address'),
                    },
                    consent: true,
                },
            });

            if (res.payment.checkout_url) {
                window.location.href = res.payment.checkout_url;
            } else {
                navigate('/pago/pendiente');
            }
        } catch (err) {
            setError(err instanceof ApiError ? err.message : 'No se pudo procesar el pago.');
            setSubmitting(false);
        }
    }

    return (
        <Container className="section-space max-w-2xl">
            <h1 className="section-heading mb-10">Checkout</h1>
            <form onSubmit={handleSubmit}>
                <Card>
                    <div className="grid gap-6 md:grid-cols-2">
                        <FormField htmlFor="name" label="Nombre completo" required>
                            <Input id="name" name="name" required />
                        </FormField>
                        <FormField htmlFor="email" label="Correo" required>
                            <Input id="email" name="email" type="email" required />
                        </FormField>
                        <FormField htmlFor="phone" label="Teléfono" required>
                            <Input id="phone" name="phone" type="tel" required />
                        </FormField>
                        <FormField htmlFor="address" label="Dirección de entrega" required>
                            <Input id="address" name="address" required />
                        </FormField>
                    </div>

                    <fieldset className="mt-8">
                        <legend className="text-sm font-semibold text-text-main">Método de pago</legend>
                        <div className="mt-3 flex flex-col gap-2">
                            <Radio
                                name="payment_method"
                                label="TiloPay (tarjeta)"
                                checked={paymentMethod === 'tilopay'}
                                onChange={() => setPaymentMethod('tilopay')}
                            />
                            <Radio
                                name="payment_method"
                                label="Efectivo"
                                checked={paymentMethod === 'cash'}
                                onChange={() => setPaymentMethod('cash')}
                            />
                        </div>
                    </fieldset>

                    <Checkbox name="consent" required label="Acepto los términos y condiciones" className="mt-6" />

                    {error ? (
                        <div className="mt-4">
                            <ErrorState message={error} />
                        </div>
                    ) : null}

                    <Button type="submit" size="lg" pending={submitting} pendingLabel="Procesando…" className="mt-8 w-full">
                        {paymentMethod === 'cash' ? 'Confirmar pedido' : 'Pagar con TiloPay'}
                    </Button>
                </Card>
            </form>
        </Container>
    );
}
