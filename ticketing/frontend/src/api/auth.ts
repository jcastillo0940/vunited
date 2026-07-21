import { apiFetch } from './client';

const TOKEN_KEY = 'veraguas-ticketing-operator-token';

export function getStoredToken(): string | null {
    return localStorage.getItem(TOKEN_KEY);
}

export function storeToken(token: string): void {
    localStorage.setItem(TOKEN_KEY, token);
}

export function clearToken(): void {
    localStorage.removeItem(TOKEN_KEY);
}

export interface LoginResponse {
    token: string;
    operator: { id: number; name: string; role: string };
}

export function login(email: string, password: string, deviceName: string): Promise<LoginResponse> {
    return apiFetch('/auth/login', { method: 'POST', body: { email, password, device_name: deviceName } });
}

export interface ValidationResponse {
    valid: boolean;
    result: string;
    message: string;
    ticket?: { id: string; status: string; zone: string | null; seat_label: string | null };
}

export function validateTicket(token: string, doorId?: number, devicePublicId?: string): Promise<ValidationResponse> {
    const operatorToken = getStoredToken();

    return fetch(`${import.meta.env.VITE_API_BASE_URL ?? '/api'}/validate`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            Authorization: `Bearer ${operatorToken}`,
            'X-Correlation-ID': crypto.randomUUID(),
        },
        body: JSON.stringify({ token, door_id: doorId, device_public_id: devicePublicId }),
    }).then(async (res) => {
        const data = (await res.json()) as ValidationResponse;

        return data;
    });
}
