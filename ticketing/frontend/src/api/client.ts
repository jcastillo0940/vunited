// boletos.wp-pa.com sirve el backend de ticketing directo en /api (dominio
// dedicado 1:1, no via el gateway compartido api.veraguas.internal de Fase 2).
const BASE_URL = import.meta.env.VITE_API_BASE_URL ?? '/api';

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

/** Agrega X-Correlation-ID a cada solicitud y normaliza errores HTTP. */
export async function apiFetch<T>(path: string, options: RequestOptions = {}): Promise<T> {
    const correlationId = newCorrelationId();
    const response = await fetch(`${BASE_URL}${path}`, {
        method: options.method ?? 'GET',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-Correlation-ID': correlationId,
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
