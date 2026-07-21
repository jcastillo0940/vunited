import { useState, type FormEvent } from 'react';
import { Container, Card, FormField, Input, Button, ErrorState } from '@veraguas/ui';

export function OrderLookup() {
    const [notFound, setNotFound] = useState(false);

    function handleSubmit(event: FormEvent) {
        event.preventDefault();
        // Shell: la consulta real usa un token opaco, no un ID autoincremental.
        setNotFound(true);
    }

    return (
        <Container className="section-space max-w-lg">
            <h1 className="section-heading mb-10">Consultar mi orden</h1>
            <form onSubmit={handleSubmit}>
                <Card>
                    <FormField htmlFor="order-token" label="Número de orden o token de consulta" required>
                        <Input id="order-token" name="orderToken" required />
                    </FormField>
                    <Button type="submit" className="mt-6 w-full">
                        Buscar
                    </Button>
                </Card>
            </form>
            {notFound ? (
                <div className="mt-8">
                    <ErrorState title="No encontramos esa orden" message="Verifica el número e intenta de nuevo." />
                </div>
            ) : null}
        </Container>
    );
}
