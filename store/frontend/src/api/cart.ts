import { apiFetch } from './client';

const CART_TOKEN_KEY = 'veraguas-store-cart-token';

export interface CartItemView {
    id: number;
    product_id: number;
    quantity: number;
    unit_price: number;
    product: { id: number; name: string; slug: string; price: number };
}

export interface CartView {
    id: number;
    token: string;
    currency: string;
    expires_at: string;
    items: CartItemView[];
}

export function getStoredCartToken(): string | null {
    return localStorage.getItem(CART_TOKEN_KEY);
}

function storeCartToken(token: string): void {
    localStorage.setItem(CART_TOKEN_KEY, token);
}

/** Obtiene el carrito actual (o crea uno nuevo si el token guardado ya no es valido). */
export async function fetchCart(): Promise<CartView> {
    const token = getStoredCartToken();
    const cart = await apiFetch<CartView>('/cart', {
        headers: token ? { 'X-Cart-Token': token } : {},
    });
    storeCartToken(cart.token);

    return cart;
}

export async function addToCart(productId: number, quantity: number): Promise<CartView> {
    const cart = await fetchCart();

    return apiFetch<CartView>('/cart/items', {
        method: 'POST',
        body: { cart_token: cart.token, product_id: productId, quantity },
    });
}
