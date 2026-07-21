import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { Container, Grid, Card, Badge, LoadingState, ErrorState, EmptyState } from '@veraguas/ui';
import { listEvents, type EventSummary } from '../api/ticketing';

export function Events() {
    const [events, setEvents] = useState<EventSummary[] | null>(null);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        let active = true;
        listEvents()
            .then((res) => active && setEvents(res.data))
            .catch(() => active && setError('No pudimos cargar los eventos.'));

        return () => {
            active = false;
        };
    }, []);

    return (
        <Container className="section-space">
            <p className="display-kicker mb-2">Boletería</p>
            <h1 className="section-heading mb-10">Próximos eventos</h1>

            {error ? <ErrorState message={error} /> : null}
            {!error && events === null ? <LoadingState label="Cargando eventos…" /> : null}
            {!error && events?.length === 0 ? <EmptyState title="No hay eventos disponibles" /> : null}

            {events && events.length > 0 ? (
                <Grid cols={2}>
                    {events.map((event) => (
                        <Link key={event.id} to={`/eventos/${event.id}`}>
                            <Card className="flex flex-col gap-3">
                                <Badge tone={event.on_sale ? 'success' : 'neutral'} className="w-fit">
                                    {event.on_sale ? 'Venta abierta' : event.status}
                                </Badge>
                                <h2 className="font-display text-xl font-bold uppercase text-primary">
                                    {event.home_team} <span className="text-accent">vs</span> {event.away_team}
                                </h2>
                                <p className="text-sm text-text-main/60">
                                    {new Date(event.starts_at).toLocaleString('es-PA', { dateStyle: 'medium', timeStyle: 'short' })}
                                    {event.venue_name ? ` · ${event.venue_name}` : ''}
                                </p>
                            </Card>
                        </Link>
                    ))}
                </Grid>
            ) : null}
        </Container>
    );
}
