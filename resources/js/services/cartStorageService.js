const STORAGE_KEY = 'veraguas-united-store-cart';

function canUseStorage() {
    return typeof window !== 'undefined' && typeof window.localStorage !== 'undefined';
}

function loadCart() {
    if (!canUseStorage()) {
        return [];
    }

    try {
        const raw = window.localStorage.getItem(STORAGE_KEY);
        const parsed = raw ? JSON.parse(raw) : [];

        return Array.isArray(parsed)
            ? parsed
                .map(normalizeItem)
                .filter(Boolean)
            : [];
    } catch {
        return [];
    }
}

function saveCart(items) {
    if (!canUseStorage()) {
        return;
    }

    window.localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
}

function clearCart() {
    if (!canUseStorage()) {
        return;
    }

    window.localStorage.removeItem(STORAGE_KEY);
}

export default {
    loadCart,
    saveCart,
    clearCart,
};

function normalizeItem(item) {
    if (!item || typeof item !== 'object') {
        return null;
    }

    const numericPrice = typeof item.price === 'number'
        ? item.price
        : Number.parseFloat(String(item.price ?? '0').replace(/[^0-9.-]+/g, ''));

    return {
        ...item,
        id: item.id ?? item.productId ?? null,
        productId: item.productId ?? item.id ?? null,
        quantity: Math.max(Number.parseInt(item.quantity ?? 1, 10) || 1, 1),
        price: Number.isNaN(numericPrice) ? 0 : numericPrice,
        priceLabel: item.priceLabel ?? (Number.isNaN(numericPrice) ? '$0.00' : `$${numericPrice.toFixed(2)}`),
    };
}
