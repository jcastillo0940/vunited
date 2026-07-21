import type { ReactNode } from 'react';
import { Icon } from './Icon';
import { Spinner } from './Spinner';
import { Button } from './Button';

export interface LoadingStateProps {
    label?: string;
}

/** Equivalente tipado de LoadingState.jsx del frontend actual. */
export function LoadingState({ label = 'Cargando…' }: LoadingStateProps) {
    return (
        <div className="flex flex-col items-center justify-center gap-4 py-16 text-text-main/70">
            <Spinner size="lg" />
            <p className="text-sm">{label}</p>
        </div>
    );
}

export interface ErrorStateProps {
    title?: string;
    message?: string;
    onRetry?: () => void;
    retryLabel?: string;
}

/** Equivalente tipado de ErrorState.jsx del frontend actual. */
export function ErrorState({
    title = 'Ocurrió un error',
    message = 'No pudimos cargar esta información. Intenta de nuevo.',
    onRetry,
    retryLabel = 'Reintentar',
}: ErrorStateProps) {
    return (
        <div role="alert" className="flex flex-col items-center justify-center gap-4 py-16 text-center">
            <Icon name="error" size="lg" className="text-red-500" />
            <div>
                <p className="font-display text-lg font-bold uppercase text-primary">{title}</p>
                <p className="mt-1 text-sm text-text-main/70">{message}</p>
            </div>
            {onRetry ? (
                <Button variant="outline" size="sm" onClick={onRetry}>
                    {retryLabel}
                </Button>
            ) : null}
        </div>
    );
}

export interface EmptyStateProps {
    icon?: string;
    title?: string;
    message?: string;
    action?: ReactNode;
}

/** Equivalente tipado de EmptyState.jsx del frontend actual. */
export function EmptyState({ icon = 'inbox', title = 'Nada por aquí todavía', message, action }: EmptyStateProps) {
    return (
        <div className="flex flex-col items-center justify-center gap-3 py-16 text-center text-text-main/70">
            <Icon name={icon} size="lg" className="text-outline" />
            <p className="font-display text-lg font-bold uppercase text-primary">{title}</p>
            {message ? <p className="max-w-sm text-sm">{message}</p> : null}
            {action}
        </div>
    );
}
