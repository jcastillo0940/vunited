import { useEffect, useState, type FormEvent } from 'react';
import { AdminLayout } from '../layouts/AdminLayout';
import { Table, type Column, Badge, Button, FormField, Input, Select, Card, Alert } from '@veraguas/ui';
import { adminFetch } from '../api';

interface OperatorRow {
    id: number;
    name: string;
    email: string;
    role: string;
    is_active: boolean;
}

export function Operators() {
    const [operators, setOperators] = useState<OperatorRow[]>([]);
    const [name, setName] = useState('');
    const [email, setEmail] = useState('');
    const [role, setRole] = useState('gate_operator');
    const [message, setMessage] = useState<string | null>(null);

    function reload() {
        adminFetch<{ data: OperatorRow[] }>('/operators').then((res) => setOperators(res.data));
    }

    useEffect(reload, []);

    async function handleSubmit(event: FormEvent) {
        event.preventDefault();
        setMessage(null);
        const res = await adminFetch<{ email: string; temporary_password: string }>('/operators', {
            method: 'POST',
            body: { name, email, role },
        });
        setMessage(`Operador ${res.email} creado. Contraseña temporal: ${res.temporary_password} (guárdala, no se vuelve a mostrar).`);
        setName('');
        setEmail('');
        reload();
    }

    async function handleRevoke(id: number) {
        await adminFetch(`/operators/${id}/revoke`, { method: 'POST' });
        reload();
    }

    const columns: Column<OperatorRow>[] = [
        { key: 'name', header: 'Nombre', render: (o) => o.name },
        { key: 'email', header: 'Correo', render: (o) => o.email },
        { key: 'role', header: 'Rol', render: (o) => o.role },
        { key: 'status', header: 'Estado', render: (o) => <Badge tone={o.is_active ? 'success' : 'neutral'}>{o.is_active ? 'Activo' : 'Desactivado'}</Badge> },
        {
            key: 'actions',
            header: 'Acciones',
            align: 'right',
            render: (o) => o.is_active ? (
                <Button size="sm" variant="outline" onClick={() => handleRevoke(o.id)}>
                    Desactivar
                </Button>
            ) : null,
        },
    ];

    return (
        <AdminLayout title="Operadores">
            <form onSubmit={handleSubmit} className="mb-8">
                <Card>
                    <div className="grid gap-4 md:grid-cols-3">
                        <FormField htmlFor="op-name" label="Nombre" required>
                            <Input id="op-name" required value={name} onChange={(e) => setName(e.target.value)} />
                        </FormField>
                        <FormField htmlFor="op-email" label="Correo" required>
                            <Input id="op-email" type="email" required value={email} onChange={(e) => setEmail(e.target.value)} />
                        </FormField>
                        <div>
                            <label htmlFor="op-role" className="text-sm font-semibold text-text-main">Rol</label>
                            <Select id="op-role" className="mt-1" value={role} onChange={(e) => setRole(e.target.value)}>
                                <option value="gate_operator">Operador de puerta</option>
                                <option value="viewer">Solo lectura</option>
                                <option value="admin">Admin</option>
                            </Select>
                        </div>
                    </div>
                    <Button type="submit" className="mt-4">
                        Crear operador
                    </Button>
                    {message ? <Alert tone="success" className="mt-4">{message}</Alert> : null}
                </Card>
            </form>

            <Table columns={columns} rows={operators} rowKey={(o) => String(o.id)} emptyLabel="Sin operadores" />
        </AdminLayout>
    );
}
