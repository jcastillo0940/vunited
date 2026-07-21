import { Icon } from './Icon';
import { cx } from '../cx';

export interface PaginationProps {
    page: number;
    totalPages: number;
    onChange: (page: number) => void;
}

export function Pagination({ page, totalPages, onChange }: PaginationProps) {
    if (totalPages <= 1) return null;

    return (
        <nav aria-label="Paginación" className="flex items-center justify-center gap-2">
            <button
                type="button"
                onClick={() => onChange(page - 1)}
                disabled={page <= 1}
                aria-label="Página anterior"
                className="rounded-md p-2 text-primary hover:bg-surface disabled:cursor-not-allowed disabled:opacity-40"
            >
                <Icon name="chevron_left" size="sm" />
            </button>
            <span className="text-sm text-text-main" aria-current="page">
                {page} / {totalPages}
            </span>
            <button
                type="button"
                onClick={() => onChange(page + 1)}
                disabled={page >= totalPages}
                aria-label="Página siguiente"
                className={cx('rounded-md p-2 text-primary hover:bg-surface disabled:cursor-not-allowed disabled:opacity-40')}
            >
                <Icon name="chevron_right" size="sm" />
            </button>
        </nav>
    );
}
