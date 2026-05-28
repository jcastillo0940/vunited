import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import { publicPrimaryCta } from '@/config/publicNavigation';
import CartHero from '@/components/cart/CartHero';
import CartItemList from '@/components/cart/CartItemList';
import CartSummary from '@/components/cart/CartSummary';
import CartEmptyState from '@/components/cart/CartEmptyState';
import cartMock from '@/mocks/cartMock';
import cartStorageService from '@/services/cartStorageService';
import storeOrderService from '@/services/storeOrderService';

export default function Cart() {
    const [items, setItems] = useState(() => cartStorageService.loadCart());
    const [couponCode, setCouponCode] = useState('');
    const [appliedDiscount, setAppliedDiscount] = useState(0);
    const [couponMessage, setCouponMessage] = useState('');
    const [customerName, setCustomerName] = useState('');
    const [customerEmail, setCustomerEmail] = useState('');
    const [customerPhone, setCustomerPhone] = useState('');
    const [acceptTerms, setAcceptTerms] = useState(false);
    const [checkoutLoading, setCheckoutLoading] = useState(false);
    const [checkoutError, setCheckoutError] = useState('');
    const [cancelledOrder, setCancelledOrder] = useState('');

    useEffect(() => {
        let active = true;

        async function loadShell() {
            try {
                const [siteSettings, header, footer] = await Promise.all([
                ]);

                if (!active) {
                    return;
                }

            } catch {
                if (!active) {
                    return;
                }

            }
        }

        loadShell();

        return () => {
            active = false;
        };
    }, [defaultFooterLinks, defaultHeaderLinks]);

    useEffect(() => {
        cartStorageService.saveCart(items);
    }, [items]);

    useEffect(() => {
        const searchParams = new URLSearchParams(window.location.search);

        if (searchParams.get('cancelled') === '1') {
            setCancelledOrder(searchParams.get('order') ?? '');
        }
    }, []);

    const pageTitle = useMemo(
        () => settings.global_seo_title || 'Carrito de Compras | Veraguas United FC',
        [settings.global_seo_title],
    );

    const subtotal = useMemo(
        () => items.reduce((sum, item) => sum + item.price * item.quantity, 0),
        [items],
    );
    const shipping = items.length ? cartMock.shipping : 0;
    const discount = items.length ? appliedDiscount : 0;
    const total = Math.max(subtotal + shipping - discount, 0);

    function handleIncrease(itemId) {
        setItems((current) =>
            current.map((item) =>
                item.id === itemId ? { ...item, quantity: item.quantity + 1 } : item,
            ),
        );
    }

    function handleDecrease(itemId) {
        setItems((current) =>
            current
                .map((item) =>
                    item.id === itemId
                        ? { ...item, quantity: Math.max(item.quantity - 1, 1) }
                        : item,
                )
                .filter(Boolean),
        );
    }

    function handleRemove(itemId) {
        setItems((current) => current.filter((item) => item.id !== itemId));
    }

    function handleApplyCoupon() {
        if (couponCode.trim().toUpperCase() === cartMock.validCoupon.code) {
            setAppliedDiscount(cartMock.validCoupon.amount);
            setCouponMessage(`Codigo visual aplicado: -$${cartMock.validCoupon.amount.toFixed(2)} (sin efecto real)`);
            return;
        }

        setAppliedDiscount(0);
        setCouponMessage('Codigo visual no valido');
    }

    async function handleCheckout() {
        if (!items.length || checkoutLoading) {
            return;
        }

        setCheckoutLoading(true);
        setCheckoutError('');

        try {
            const response = await storeOrderService.createOrder({
                customer_name: customerName,
                customer_email: customerEmail,
                customer_phone: customerPhone || null,
                items: items.map((item) => ({
                    product_id: item.productId ?? item.id,
                    quantity: item.quantity,
                })),
                accept_terms: acceptTerms,
                coupon_code: couponCode || null,
            });

            const approveUrl = response.data?.approve_url;

            if (!approveUrl) {
                throw new Error('No se recibio approve_url de PayPal.');
            }

            window.location.assign(approveUrl);
        } catch (error) {
            setCheckoutError(
                error?.response?.data?.message
                ?? 'No se pudo crear la orden de tienda. Intenta nuevamente.',
            );
            setCheckoutLoading(false);
        }
    }

    return (
        <>
            <Head title={pageTitle} />
            <AppLayout
                navbarBrandName="VERAGUAS UNITED"
                navbarCtaLabel={publicPrimaryCta.label}
                navbarCtaHref={publicPrimaryCta.url}
                navbarCtaPending={publicPrimaryCta.pending}
                navbarCtaPendingLabel={publicPrimaryCta.pendingLabel}
                navbarVariant="light"
                mainClassName="pt-0"
            >
                <CartHero hero={cartMock.hero} />

                <section className="pb-24 pt-12">
                    <div className="mx-auto max-w-7xl px-margin-mobile md:px-margin-desktop">
                        {cancelledOrder ? (
                            <div className="mb-8 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                                El pago de PayPal fue cancelado para la orden {cancelledOrder}. Puedes revisar el carrito y reintentar cuando quieras.
                            </div>
                        ) : null}

                        <div className="grid grid-cols-1 gap-12 lg:grid-cols-12">
                            <div className="lg:col-span-8">
                                {items.length ? (
                                    <CartItemList
                                        items={items}
                                        onIncrease={handleIncrease}
                                        onDecrease={handleDecrease}
                                        onRemove={handleRemove}
                                    />
                                ) : (
                                    <CartEmptyState />
                                )}
                            </div>

                            <div className="lg:col-span-4">
                                <CartSummary
                                    subtotal={subtotal}
                                    shipping={shipping}
                                    discount={discount}
                                    total={total}
                                    couponCode={couponCode}
                                    onCouponCodeChange={setCouponCode}
                                    onApplyCoupon={handleApplyCoupon}
                                    couponMessage={couponMessage}
                                    securityNotice={cartMock.securityNotice}
                                    customerName={customerName}
                                    onCustomerNameChange={setCustomerName}
                                    customerEmail={customerEmail}
                                    onCustomerEmailChange={setCustomerEmail}
                                    customerPhone={customerPhone}
                                    onCustomerPhoneChange={setCustomerPhone}
                                    acceptTerms={acceptTerms}
                                    onAcceptTermsChange={setAcceptTerms}
                                    onCheckout={handleCheckout}
                                    checkoutLoading={checkoutLoading}
                                    checkoutError={checkoutError}
                                    checkoutDisabled={!items.length}
                                />
                            </div>
                        </div>
                    </div>
                </section>
            </AppLayout>
        </>
    );
}

function toMenuLinks(items = [], fallback = [], activeUrl = '') {
    if (!items.length) {
        return fallback;
    }

    return items.map((item) => ({
        ...item,
        active: item.url ? item.url === activeUrl : false,
        children: toMenuLinks(item.children ?? [], [], activeUrl),
    }));
}
