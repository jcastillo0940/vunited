import { apiFetch } from './client';

const TOKEN_KEY = 'veraguas-store-admin-token';

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
    admin: { id: number; name: string };
}

export function login(email: string, password: string, deviceName: string): Promise<LoginResponse> {
    return apiFetch('/auth/login', { method: 'POST', body: { email, password, device_name: deviceName } });
}
