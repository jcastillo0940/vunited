import { useState, type FormEvent } from 'react';
import { useNavigate } from 'react-router-dom';
import { Container, Card, FormField, Input, Button } from '@veraguas/ui';

export function Checkout() {
    const navigate = useNavigate();
    const [submitting, setSubmitting] = useState(false);

    function handleSubmit(event: FormEvent) {
        event.preventDefault();
        setSubmitting(true);
        // Shell: la integracion real con Payments/TiloPay llega en otra fase.
        window.setTimeout(() => navigate('/confirmacion'), 400);
    }

    return (
        <Container className="section-space max-w-xl">
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
                    </div>
                    <Button type="submit" size="lg" pending={submitting} pendingLabel="Procesando…" className="mt-8 w-full">
                        Pagar con TiloPay
                    </Button>
                </Card>
            </form>
        </Container>
    );
}
