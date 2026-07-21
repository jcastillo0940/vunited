import { AdminLayout } from '../layouts/AdminLayout';
import { Grid, Card } from '@veraguas/ui';
import { useEffect, useState } from 'react';
import { apiFetch } from '../../api/client';

const STATS = [
    { label: 'Usuarios activos', value: '128' },
    { label: 'Noticias publicadas', value: '42' },
    { label: 'Páginas CMS', value: '17' },
];

export function Dashboard() {
    const [stats, setStats] = useState(STATS);
    useEffect(() => { apiFetch<{users:number,news:number,pages:number}>('/admin/dashboard').then((d) => setStats([{label:'Usuarios activos',value:String(d.users)},{label:'Noticias publicadas',value:String(d.news)},{label:'Páginas CMS',value:String(d.pages)}])).catch(() => undefined); }, []);
    return (
        <AdminLayout title="Dashboard">
            <Grid cols={3}>
                {stats.map((stat) => (
                    <Card key={stat.label}>
                        <p className="text-xs font-semibold uppercase tracking-wide text-text-main/60">{stat.label}</p>
                        <p className="mt-2 font-display text-3xl font-bold text-primary">{stat.value}</p>
                    </Card>
                ))}
            </Grid>
        </AdminLayout>
    );
}
