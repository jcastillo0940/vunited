import { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { Container, Card, Button, LoadingState, ErrorState } from '@veraguas/ui';
import { getEvent, type EventSummary } from '../api/ticketing';
import { useOrderFlow } from '../context/OrderFlowContext';

export function MatchDetail() {
    const { id } = useParams<{ id: string }>();
    const navigate = useNavigate();
    const { setSelection } = useOrderFlow();
    const [event, setEvent] = useState<EventSummary | null>(null);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        if (!id) return;
        let active = true;
        getEvent(id)
            .then((res) => active && setEvent(res.data))
            .catch(() => active && setError('No pudimos cargar este evento.'));

        return () => {
            active = false;
        };
    }, [id]);

    if (error) return <ErrorState message={error} />;
    if (!event) return <LoadingState label="Cargando evento…" />;

    return (
        <Container className="section-space max-w-2xl">
            <p className="display-kicker mb-2">{event.code}</p>
            <h1 className="section-heading mb-8">
                {event.home_team} vs {event.away_team}
            </h1>
            <Card className="flex flex-col gap-4">
                <p className="text-text-main/70">
                    {new Date(event.starts_at).toLocaleString('es-PA', { dateStyle: 'full', timeStyle: 'short' })}
                    {event.venue_name ? ` · ${event.venue_name}` : ''}
                </p>
                <Button
                    size="lg"
                    className="w-fit"
                    disabled={!event.on_sale}
                    onClick={() => {
                        setSelection(event.id, '');
                        navigate('/zona');
                    }}
                >
                    {event.on_sale ? 'Elegir zona' : 'Venta no disponible'}
                </Button>
            </Card>
        </Container>
    );
}
