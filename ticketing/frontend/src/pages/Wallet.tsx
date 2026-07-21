import { Link } from 'react-router-dom';
import { Container, Grid, Card, Badge, EmptyState } from '@veraguas/ui';

const TICKETS = [
    { id: '1', match: 'Veraguas United vs Herrera FC', date: '19 OCT, 19:00', zone: 'General' },
];

export function Wallet() {
    return (
        <Container className="section-space">
            <h1 className="section-heading mb-10">Mi wallet</h1>
            {TICKETS.length === 0 ? (
                <EmptyState icon="confirmation_number" title="Todavía no tienes entradas" />
            ) : (
                <Grid cols={3}>
                    {TICKETS.map((ticket) => (
                        <Link key={ticket.id} to={`/ticket/${ticket.id}`}>
                            <Card className="flex flex-col gap-2">
                                <Badge tone="accent" className="w-fit">
                                    {ticket.zone}
                                </Badge>
                                <h2 className="font-display text-lg font-bold uppercase text-primary">{ticket.match}</h2>
                                <p className="text-sm text-text-main/60">{ticket.date}</p>
                            </Card>
                        </Link>
                    ))}
                </Grid>
            )}
        </Container>
    );
}
