import { useEffect, useState } from 'react';
import { AdminLayout } from '../layouts/AdminLayout';
import { Table, type Column, Badge } from '@veraguas/ui';
import { adminFetch } from '../api';

interface EventReport {
    id: string;
    code: string;
    home_team: string;
    away_team: string;
    starts_at: string;
    status: string;
    capacity_total: number;
    capacity_sold: number;
    capacity_held: number;
}

export function Events() {
    const [events, setEvents] = useState<EventReport[]>([]);

    useEffect(() => {
        adminFetch<{ data: EventReport[] }>('/reports/events').then((res) => setEvents(res.data));
    }, []);

    const columns: Column<EventReport>[] = [
        { key: 'match', header: 'Partido', render: (e) => `${e.home_team} vs ${e.away_team}` },
        { key: 'starts_at', header: 'Fecha', render: (e) => new Date(e.starts_at).toLocaleString('es-PA') },
        { key: 'status', header: 'Estado', render: (e) => <Badge tone={e.status === 'on_sale' ? 'success' : 'neutral'}>{e.status}</Badge> },
        { key: 'capacity', header: 'Vendidos / Total', render: (e) => `${e.capacity_sold} / ${e.capacity_total}`, align: 'right' },
        { key: 'held', header: 'En reserva', render: (e) => e.capacity_held, align: 'right' },
    ];

    return (
        <AdminLayout title="Eventos">
            <Table columns={columns} rows={events} rowKey={(e) => e.id} emptyLabel="Sin eventos" />
        </AdminLayout>
    );
}
