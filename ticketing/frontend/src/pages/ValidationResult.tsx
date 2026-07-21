import { Container, Card, Icon, Badge, Button } from '@veraguas/ui';

/** Shell del resultado de validación en puerta: válido / ya usado / inválido. */
export function ValidationResult() {
    const status: 'valid' | 'used' | 'invalid' = 'valid';

    const STATUS_COPY = {
        valid: { icon: 'check_circle', color: 'text-emerald-500', tone: 'success' as const, label: 'Entrada válida' },
        used: { icon: 'error', color: 'text-amber-500', tone: 'warning' as const, label: 'Entrada ya utilizada' },
        invalid: { icon: 'cancel', color: 'text-red-500', tone: 'danger' as const, label: 'Entrada inválida' },
    }[status];

    return (
        <Container className="section-space flex max-w-md flex-col items-center text-center">
            <Icon name={STATUS_COPY.icon} size="lg" className={STATUS_COPY.color} />
            <h1 className="section-heading mt-4">{STATUS_COPY.label}</h1>
            <Card className="mt-6 flex w-full flex-col items-center gap-2">
                <Badge tone={STATUS_COPY.tone}>Zona General</Badge>
                <p className="text-sm text-text-main/60">Escaneada el 19 OCT, 18:32</p>
            </Card>
            <Button as="a" href="/escaner" variant="outline" className="mt-8">
                Escanear otra entrada
            </Button>
        </Container>
    );
}
