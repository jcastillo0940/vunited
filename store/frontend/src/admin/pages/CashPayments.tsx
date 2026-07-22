import { useEffect, useState } from 'react';
import { AdminLayout } from '../layouts/AdminLayout';
import { Table, type Column, Button } from '@veraguas/ui';
import { adminFetch } from '../api';

interface CashOrderRow {
    id: string;
    email: string;
    total: number;
    currency: string;
    created_at: string | null;
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
        { key: 'id', header: 'Orden', render: (o) => o.id },
        { key: 'email', header: 'Cliente', render: (o) => o.email },
        { key: 'total', header: 'Total', render: (o) => `${o.currency} ${o.total.toFixed(2)}`, align: 'right' },
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
