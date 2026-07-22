import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { Container, Grid, Card, Badge, EmptyState, LoadingState } from '@veraguas/ui';
import { getMyTickets, type TicketView } from '../api/ticketing';

export function Wallet() {
    const [tickets, setTickets] = useState<TicketView[] | null>(null);

    useEffect(() => {
        getMyTickets().then((res) => setTickets(res.data));
    }, []);

    if (tickets === null) return <LoadingState label="Cargando tus entradas…" />;

    return (
        <Container className="section-space">
            <h1 className="section-heading mb-10">Mi wallet</h1>
            {tickets.length === 0 ? (
                <EmptyState icon="confirmation_number" title="Todavía no tienes entradas" message="Compra tus boletos desde Eventos." />
            ) : (
                <Grid cols={3}>
                    {tickets.map((ticket) => (
                        <Link key={ticket.id} to={`/ticket/${ticket.id}`} state={{ ticket }}>
                            <Card className="flex flex-col gap-2">
                                <Badge tone={ticket.status === 'issued' ? 'accent' : ticket.status === 'used' ? 'neutral' : 'danger'} className="w-fit">
                                    {ticket.zone_name}
                                </Badge>
                                <p className="text-sm text-text-main/60">{ticket.seat_label ?? 'Admisión general'}</p>
                                <p className="text-xs uppercase text-text-main/40">{ticket.status}</p>
                            </Card>
                        </Link>
                    ))}
                </Grid>
            )}
        </Container>
    );
}
