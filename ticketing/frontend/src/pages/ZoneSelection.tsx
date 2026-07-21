import { Link } from 'react-router-dom';
import { Container, Grid, Card, Badge } from '@veraguas/ui';

const ZONES = [
    { id: 'general', name: 'General', price: 'B/. 8.00', available: true },
    { id: 'preferencial', name: 'Preferencial', price: 'B/. 15.00', available: true },
    { id: 'palco', name: 'Palco', price: 'B/. 35.00', available: false },
];

export function ZoneSelection() {
    return (
        <Container className="section-space">
            <h1 className="section-heading mb-10">Selecciona tu zona</h1>
            <Grid cols={3}>
                {ZONES.map((zone) => (
                    <Link key={zone.id} to={zone.available ? '/cantidad' : '#'} aria-disabled={!zone.available}>
                        <Card className={`flex flex-col gap-2 ${!zone.available ? 'opacity-50' : ''}`}>
                            <h2 className="font-display text-lg font-bold uppercase text-primary">{zone.name}</h2>
                            <p className="text-xl font-semibold text-text-main">{zone.price}</p>
                            <Badge tone={zone.available ? 'success' : 'neutral'} className="w-fit">
                                {zone.available ? 'Disponible' : 'Agotado'}
                            </Badge>
                        </Card>
                    </Link>
                ))}
            </Grid>
        </Container>
    );
}
