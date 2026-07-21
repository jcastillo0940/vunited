import { useEffect, useRef, useState } from 'react';
import { useLocation, useParams } from 'react-router-dom';
import QRCode from 'qrcode';
import { Container, Card, Badge, Button, LoadingState, ErrorState } from '@veraguas/ui';
import { getTicket, googleWalletLink, type TicketView } from '../api/ticketing';
import { ApiError } from '../api/client';

export function Ticket() {
    const { id } = useParams<{ id: string }>();
    const location = useLocation();
    const [ticket, setTicket] = useState<TicketView | null>((location.state as { ticket?: TicketView } | null)?.ticket ?? null);
    const [error, setError] = useState<string | null>(null);
    const [walletMessage, setWalletMessage] = useState<string | null>(null);
    const canvasRef = useRef<HTMLCanvasElement>(null);

    useEffect(() => {
        if (ticket || !id) return;
        getTicket(id)
            .then((res) => setTicket(res.data))
            .catch(() => setError('No pudimos cargar esta entrada.'));
    }, [id, ticket]);

    useEffect(() => {
        if (ticket && canvasRef.current) {
            // El QR codifica UNICAMENTE ticket.qr_token (firmado, sin PII) -
            // nunca nombre/correo/precio, ver Fase 7 §7.
            QRCode.toCanvas(canvasRef.current, ticket.qr_token, { width: 220, margin: 1 }).catch(() => {
                setError('No se pudo generar el código QR.');
            });
        }
    }, [ticket]);

    async function handleGoogleWallet() {
        if (!ticket) return;
        try {
            const res = await googleWalletLink(ticket.id);
            window.location.href = res.save_url;
        } catch (err) {
            setWalletMessage(
                err instanceof ApiError && err.status === 501
                    ? 'Google Wallet aún no está configurado para este club.'
                    : 'No se pudo generar el enlace de Google Wallet.',
            );
        }
    }

    if (error) return <ErrorState message={error} />;
    if (!ticket) return <LoadingState label="Cargando entrada…" />;

    return (
        <Container className="section-space flex max-w-md flex-col items-center text-center">
            <h1 className="section-heading mb-8">Tu entrada</h1>
            <Card className="flex w-full flex-col items-center gap-4">
                <Badge tone="accent">{ticket.zone_name}</Badge>
                <p className="text-sm text-text-main/60">{ticket.seat_label ?? 'Admisión general'}</p>
                <canvas ref={canvasRef} role="img" aria-label="Código QR de la entrada" />
                <p className="text-xs text-text-main/50">Presenta este código en la entrada del estadio.</p>

                <div className="mt-4 flex w-full flex-col gap-2">
                    <Button variant="outline" onClick={handleGoogleWallet}>
                        Agregar a Google Wallet
                    </Button>
                    <Button variant="outline" disabled title="Apple Wallet pendiente de certificado (ver docs/architecture/wallets.md)">
                        Agregar a Apple Wallet
                    </Button>
                </div>
                {walletMessage ? <p className="text-xs text-amber-600">{walletMessage}</p> : null}
            </Card>
        </Container>
    );
}
