import { useLocation, useNavigate } from 'react-router-dom';
import { Container, Card, Icon, Badge, Button } from '@veraguas/ui';
import type { ValidationResponse } from '../api/auth';

const RESULT_COPY: Record<string, { icon: string; color: string; tone: 'success' | 'warning' | 'danger'; label: string }> = {
    valid: { icon: 'check_circle', color: 'text-emerald-500', tone: 'success', label: 'Entrada válida' },
    already_used: { icon: 'error', color: 'text-amber-500', tone: 'warning', label: 'Entrada ya utilizada' },
    revoked: { icon: 'cancel', color: 'text-red-500', tone: 'danger', label: 'Entrada revocada' },
    wrong_event: { icon: 'cancel', color: 'text-red-500', tone: 'danger', label: 'Evento incorrecto' },
    wrong_door: { icon: 'cancel', color: 'text-red-500', tone: 'danger', label: 'Sin permiso para esta puerta' },
    invalid: { icon: 'cancel', color: 'text-red-500', tone: 'danger', label: 'QR inválido' },
    not_found: { icon: 'cancel', color: 'text-red-500', tone: 'danger', label: 'Boleto no encontrado' },
};

export function ValidationResult() {
    const navigate = useNavigate();
    const location = useLocation();
    const result = (location.state as { result?: ValidationResponse } | null)?.result;

    if (!result) {
        navigate('/escaner');

        return null;
    }

    const copy = RESULT_COPY[result.result] ?? RESULT_COPY.invalid;

    return (
        <Container className="section-space flex max-w-md flex-col items-center text-center">
            <Icon name={copy.icon} size="lg" className={copy.color} />
            <h1 className="section-heading mt-4">{copy.label}</h1>
            {result.ticket ? (
                <Card className="mt-6 flex w-full flex-col items-center gap-2">
                    <Badge tone={copy.tone}>{result.ticket.zone ?? 'Zona'}</Badge>
                    <p className="text-sm text-text-main/60">{result.ticket.seat_label ?? 'Admisión general'}</p>
                </Card>
            ) : null}
            <Button as="a" href="/escaner" variant="outline" className="mt-8">
                Escanear otra entrada
            </Button>
        </Container>
    );
}
