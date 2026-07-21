import { apiFetch } from './client';

export interface EventSummary {
    id: string;
    code: string;
    home_team: string;
    away_team: string;
    competition: string | null;
    starts_at: string;
    venue_name: string | null;
    venue_location: string | null;
    status: string;
    on_sale: boolean;
    purchase_limit_per_buyer: number;
}

export interface ZoneSummary {
    id: string;
    name: string;
    slug: string;
    description: string | null;
    kind: 'general' | 'seated';
    price: number;
    currency: string;
    available: number;
    sold_out: boolean;
    purchase_limit_per_buyer: number | null;
}

export interface OrderItemView {
    zone_id: string;
    zone_name: string;
    seat_id: string | null;
    seat_label: string | null;
    quantity: number;
    unit_price: number;
    line_total: number;
}

export interface OrderView {
    id: string;
    order_number: string;
    status: string;
    currency: string;
    subtotal: number;
    total: number;
    hold_expires_at: string | null;
    payment_redirect_url: string | null;
    items?: OrderItemView[];
}

export function listEvents(): Promise<{ data: EventSummary[] }> {
    return apiFetch('/events');
}

export function getEvent(eventId: string): Promise<{ data: EventSummary }> {
    return apiFetch(`/events/${eventId}`);
}

export function listZones(eventId: string): Promise<{ data: ZoneSummary[] }> {
    return apiFetch(`/events/${eventId}/zones`);
}

export function createOrder(
    eventId: string,
    payload: {
        customer_email: string;
        customer_name?: string;
        customer_phone?: string;
        idempotency_key?: string;
        items: Array<{ zone_id: string; quantity?: number; seat_ids?: string[] }>;
    },
): Promise<{ data: OrderView }> {
    return apiFetch(`/events/${eventId}/orders`, { method: 'POST', body: payload });
}

export function getOrder(orderId: string): Promise<{ data: OrderView }> {
    return apiFetch(`/orders/${orderId}`);
}

export function requestPayment(orderId: string): Promise<{ data: OrderView }> {
    return apiFetch(`/orders/${orderId}/payment`, { method: 'POST' });
}

export function googleWalletLink(ticketId: string): Promise<{ save_url: string }> {
    return apiFetch(`/tickets/${ticketId}/wallet/google`);
}

export interface TicketView {
    id: string;
    status: string;
    zone_name: string | null;
    seat_label: string | null;
    qr_token: string;
    issued_at: string | null;
}

export function getOrderTickets(orderId: string): Promise<{ data: TicketView[] }> {
    return apiFetch(`/orders/${orderId}/tickets`);
}

export function getTicket(ticketId: string): Promise<{ data: TicketView }> {
    return apiFetch(`/tickets/${ticketId}`);
}
