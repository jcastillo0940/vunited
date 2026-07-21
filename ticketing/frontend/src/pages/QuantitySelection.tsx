import { useState } from 'react';
import { Container, Card, Button, Icon } from '@veraguas/ui';

export function QuantitySelection() {
    const [quantity, setQuantity] = useState(1);

    return (
        <Container className="section-space max-w-md">
            <h1 className="section-heading mb-8">¿Cuántas entradas?</h1>
            <Card className="flex flex-col items-center gap-6">
                <div className="flex items-center gap-6">
                    <button
                        type="button"
                        onClick={() => setQuantity((q) => Math.max(1, q - 1))}
                        aria-label="Reducir cantidad"
                        className="rounded-full border border-outline p-2 hover:bg-surface"
                    >
                        <Icon name="remove" size="sm" />
                    </button>
                    <span className="w-10 text-center font-display text-3xl font-bold text-primary">{quantity}</span>
                    <button
                        type="button"
                        onClick={() => setQuantity((q) => Math.min(6, q + 1))}
                        aria-label="Aumentar cantidad"
                        className="rounded-full border border-outline p-2 hover:bg-surface"
                    >
                        <Icon name="add" size="sm" />
                    </button>
                </div>
                <p className="text-xs text-text-main/60">Máximo 6 entradas por orden</p>
                <Button as="a" href="/resumen" size="lg" className="w-full">
                    Continuar
                </Button>
            </Card>
        </Container>
    );
}
