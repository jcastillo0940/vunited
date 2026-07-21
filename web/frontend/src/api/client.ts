const BASE_URL = import.meta.env.VITE_API_BASE_URL ?? '/api/v1/web';

export class ApiError extends Error {
    status: number;
    correlationId: string;

    constructor(message: string, status: number, correlationId: string) {
        super(message);
        this.name = 'ApiError';
        this.status = status;
        this.correlationId = correlationId;
    }
}

function newCorrelationId(): string {
    return crypto.randomUUID();
}

export interface RequestOptions {
    method?: 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE';
    body?: unknown;
    signal?: AbortSignal;
}

/**
 * Cliente API mínimo: agrega X-Correlation-ID a cada solicitud (el mismo
 * mecanismo que nginx/PHP-FPM propagan en Fase 2 vía
 * fastcgi_param HTTP_X_CORRELATION_ID) y normaliza errores HTTP.
 */
export async function apiFetch<T>(path: string, options: RequestOptions = {}): Promise<T> {
    const correlationId = newCorrelationId();
    const response = await fetch(`${BASE_URL}${path}`, {
        method: options.method ?? 'GET',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-Correlation-ID': correlationId,
            ...(sessionStorage.getItem('web_admin_token') ? { Authorization: `Bearer ${sessionStorage.getItem('web_admin_token')}` } : {}),
        },
        body: options.body ? JSON.stringify(options.body) : undefined,
        signal: options.signal,
    });

    if (!response.ok) {
        const responseCorrelationId = response.headers.get('X-Correlation-ID') ?? correlationId;
        throw new ApiError(`Error ${response.status} al consultar ${path}`, response.status, responseCorrelationId);
    }

    return (await response.json()) as T;
}

export function setAuthToken(token: string) { sessionStorage.setItem('web_admin_token', token); }
export function clearAuthToken() { sessionStorage.removeItem('web_admin_token'); }
