import { useEffect, useState } from 'react';
import { AdminLayout } from '../layouts/AdminLayout';
import { Table, type Column, Button } from '@veraguas/ui';
import { adminFetch } from '../api';

interface CashOrderRow {
    id: string;
    order_number: string;
    customer_email: string;
    total: number;
    event: string | null;
    created_at: string;
}

export function CashPayments() {
    const [orders, setOrders] = useState<CashOrderRow[]>([]);

    function reload() {
        adminFetch<{ data: CashOrderRow[] }>('/cash-payments').then((res) => setOrders(res.data));
    }

    useEffect(reload, []);

    async function handleConfirm(id: string) {
        await adminFetch(`/cash-payments/${id}/confirm`, { method: 'POST' });
        reload();
    }

    const columns: Column<CashOrderRow>[] = [
        { key: 'order_number', header: 'Orden', render: (o) => o.order_number },
        { key: 'event', header: 'Evento', render: (o) => o.event ?? '—' },
        { key: 'customer_email', header: 'Cliente', render: (o) => o.customer_email },
        { key: 'total', header: 'Total', render: (o) => `$${o.total.toFixed(2)}`, align: 'right' },
        {
            key: 'actions',
            header: 'Acciones',
            align: 'right',
            render: (o) => (
                <Button size="sm" onClick={() => handleConfirm(o.id)}>
                    Confirmar recibido
                </Button>
            ),
        },
    ];

    return (
        <AdminLayout title="Pagos en efectivo">
            <Table columns={columns} rows={orders} rowKey={(o) => o.id} emptyLabel="Sin pagos en efectivo pendientes" />
        </AdminLayout>
    );
}
