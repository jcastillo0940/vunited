import { useEffect, useState } from 'react';
import { AdminLayout } from '../layouts/AdminLayout';
import { Grid, Card, LoadingState } from '@veraguas/ui';
import { adminFetch } from '../api';

interface EventReport {
    id: string;
    home_team: string;
    away_team: string;
    status: string;
    capacity_total: number;
    capacity_sold: number;
    capacity_held: number;
}

export function Dashboard() {
    const [events, setEvents] = useState<EventReport[] | null>(null);

    useEffect(() => {
        adminFetch<{ data: EventReport[] }>('/reports/events').then((res) => setEvents(res.data));
    }, []);

    if (!events) return <AdminLayout title="Dashboard"><LoadingState /></AdminLayout>;

    const totals = events.reduce(
        (acc, e) => ({
            capacity: acc.capacity + e.capacity_total,
            sold: acc.sold + e.capacity_sold,
            held: acc.held + e.capacity_held,
        }),
        { capacity: 0, sold: 0, held: 0 },
    );

    return (
        <AdminLayout title="Dashboard">
            <Grid cols={3}>
                <Card>
                    <p className="text-xs font-semibold uppercase tracking-wide text-text-main/60">Eventos activos</p>
                    <p className="mt-2 font-display text-3xl font-bold text-primary">{events.length}</p>
                </Card>
                <Card>
                    <p className="text-xs font-semibold uppercase tracking-wide text-text-main/60">Boletos vendidos</p>
                    <p className="mt-2 font-display text-3xl font-bold text-primary">{totals.sold}</p>
                </Card>
                <Card>
                    <p className="text-xs font-semibold uppercase tracking-wide text-text-main/60">Cupo en reserva activa</p>
                    <p className="mt-2 font-display text-3xl font-bold text-primary">{totals.held}</p>
                </Card>
            </Grid>
        </AdminLayout>
    );
}
