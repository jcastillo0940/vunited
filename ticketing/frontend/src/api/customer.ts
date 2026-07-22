import { apiFetch } from './client';

const TOKEN_KEY = 'veraguas-ticketing-customer-token';

export interface CustomerView {
    id: string;
    name: string;
    email: string;
}

export function getStoredCustomerToken(): string | null {
    return localStorage.getItem(TOKEN_KEY);
}

export function storeCustomerToken(token: string): void {
    localStorage.setItem(TOKEN_KEY, token);
}

export function clearCustomerToken(): void {
    localStorage.removeItem(TOKEN_KEY);
}

interface AuthResponse {
    token: string;
    customer: CustomerView;
}

export function registerCustomer(name: string, email: string, password: string, deviceName = 'web'): Promise<AuthResponse> {
    return apiFetch('/customers/register', { method: 'POST', body: { name, email, password, device_name: deviceName } });
}

export function loginCustomer(email: string, password: string, deviceName = 'web'): Promise<AuthResponse> {
    return apiFetch('/customers/login', { method: 'POST', body: { email, password, device_name: deviceName } });
}

export function logoutCustomer(): Promise<{ message: string }> {
    return apiFetch('/customers/logout', { method: 'POST' });
}

export function getMyProfile(): Promise<{ customer: CustomerView }> {
    return apiFetch('/customers/me');
}
