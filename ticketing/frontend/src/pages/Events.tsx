import { Link } from 'react-router-dom';
import { Container, Grid, Card, Badge } from '@veraguas/ui';

const MOCK_EVENTS = [
    { id: '1', home: 'Veraguas United', away: 'Herrera FC', date: '19 OCT, 19:00', venue: 'Estadio Atalaya' },
    { id: '2', home: 'Veraguas United', away: 'Chiriquí FC', date: '02 NOV, 19:00', venue: 'Estadio Atalaya' },
];

export function Events() {
    return (
        <Container className="section-space">
            <p className="display-kicker mb-2">Boletería</p>
            <h1 className="section-heading mb-10">Próximos eventos</h1>
            <Grid cols={2}>
                {MOCK_EVENTS.map((event) => (
                    <Link key={event.id} to={`/eventos/${event.id}`}>
                        <Card className="flex flex-col gap-3">
                            <Badge tone="primary" className="w-fit">
                                {event.venue}
                            </Badge>
                            <h2 className="font-display text-xl font-bold uppercase text-primary">
                                {event.home} <span className="text-accent">vs</span> {event.away}
                            </h2>
                            <p className="text-sm text-text-main/60">{event.date}</p>
                        </Card>
                    </Link>
                ))}
            </Grid>
        </Container>
    );
}
