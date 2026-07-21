import type { ReactNode, TableHTMLAttributes } from 'react';
import { cx } from '../cx';

export interface Column<Row> {
    key: string;
    header: string;
    render: (row: Row) => ReactNode;
    align?: 'left' | 'right' | 'center';
}

export interface TableProps<Row> extends TableHTMLAttributes<HTMLTableElement> {
    columns: Column<Row>[];
    rows: Row[];
    rowKey: (row: Row) => string;
    emptyLabel?: string;
}

const ALIGN_CLASS = { left: 'text-left', right: 'text-right', center: 'text-center' } as const;

/** Tabla genérica de datos — sin lógica de negocio, el consumidor define columnas. */
export function Table<Row>({ columns, rows, rowKey, emptyLabel = 'Sin datos', className, ...rest }: TableProps<Row>) {
    return (
        <div className="overflow-x-auto rounded-lg border border-outline">
            <table className={cx('w-full border-collapse text-sm', className)} {...rest}>
                <thead className="bg-surface text-xs font-bold uppercase tracking-wide text-text-main/70">
                    <tr>
                        {columns.map((col) => (
                            <th key={col.key} scope="col" className={cx('px-4 py-3', ALIGN_CLASS[col.align ?? 'left'])}>
                                {col.header}
                            </th>
                        ))}
                    </tr>
                </thead>
                <tbody className="divide-y divide-outline">
                    {rows.length === 0 ? (
                        <tr>
                            <td colSpan={columns.length} className="px-4 py-8 text-center text-text-main/60">
                                {emptyLabel}
                            </td>
                        </tr>
                    ) : (
                        rows.map((row) => (
                            <tr key={rowKey(row)} className="hover:bg-surface/60">
                                {columns.map((col) => (
                                    <td key={col.key} className={cx('px-4 py-3', ALIGN_CLASS[col.align ?? 'left'])}>
                                        {col.render(row)}
                                    </td>
                                ))}
                            </tr>
                        ))
                    )}
                </tbody>
            </table>
        </div>
    );
}
