import { useEffect, useState } from 'react';
import { AdminLayout } from '../layouts/AdminLayout';
import { Table, type Column, Badge } from '@veraguas/ui';
import { adminFetch } from '../api';

interface ValidationReport {
    id: number;
    result: string;
    ticket_id: string | null;
    door: string | null;
    operator: string | null;
    correlation_id: string | null;
    occurred_at: string;
}

const RESULT_TONE: Record<string, 'success' | 'warning' | 'danger' | 'neutral'> = {
    valid: 'success',
    already_used: 'warning',
    revoked: 'danger',
    invalid: 'danger',
    wrong_event: 'danger',
    wrong_door: 'danger',
    not_found: 'neutral',
};

/** Auditoría visual: quién validó qué, cuándo, dónde y con qué resultado. */
export function Validations() {
    const [events, setEvents] = useState<ValidationReport[]>([]);

    useEffect(() => {
        adminFetch<{ data: ValidationReport[] }>('/reports/validations').then((res) => setEvents(res.data));
    }, []);

    const columns: Column<ValidationReport>[] = [
        { key: 'occurred_at', header: 'Fecha', render: (v) => new Date(v.occurred_at).toLocaleString('es-PA') },
        { key: 'result', header: 'Resultado', render: (v) => <Badge tone={RESULT_TONE[v.result] ?? 'neutral'}>{v.result}</Badge> },
        { key: 'ticket_id', header: 'Boleto', render: (v) => v.ticket_id ?? '—' },
        { key: 'door', header: 'Puerta', render: (v) => v.door ?? '—' },
        { key: 'operator', header: 'Operador', render: (v) => v.operator ?? '—' },
        { key: 'correlation_id', header: 'Correlation ID', render: (v) => v.correlation_id ?? '—' },
    ];

    return (
        <AdminLayout title="Validaciones">
            <Table columns={columns} rows={events} rowKey={(v) => String(v.id)} emptyLabel="Sin validaciones registradas" />
        </AdminLayout>
    );
}
