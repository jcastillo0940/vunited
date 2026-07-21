import { Container, Card, Table, type Column, Button } from '@veraguas/ui';

interface SummaryLine {
    id: string;
    concept: string;
    amount: string;
}

const LINES: SummaryLine[] = [
    { id: '1', concept: 'General × 2', amount: 'B/. 16.00' },
    { id: '2', concept: 'Cargo por servicio', amount: 'B/. 1.50' },
];

const columns: Column<SummaryLine>[] = [
    { key: 'concept', header: 'Concepto', render: (row) => row.concept },
    { key: 'amount', header: 'Monto', render: (row) => row.amount, align: 'right' },
];

export function Summary() {
    return (
        <Container className="section-space max-w-xl">
            <h1 className="section-heading mb-8">Resumen de tu orden</h1>
            <Table columns={columns} rows={LINES} rowKey={(row) => row.id} />
            <Card className="mt-6 flex items-center justify-between">
                <span className="font-semibold text-text-main">Total</span>
                <span className="text-xl font-bold text-primary">B/. 17.50</span>
            </Card>
            <Button as="a" href="/checkout" size="lg" className="mt-8 w-full">
                Continuar a checkout
            </Button>
        </Container>
    );
}
