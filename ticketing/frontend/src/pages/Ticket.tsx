import { useParams } from 'react-router-dom';
import { Container, Card, Badge } from '@veraguas/ui';

export function Ticket() {
    const { id } = useParams<{ id: string }>();
    return (
        <Container className="section-space flex max-w-md flex-col items-center text-center">
            <h1 className="section-heading mb-8">Entrada #{id}</h1>
            <Card className="flex w-full flex-col items-center gap-4">
                <p className="font-display text-lg font-bold uppercase text-primary">
                    Veraguas United vs Herrera FC
                </p>
                <p className="text-sm text-text-main/60">19 OCT, 19:00 · Estadio Atalaya</p>
                <Badge tone="accent">Zona General</Badge>
                <div
                    role="img"
                    aria-label="Código QR de la entrada"
                    className="mt-4 flex h-40 w-40 items-center justify-center rounded-lg border-2 border-dashed border-outline text-text-main/40"
                >
                    QR
                </div>
                <p className="text-xs text-text-main/50">Presenta este código en la entrada del estadio.</p>
            </Card>
        </Container>
    );
}
