import { useEffect, useRef, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Container, Card, Button, Icon, Select, Input, Alert } from '@veraguas/ui';
import { getStoredToken, validateTicket } from '../api/auth';
import { listEvents, type EventSummary } from '../api/ticketing';

interface OfflineScan {
    token: string;
    scannedAt: string;
}

const OFFLINE_QUEUE_KEY = 'veraguas-ticketing-offline-scans';

function loadOfflineQueue(): OfflineScan[] {
    try {
        return JSON.parse(localStorage.getItem(OFFLINE_QUEUE_KEY) ?? '[]');
    } catch {
        return [];
    }
}

function saveOfflineQueue(queue: OfflineScan[]): void {
    localStorage.setItem(OFFLINE_QUEUE_KEY, JSON.stringify(queue));
}

function feedback(kind: 'valid' | 'invalid') {
    if ('vibrate' in navigator) {
        navigator.vibrate(kind === 'valid' ? 100 : [80, 60, 80]);
    }
}

/** Escáner de puerta: cámara (BarcodeDetector nativo si está disponible) + entrada manual + cola offline. */
export function Scanner() {
    const navigate = useNavigate();
    const [events, setEvents] = useState<EventSummary[]>([]);
    const [eventId, setEventId] = useState('');
    const [manualToken, setManualToken] = useState('');
    const [isOnline, setIsOnline] = useState(navigator.onLine);
    const [offlineCount, setOfflineCount] = useState(loadOfflineQueue().length);
    const [lastMessage, setLastMessage] = useState<string | null>(null);
    const videoRef = useRef<HTMLVideoElement>(null);
    const [cameraSupported] = useState(() => 'BarcodeDetector' in window);

    useEffect(() => {
        if (!getStoredToken()) {
            navigate('/escaner/login');

            return;
        }
        listEvents().then((res) => setEvents(res.data));
    }, [navigate]);

    useEffect(() => {
        function goOnline() {
            setIsOnline(true);
            void syncOfflineQueue();
        }
        function goOffline() {
            setIsOnline(false);
        }
        window.addEventListener('online', goOnline);
        window.addEventListener('offline', goOffline);

        return () => {
            window.removeEventListener('online', goOnline);
            window.removeEventListener('offline', goOffline);
        };
    }, []);

    async function syncOfflineQueue() {
        const queue = loadOfflineQueue();
        if (queue.length === 0) return;

        for (const scan of queue) {
            await validateTicket(scan.token).catch(() => null);
        }
        saveOfflineQueue([]);
        setOfflineCount(0);
    }

    async function handleScanValue(token: string) {
        if (!isOnline) {
            const queue = loadOfflineQueue();
            queue.push({ token, scannedAt: new Date().toISOString() });
            saveOfflineQueue(queue);
            setOfflineCount(queue.length);
            feedback('invalid');
            setLastMessage('Sin conexión: escaneo guardado para sincronizar después.');

            return;
        }

        const result = await validateTicket(token, undefined, undefined);
        feedback(result.valid ? 'valid' : 'invalid');
        navigate('/escaner/resultado', { state: { result } });
    }

    function handleManualSubmit(event: React.FormEvent) {
        event.preventDefault();
        if (manualToken.trim()) void handleScanValue(manualToken.trim());
    }

    async function startCamera() {
        if (!cameraSupported || !videoRef.current) return;
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            videoRef.current.srcObject = stream;
            await videoRef.current.play();

            // @ts-expect-error BarcodeDetector no tiene tipos oficiales de TS todavia.
            const detector = new window.BarcodeDetector({ formats: ['qr_code'] });
            const tick = async () => {
                if (!videoRef.current) return;
                try {
                    const codes = await detector.detect(videoRef.current);
                    if (codes.length > 0) {
                        void handleScanValue(codes[0].rawValue);

                        return;
                    }
                } catch {
                    // frame no decodificable, seguimos intentando
                }
                requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        } catch {
            setLastMessage('No se pudo acceder a la cámara. Usa la entrada manual.');
        }
    }

    return (
        <Container className="section-space flex max-w-md flex-col items-center text-center">
            <h1 className="section-heading mb-4">Escáner de entradas</h1>

            {!isOnline ? (
                <Alert tone="warning" className="mb-4 w-full">
                    Sin conexión — los escaneos se guardan localmente ({offlineCount} pendientes) y se sincronizan al reconectar.
                </Alert>
            ) : offlineCount > 0 ? (
                <Alert tone="info" className="mb-4 w-full">
                    Sincronizando {offlineCount} escaneos pendientes…
                </Alert>
            ) : null}

            <div className="mb-4 w-full text-left">
                <label htmlFor="event-select" className="text-sm font-semibold text-text-main">
                    Evento
                </label>
                <Select id="event-select" className="mt-1" value={eventId} onChange={(e) => setEventId(e.target.value)}>
                    <option value="">Selecciona un evento</option>
                    {events.map((event) => (
                        <option key={event.id} value={event.id}>
                            {event.home_team} vs {event.away_team}
                        </option>
                    ))}
                </Select>
            </div>

            <Card className="flex w-full flex-col items-center gap-4">
                <div className="flex h-56 w-full items-center justify-center rounded-lg border-2 border-dashed border-outline bg-surface">
                    <video ref={videoRef} className="h-full w-full rounded-lg object-cover" muted playsInline />
                    {!cameraSupported ? <Icon name="qr_code_scanner" size="lg" className="absolute text-text-main/40" /> : null}
                </div>
                {cameraSupported ? (
                    <Button size="lg" className="w-full" onClick={startCamera}>
                        Activar cámara
                    </Button>
                ) : (
                    <p className="text-xs text-text-main/60">Cámara no disponible en este navegador — usa entrada manual.</p>
                )}

                <form onSubmit={handleManualSubmit} className="flex w-full gap-2">
                    <Input
                        aria-label="Código del boleto (entrada manual)"
                        placeholder="Pega o escribe el código del QR"
                        value={manualToken}
                        onChange={(e) => setManualToken(e.target.value)}
                    />
                    <Button type="submit">Validar</Button>
                </form>
            </Card>

            {lastMessage ? <p className="mt-4 text-sm text-text-main/60">{lastMessage}</p> : null}
        </Container>
    );
}
