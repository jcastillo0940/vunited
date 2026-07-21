import { getStoredToken } from '../api/auth';

const BASE_URL = import.meta.env.VITE_API_BASE_URL ?? '/api';

export async function adminFetch<T>(path: string, options: { method?: string; body?: unknown } = {}): Promise<T> {
    const token = getStoredToken();
    const response = await fetch(`${BASE_URL}/admin${path}`, {
        method: options.method ?? 'GET',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            Authorization: `Bearer ${token}`,
            'X-Correlation-ID': crypto.randomUUID(),
        },
        body: options.body ? JSON.stringify(options.body) : undefined,
    });

    if (!response.ok) {
        throw new Error(`Error ${response.status} al consultar ${path}`);
    }

    return (await response.json()) as T;
}
