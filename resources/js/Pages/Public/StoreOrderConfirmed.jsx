import { useLayoutSettings } from "@/context/LayoutContext";
import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import { publicPrimaryCta } from '@/config/publicNavigation';
import cartStorageService from '@/services/cartStorageService';
import storeOrderService from '@/services/storeOrderService';

export default function StoreOrderConfirmed() {
    const settings = useLayoutSettings();
    const orderNumber = useMemo(
        () => new URLSearchParams(window.location.search).get('order'),
        [],
    );
    const [orderData, setOrderData] = useState(null);
    const [loading, setLoading] = useState(Boolean(orderNumber));

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
        if (!orderNumber) {
            setLoading(false);
            return;
        }

        let active = true;

        storeOrderService
            .getOrder(orderNumber)
            .then((response) => {
                if (!active) {
                    return;
                }

                setOrderData(response.data?.data ?? response.data);
            })
            .catch(() => {
                if (!active) {
                    return;
                }

                setOrderData(null);
            })
            .finally(() => {
                if (!active) {
                    return;
                }

                setLoading(false);
            });

        return () => {
            active = false;
        };
    }, [orderNumber]);

    useEffect(() => {
        if (orderData?.status === 'paid') {
            cartStorageService.clearCart();
        }
    }, [orderData]);

    return (
        <>
            <Head title={settings.global_seo_title || 'Orden de Tienda | Veraguas United FC'} />
            <AppLayout
                navbarBrandName="VERAGUAS UNITED"
                navbarCtaLabel={publicPrimaryCta.label}
                navbarCtaHref={publicPrimaryCta.url}
                navbarCtaPending={publicPrimaryCta.pending}
                navbarCtaPendingLabel={publicPrimaryCta.pendingLabel}
                navbarVariant="solid"
                mainClassName="min-h-screen px-margin-mobile pb-24 pt-40 md:px-margin-desktop"
            >
                <div className="mx-auto max-w-5xl">
                    <section className="overflow-hidden rounded-3xl border border-white/10 bg-primary text-white shadow-panel">
                        <div className="bg-gradient-to-r from-primary via-primary to-accent/70 px-8 py-12">
                            <p className="text-xs font-bold uppercase tracking-[0.32em] text-white/65">
                                Orden de tienda
                            </p>
                            <h1 className="mt-4 font-display text-4xl font-bold uppercase md:text-6xl">
                                {statusTitle(orderData?.status, loading, orderNumber)}
                            </h1>
                            <p className="mt-4 max-w-2xl text-base leading-7 text-white/80">
                                {statusMessage(orderData?.status, loading, orderNumber)}
                            </p>
                        </div>
                    </section>

                    <section className="mt-10 grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px]">
                        <article className="rounded-2xl border border-slate-200 bg-white p-8 shadow-card">
                            <h2 className="font-display text-2xl font-bold uppercase text-primary">
                                Resumen de orden
                            </h2>

                            {loading ? (
                                <p className="mt-6 text-sm text-slate-500">Consultando estado de la orden...</p>
                            ) : orderData ? (
                                <>
                                    <div className="mt-6 grid gap-3 text-sm text-slate-600 md:grid-cols-2">
                                        <p><strong>Orden:</strong> {orderData.order_number}</p>
                                        <p><strong>Estado:</strong> {orderData.status}</p>
                                        <p><strong>Cliente:</strong> {orderData.customer_name}</p>
                                        <p><strong>Email:</strong> {orderData.customer_email}</p>
                                        <p><strong>Total:</strong> ${Number(orderData.total ?? 0).toFixed(2)} {orderData.currency}</p>
                                        <p><strong>Pago:</strong> {orderData.payment_status ?? 'sin sincronizar'}</p>
                                    </div>

                                    <div className="mt-8 space-y-4">
                                        {orderData.items?.map((item) => (
                                            <div
                                                key={item.id}
                                                className="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-4 py-4"
                                            >
                                                <div>
                                                    <p className="font-semibold uppercase text-text-main">{item.product_name}</p>
                                                    <p className="text-xs uppercase tracking-[0.2em] text-slate-400">
                                                        x{item.quantity} · {item.product_sku || 'SIN SKU'}
                                                    </p>
                                                </div>
                                                <span className="font-display text-xl font-bold text-primary">
                                                    ${Number(item.line_total ?? 0).toFixed(2)}
                                                </span>
                                            </div>
                                        ))}
                                    </div>
                                </>
                            ) : (
                                <p className="mt-6 text-sm text-slate-500">
                                    No encontramos la orden solicitada. Si vienes de PayPal, confirma que el parametro <code>order</code> este presente.
                                </p>
                            )}
                        </article>

                        <aside className="rounded-2xl border border-slate-200 bg-white p-8 shadow-card">
                            <h2 className="font-display text-2xl font-bold uppercase text-primary">
                                Próximos pasos
                            </h2>
                            <ul className="mt-6 space-y-4 text-sm leading-6 text-slate-600">
                                <li>Si el estado está en <strong>pending_payment</strong>, PayPal aún no ha confirmado la captura del pago.</li>
                                <li>Si el estado está en <strong>paid</strong>, la orden ya fue sincronizada correctamente por el webhook de PayPal.</li>
                                <li>Envíos y fulfillment reales se activarán en una fase operativa del club.</li>
                            </ul>

                            <div className="mt-8 flex flex-col gap-3">
                                <a
                                    href="/tienda"
                                    className="inline-flex items-center justify-center rounded-md bg-accent px-6 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white transition hover:bg-primary"
                                >
                                    Volver a tienda
                                </a>
                                <a
                                    href="/carrito"
                                    className="inline-flex items-center justify-center rounded-md border-2 border-primary px-6 py-3 text-sm font-bold uppercase tracking-[0.2em] text-primary transition hover:bg-primary hover:text-white"
                                >
                                    Revisar carrito
                                </a>
                            </div>
                        </aside>
                    </section>
                </div>
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

function statusTitle(status, loading, orderNumber) {
    if (loading) {
        return 'VERIFICANDO ORDEN';
    }

    return {
        paid: 'PAGO CONFIRMADO',
        pending_payment: 'PAGO EN PROCESO',
        failed: 'PAGO FALLIDO',
        cancelled: 'ORDEN CANCELADA',
    }[status] ?? (orderNumber ? `ORDEN ${orderNumber}` : 'ORDEN DE TIENDA');
}

function statusMessage(status, loading, orderNumber) {
    if (loading) {
        return 'Estamos consultando el estado de tu orden de tienda con la informacion mas reciente disponible.';
    }

    return {
        paid: `La orden ${orderNumber} ya fue pagada correctamente y queda lista para las siguientes fases operativas del club.`,
        pending_payment: `La orden ${orderNumber} fue creada y está pendiente de confirmación final del pago por PayPal.`,
        failed: `La orden ${orderNumber} no pudo completarse. Puedes volver al carrito e intentarlo nuevamente.`,
        cancelled: `La orden ${orderNumber} fue cancelada o revertida. No se realizó ningún cargo.`,
    }[status] ?? 'No se encontro una orden valida para mostrar.';
}
