import { useEffect, useState } from 'react';
import { AdminLayout } from '../layouts/AdminLayout';
import { Table, type Column, Badge, Button } from '@veraguas/ui';
import { adminFetch } from '../api';

interface DeviceRow {
    id: string;
    name: string;
    is_active: boolean;
    last_seen_at: string | null;
    operator: { name: string } | null;
}

export function Devices() {
    const [devices, setDevices] = useState<DeviceRow[]>([]);

    function reload() {
        adminFetch<{ data: DeviceRow[] }>('/devices').then((res) => setDevices(res.data));
    }

    useEffect(reload, []);

    async function handleRevoke(id: string) {
        await adminFetch(`/devices/${id}/revoke`, { method: 'POST' });
        reload();
    }

    const columns: Column<DeviceRow>[] = [
        { key: 'name', header: 'Dispositivo', render: (d) => d.name },
        { key: 'operator', header: 'Último operador', render: (d) => d.operator?.name ?? '—' },
        { key: 'last_seen_at', header: 'Última actividad', render: (d) => (d.last_seen_at ? new Date(d.last_seen_at).toLocaleString('es-PA') : '—') },
        { key: 'status', header: 'Estado', render: (d) => <Badge tone={d.is_active ? 'success' : 'neutral'}>{d.is_active ? 'Activo' : 'Revocado'}</Badge> },
        {
            key: 'actions',
            header: 'Acciones',
            align: 'right',
            render: (d) => d.is_active ? (
                <Button size="sm" variant="outline" onClick={() => handleRevoke(d.id)}>
                    Revocar
                </Button>
            ) : null,
        },
    ];

    return (
        <AdminLayout title="Dispositivos">
            <Table columns={columns} rows={devices} rowKey={(d) => d.id} emptyLabel="Sin dispositivos registrados" />
        </AdminLayout>
    );
}
