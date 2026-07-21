import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Container, Grid, Card, Badge, LoadingState, ErrorState } from '@veraguas/ui';
import { listZones, type ZoneSummary } from '../api/ticketing';
import { useOrderFlow } from '../context/OrderFlowContext';

export function ZoneSelection() {
    const { eventId, setSelection } = useOrderFlow();
    const navigate = useNavigate();
    const [zones, setZones] = useState<ZoneSummary[] | null>(null);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        if (!eventId) {
            navigate('/');

            return;
        }
        let active = true;
        listZones(eventId)
            .then((res) => active && setZones(res.data))
            .catch(() => active && setError('No pudimos cargar las zonas.'));

        return () => {
            active = false;
        };
    }, [eventId, navigate]);

    if (error) return <ErrorState message={error} />;
    if (!zones) return <LoadingState label="Cargando zonas…" />;

    return (
        <Container className="section-space">
            <h1 className="section-heading mb-10">Selecciona tu zona</h1>
            <Grid cols={3}>
                {zones.map((zone) => (
                    <button
                        key={zone.id}
                        type="button"
                        disabled={zone.sold_out}
                        onClick={() => {
                            setSelection(eventId!, zone.id);
                            navigate('/cantidad');
                        }}
                        className="text-left disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <Card className="flex flex-col gap-2">
                            <h2 className="font-display text-lg font-bold uppercase text-primary">{zone.name}</h2>
                            <p className="text-xl font-semibold text-text-main">
                                {zone.currency} {zone.price.toFixed(2)}
                            </p>
                            <Badge tone={zone.sold_out ? 'neutral' : 'success'} className="w-fit">
                                {zone.sold_out ? 'Agotado' : `${zone.available} disponibles`}
                            </Badge>
                        </Card>
                    </button>
                ))}
            </Grid>
        </Container>
    );
}
