import { useNavigate } from 'react-router-dom';
import { Container, Card, Button, Icon } from '@veraguas/ui';

/** Shell para el escáner de validación en puerta (operado por personal del club). */
export function Scanner() {
    const navigate = useNavigate();

    return (
        <Container className="section-space flex max-w-md flex-col items-center text-center">
            <h1 className="section-heading mb-8">Escáner de entradas</h1>
            <Card className="flex w-full flex-col items-center gap-4">
                <div className="flex h-56 w-full items-center justify-center rounded-lg border-2 border-dashed border-outline text-text-main/40">
                    <Icon name="qr_code_scanner" size="lg" />
                </div>
                <p className="text-sm text-text-main/60">Apunta la cámara al código QR de la entrada.</p>
                <Button size="lg" className="w-full" onClick={() => navigate('/escaner/resultado')}>
                    Simular escaneo
                </Button>
            </Card>
        </Container>
    );
}
