import { AdminLayout } from '../layouts/AdminLayout';
import { Table, type Column, Badge } from '@veraguas/ui';

interface AuditEntry {
    id: string;
    actor: string;
    action: string;
    target: string;
    at: string;
}

const ENTRIES: AuditEntry[] = [
    { id: '1', actor: 'ana@veraguasunited.test', action: 'Suspendió', target: 'usuario maria@veraguasunited.test', at: '2026-07-20 14:02' },
    { id: '2', actor: 'luis@veraguasunited.test', action: 'Publicó', target: 'noticia "Presentación de refuerzos"', at: '2026-07-19 09:15' },
];

/** Auditoría visual: quién hizo qué y cuándo, de solo lectura. */
export function AuditLog() {
    const columns: Column<AuditEntry>[] = [
        { key: 'at', header: 'Fecha', render: (row) => row.at },
        { key: 'actor', header: 'Usuario', render: (row) => row.actor },
        { key: 'action', header: 'Acción', render: (row) => <Badge tone="neutral">{row.action}</Badge> },
        { key: 'target', header: 'Sobre', render: (row) => row.target },
    ];

    return (
        <AdminLayout title="Auditoría">
            <Table columns={columns} rows={ENTRIES} rowKey={(row) => row.id} emptyLabel="Sin actividad registrada" />
        </AdminLayout>
    );
}
