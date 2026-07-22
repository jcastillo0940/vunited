import { useState, type FormEvent } from 'react';
import { useNavigate } from 'react-router-dom';
import { Container, Card, FormField, Input, Button, ErrorState } from '@veraguas/ui';

export function OrderLookup() {
    const navigate = useNavigate();
    const [orderId, setOrderId] = useState('');
    const [error, setError] = useState<string | null>(null);

    function handleSubmit(event: FormEvent) {
        event.preventDefault();
        const value = orderId.trim();
        if (!value) {
            setError('Ingresa el número de orden.');
            return;
        }
        navigate(`/orden/${value}`);
    }

    return (
        <Container className="section-space max-w-lg">
            <h1 className="section-heading mb-10">Consultar mi orden</h1>
            <form onSubmit={handleSubmit}>
                <Card>
                    <FormField htmlFor="order-id" label="Número de orden" required>
                        <Input id="order-id" name="orderId" required value={orderId} onChange={(e) => setOrderId(e.target.value)} />
                    </FormField>
                    <Button type="submit" className="mt-6 w-full">
                        Buscar
                    </Button>
                </Card>
            </form>
            {error ? (
                <div className="mt-8">
                    <ErrorState title="No pudimos buscar tu orden" message={error} />
                </div>
            ) : null}
        </Container>
    );
}
