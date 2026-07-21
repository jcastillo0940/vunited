import { createContext, useContext, useMemo, useState, type ReactNode } from 'react';

interface OrderFlowState {
    eventId: string | null;
    zoneId: string | null;
    quantity: number;
    orderId: string | null;
}

interface OrderFlowContextValue extends OrderFlowState {
    setSelection: (eventId: string, zoneId: string) => void;
    setQuantity: (quantity: number) => void;
    setOrderId: (orderId: string) => void;
    reset: () => void;
}

const STORAGE_KEY = 'veraguas-ticketing-order-flow';

function loadInitial(): OrderFlowState {
    try {
        const raw = sessionStorage.getItem(STORAGE_KEY);
        if (raw) return JSON.parse(raw) as OrderFlowState;
    } catch {
        // sessionStorage no disponible o corrupto: empezamos de cero.
    }
    return { eventId: null, zoneId: null, quantity: 1, orderId: null };
}

const OrderFlowContext = createContext<OrderFlowContextValue | null>(null);

export function OrderFlowProvider({ children }: { children: ReactNode }) {
    const [state, setState] = useState<OrderFlowState>(loadInitial);

    function persist(next: OrderFlowState) {
        setState(next);
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify(next));
    }

    const value = useMemo<OrderFlowContextValue>(
        () => ({
            ...state,
            setSelection: (eventId, zoneId) => persist({ ...state, eventId, zoneId }),
            setQuantity: (quantity) => persist({ ...state, quantity }),
            setOrderId: (orderId) => persist({ ...state, orderId }),
            reset: () => persist({ eventId: null, zoneId: null, quantity: 1, orderId: null }),
        }),
        [state],
    );

    return <OrderFlowContext.Provider value={value}>{children}</OrderFlowContext.Provider>;
}

export function useOrderFlow(): OrderFlowContextValue {
    const ctx = useContext(OrderFlowContext);
    if (!ctx) throw new Error('useOrderFlow debe usarse dentro de <OrderFlowProvider>');

    return ctx;
}
