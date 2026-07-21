import { useEffect, useState } from 'react';
import { AdminLayout } from '../layouts/AdminLayout';
import { Table, type Column, Badge } from '@veraguas/ui';
import { adminFetch } from '../api';

interface OrderReport {
    id: string;
    order_number: string;
    status: string;
    customer_email: string;
    total: number;
    event: string | null;
    created_at: string;
}

const STATUS_TONE: Record<string, 'success' | 'warning' | 'danger' | 'neutral'> = {
    paid: 'success',
    tickets_issued: 'success',
    failed: 'danger',
    cancelled: 'danger',
    expired: 'neutral',
};

export function Orders() {
    const [orders, setOrders] = useState<OrderReport[]>([]);

    useEffect(() => {
        adminFetch<{ data: OrderReport[] }>('/reports/orders').then((res) => setOrders(res.data));
    }, []);

    const columns: Column<OrderReport>[] = [
        { key: 'order_number', header: 'Orden', render: (o) => o.order_number },
        { key: 'event', header: 'Evento', render: (o) => o.event ?? '—' },
        { key: 'customer_email', header: 'Cliente', render: (o) => o.customer_email },
        { key: 'status', header: 'Estado', render: (o) => <Badge tone={STATUS_TONE[o.status] ?? 'neutral'}>{o.status}</Badge> },
        { key: 'total', header: 'Total', render: (o) => `$${o.total.toFixed(2)}`, align: 'right' },
    ];

    return (
        <AdminLayout title="Órdenes">
            <Table columns={columns} rows={orders} rowKey={(o) => o.id} emptyLabel="Sin órdenes" />
        </AdminLayout>
    );
}
