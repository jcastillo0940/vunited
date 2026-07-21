import { AdminLayout } from '../layouts/AdminLayout';
import { Grid, Card } from '@veraguas/ui';

const STATS = [
    { label: 'Usuarios activos', value: '128' },
    { label: 'Noticias publicadas', value: '42' },
    { label: 'Páginas CMS', value: '17' },
];

export function Dashboard() {
    return (
        <AdminLayout title="Dashboard">
            <Grid cols={3}>
                {STATS.map((stat) => (
                    <Card key={stat.label}>
                        <p className="text-xs font-semibold uppercase tracking-wide text-text-main/60">{stat.label}</p>
                        <p className="mt-2 font-display text-3xl font-bold text-primary">{stat.value}</p>
                    </Card>
                ))}
            </Grid>
        </AdminLayout>
    );
}
