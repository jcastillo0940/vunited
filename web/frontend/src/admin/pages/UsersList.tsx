import { useMemo, useState } from 'react';
import { AdminLayout } from '../layouts/AdminLayout';
import { ConfirmDialog } from '../components/ConfirmDialog';
import { Table, type Column, Select, Badge, Button, Pagination } from '@veraguas/ui';

interface AdminUser {
    id: string;
    name: string;
    email: string;
    role: 'admin' | 'editor' | 'viewer';
    status: 'active' | 'suspended';
}

const MOCK_USERS: AdminUser[] = [
    { id: '1', name: 'Ana Pérez', email: 'ana@veraguasunited.test', role: 'admin', status: 'active' },
    { id: '2', name: 'Luis Gómez', email: 'luis@veraguasunited.test', role: 'editor', status: 'active' },
    { id: '3', name: 'María Ríos', email: 'maria@veraguasunited.test', role: 'viewer', status: 'suspended' },
];

/** Formularios/filtros/tablas/estados/confirmaciones del panel — gestión de usuarios. */
export function UsersList() {
    const [roleFilter, setRoleFilter] = useState<string>('all');
    const [page, setPage] = useState(1);
    const [pendingSuspend, setPendingSuspend] = useState<AdminUser | null>(null);

    const filtered = useMemo(
        () => MOCK_USERS.filter((user) => roleFilter === 'all' || user.role === roleFilter),
        [roleFilter],
    );

    const columns: Column<AdminUser>[] = [
        { key: 'name', header: 'Nombre', render: (row) => row.name },
        { key: 'email', header: 'Correo', render: (row) => row.email },
        { key: 'role', header: 'Rol', render: (row) => row.role },
        {
            key: 'status',
            header: 'Estado',
            render: (row) => <Badge tone={row.status === 'active' ? 'success' : 'warning'}>{row.status === 'active' ? 'Activo' : 'Suspendido'}</Badge>,
        },
        {
            key: 'actions',
            header: 'Acciones',
            align: 'right',
            render: (row) => (
                <Button size="sm" variant="outline" onClick={() => setPendingSuspend(row)}>
                    {row.status === 'active' ? 'Suspender' : 'Reactivar'}
                </Button>
            ),
        },
    ];

    return (
        <AdminLayout title="Usuarios">
            <div className="mb-4 flex items-center gap-3">
                <label htmlFor="role-filter" className="text-sm font-semibold text-text-main">
                    Filtrar por rol
                </label>
                <Select id="role-filter" className="max-w-xs" value={roleFilter} onChange={(e) => setRoleFilter(e.target.value)}>
                    <option value="all">Todos</option>
                    <option value="admin">Admin</option>
                    <option value="editor">Editor</option>
                    <option value="viewer">Viewer</option>
                </Select>
            </div>

            <Table columns={columns} rows={filtered} rowKey={(row) => row.id} emptyLabel="Sin usuarios para este filtro" />

            <div className="mt-6">
                <Pagination page={page} totalPages={1} onChange={setPage} />
            </div>

            <ConfirmDialog
                open={pendingSuspend !== null}
                title={pendingSuspend?.status === 'active' ? 'Suspender usuario' : 'Reactivar usuario'}
                message={`¿Confirmas ${pendingSuspend?.status === 'active' ? 'suspender' : 'reactivar'} a ${pendingSuspend?.name}?`}
                confirmLabel="Confirmar"
                onConfirm={() => setPendingSuspend(null)}
                onCancel={() => setPendingSuspend(null)}
            />
        </AdminLayout>
    );
}
