import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import ticketingService from '@/services/ticketingService';
import homeMock from '@/mocks/homeMock';
import { fetchSiteSettings } from '@/services/siteService';
import { fetchMenu } from '@/services/menuService';
import {
    buildPublicFooterLinks,
    buildPublicHeaderLinks,
    publicLegalLinks,
    publicPrimaryCta,
} from '@/config/publicNavigation';

const fallbackSettings = {
    site_name: 'Veraguas United FC',
    site_tagline: 'Orgullo de Veraguas',
    primary_logo_url: null,
    secondary_logo_url: null,
    primary_color: '#1D428A',
    accent_color: '#5BC2E7',
    contact_email: 'hola@veraguasunited.test',
    contact_phone: '+507 6000-0000',
    social_links: {
        instagram: 'https://instagram.com/veraguasunited',
        facebook: 'https://facebook.com/veraguasunited',
    },
    global_seo_title: 'Confirmación de Boletos | Veraguas United FC',
    global_seo_description: 'Estado de la orden de boletos del Veraguas United FC.',
    maintenance_mode: false,
};

export default function TicketOrderConfirmed() {
    const defaultHeaderLinks = useMemo(() => buildPublicHeaderLinks('/boletos'), []);
    const defaultFooterLinks = useMemo(() => buildPublicFooterLinks(), []);
    const orderNumber = useMemo(
        () => new URLSearchParams(window.location.search).get('order'),
        [],
    );

    const [settings, setSettings]         = useState(fallbackSettings);
    const [headerMenu, setHeaderMenu]     = useState(defaultHeaderLinks);
    const [footerMenu, setFooterMenu]     = useState(defaultFooterLinks);
    const [orderData, setOrderData]       = useState(null);
    const [loading, setLoading]           = useState(Boolean(orderNumber));
    const [ticketsData, setTicketsData]   = useState(null);
    const [ticketsLoading, setTicketsLoading] = useState(false);

    // Shell: settings + menus
    useEffect(() => {
        let active = true;

        async function loadShell() {
            try {
                const [siteSettings, header, footer] = await Promise.all([
                    fetchSiteSettings(),
                    fetchMenu('header'),
                    fetchMenu('footer'),
                ]);

                if (!active) return;

                setSettings(siteSettings ?? fallbackSettings);
                setHeaderMenu(toMenuLinks(header?.items ?? [], defaultHeaderLinks, '/boletos'));
                setFooterMenu(toMenuLinks(footer?.items ?? [], defaultFooterLinks));
            } catch {
                if (!active) return;
                setSettings(fallbackSettings);
                setHeaderMenu(defaultHeaderLinks);
                setFooterMenu(defaultFooterLinks);
            }
        }

        loadShell();
        return () => { active = false; };
    }, [defaultFooterLinks, defaultHeaderLinks]);

    // Order data
    useEffect(() => {
        if (!orderNumber) {
            setLoading(false);
            return;
        }

        let active = true;

        ticketingService
            .getOrder(orderNumber)
            .then((response) => {
                if (!active) return;
                setOrderData(response.data?.data ?? response.data);
            })
            .catch(() => {
                if (!active) return;
                setOrderData(null);
            })
            .finally(() => {
                if (!active) return;
                setLoading(false);
            });

        return () => { active = false; };
    }, [orderNumber]);

    // Tickets — only fetch when order is paid
    useEffect(() => {
        if (!orderNumber || orderData?.status !== 'paid') {
            setTicketsLoading(false);
            return;
        }

        let active = true;
        setTicketsLoading(true);

        ticketingService
            .getOrderTickets(orderNumber)
            .then((response) => {
                if (!active) return;
                setTicketsData(response.data);
            })
            .catch(() => {
                if (!active) return;
                setTicketsData(null);
            })
            .finally(() => {
                if (!active) return;
                setTicketsLoading(false);
            });

        return () => { active = false; };
    }, [orderNumber, orderData?.status]);

    const isPaid      = orderData?.status === 'paid';
    const isPending   = orderData?.status === 'pending_payment';
    const isFailed    = orderData?.status === 'failed';
    const isCancelled = orderData?.status === 'cancelled';

    return (
        <>
            <Head title={settings.global_seo_title || 'Confirmación de Boletos | Veraguas United FC'} />
            <AppLayout
                settings={settings}
                headerMenu={headerMenu}
                footerMenu={footerMenu}
                legalMenu={publicLegalLinks}
                ticker={homeMock.ticker}
                tickerClubLabel="VERAGUAS UNITED FC"
                navbarBrandName="VERAGUAS UNITED"
                navbarCtaLabel={publicPrimaryCta.label}
                navbarCtaHref={publicPrimaryCta.url}
                navbarCtaPending={publicPrimaryCta.pending}
                navbarCtaPendingLabel={publicPrimaryCta.pendingLabel}
                navbarVariant="solid"
                mainClassName="min-h-screen px-margin-mobile pb-24 pt-40 md:px-margin-desktop"
            >
                <div className="mx-auto max-w-5xl">

                    {/* Hero status banner */}
                    <section className="overflow-hidden rounded-3xl border border-white/10 bg-primary text-white shadow-panel">
                        <div className="bg-gradient-to-r from-primary via-primary to-accent/70 px-8 py-12">
                            <p className="text-xs font-bold uppercase tracking-[0.32em] text-white/65">
                                Orden de Boletos
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

                        {/* Main content column */}
                        <div className="space-y-8">

                            {/* Order summary */}
                            <article className="rounded-2xl border border-slate-200 bg-white p-8 shadow-card">
                                <h2 className="font-display text-2xl font-bold uppercase text-primary">
                                    Resumen de la Orden
                                </h2>

                                {loading ? (
                                    <p className="mt-6 text-sm text-slate-500">
                                        Consultando estado de los boletos...
                                    </p>
                                ) : orderData ? (
                                    <>
                                        <div className="mt-6 grid gap-4 text-sm text-slate-600 md:grid-cols-2">
                                            <p><strong>Orden:</strong> {orderData.order_number}</p>
                                            <p>
                                                <strong>Estado:</strong>{' '}
                                                <StatusBadge status={orderData.status} />
                                            </p>
                                            <p><strong>Cliente:</strong> {orderData.customer_name || 'N/A'}</p>
                                            <p><strong>Email:</strong> {orderData.customer_email}</p>
                                            <p>
                                                <strong>Total:</strong>{' '}
                                                ${Number(orderData.total ?? 0).toFixed(2)} {orderData.currency}
                                            </p>
                                            {orderData.match && (
                                                <p>
                                                    <strong>Partido:</strong>{' '}
                                                    {orderData.match.home_team} vs {orderData.match.away_team}
                                                </p>
                                            )}
                                        </div>

                                        <div className="mt-8 space-y-3">
                                            {orderData.items?.map((item, i) => (
                                                <div
                                                    key={i}
                                                    className="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-4 py-4"
                                                >
                                                    <div>
                                                        <p className="font-semibold uppercase text-text-main">
                                                            Entrada — {item.zone_name}
                                                        </p>
                                                        <p className="text-xs uppercase tracking-[0.2em] text-slate-400">
                                                            Cantidad: x{item.quantity} · Precio: ${Number(item.unit_price ?? 0).toFixed(2)}
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
                                        No encontramos la orden solicitada. Si vienes de PayPal,
                                        confirma que el parámetro <code>order</code> esté presente en la URL.
                                    </p>
                                )}
                            </article>

                            {/* Tickets section — only when paid */}
                            {!loading && isPaid && (
                                <article className="rounded-2xl border border-slate-200 bg-white p-8 shadow-card">
                                    <h2 className="font-display text-2xl font-bold uppercase text-primary">
                                        Boletos Digitales
                                    </h2>

                                    {ticketsLoading ? (
                                        <p className="mt-6 text-sm text-slate-500">
                                            Cargando boletos digitales...
                                        </p>
                                    ) : ticketsData?.tickets?.length > 0 ? (
                                        <>
                                            <p className="mt-2 text-sm text-slate-500">
                                                Presenta el código de cada boleto al personal del estadio en la entrada.
                                            </p>
                                            <div className="mt-6 grid gap-4 sm:grid-cols-2">
                                                {ticketsData.tickets.map((ticket) => (
                                                    <TicketCard key={ticket.id} ticket={ticket} />
                                                ))}
                                            </div>
                                        </>
                                    ) : (
                                        <div className="mt-6 rounded-xl border border-amber-100 bg-amber-50 p-5">
                                            <p className="text-sm font-semibold text-amber-800">
                                                Emisión en proceso
                                            </p>
                                            <p className="mt-1 text-sm text-amber-700">
                                                Tu pago fue confirmado. Los boletos digitales se están
                                                generando y estarán disponibles en unos instantes.
                                                Recarga la página si no aparecen pronto.
                                            </p>
                                        </div>
                                    )}
                                </article>
                            )}

                            {/* Pending message */}
                            {!loading && isPending && (
                                <div className="rounded-2xl border border-blue-100 bg-blue-50 p-6">
                                    <p className="text-sm font-semibold text-blue-800">
                                        Esperando confirmación de PayPal
                                    </p>
                                    <p className="mt-1 text-sm text-blue-700">
                                        Los boletos digitales se emiten automáticamente en cuanto
                                        PayPal confirme la captura del pago. Este proceso puede
                                        tomar unos minutos. No es necesario hacer nada más.
                                    </p>
                                </div>
                            )}

                            {/* Failed / cancelled message */}
                            {!loading && (isFailed || isCancelled) && (
                                <div className="rounded-2xl border border-red-100 bg-red-50 p-6">
                                    <p className="text-sm font-semibold text-red-800">
                                        {isFailed ? 'Pago no procesado' : 'Orden cancelada'}
                                    </p>
                                    <p className="mt-1 text-sm text-red-700">
                                        No se generaron boletos digitales para esta orden.
                                        {isFailed && ' Puedes volver a boletos e intentar nuevamente.'}
                                    </p>
                                </div>
                            )}
                        </div>

                        {/* Sidebar */}
                        <aside className="rounded-2xl border border-slate-200 bg-white p-8 shadow-card">
                            <h2 className="font-display text-2xl font-bold uppercase text-primary">
                                Información
                            </h2>
                            <ul className="mt-6 space-y-4 text-sm leading-6 text-slate-600">
                                {isPaid ? (
                                    <>
                                        <li>
                                            <span className="font-semibold text-green-700">Pago confirmado.</span>{' '}
                                            Tus boletos digitales están disponibles arriba.
                                        </li>
                                        <li>
                                            Presenta el código en la entrada del estadio. El personal
                                            lo escaneará para validar tu ingreso.
                                        </li>
                                        <li>
                                            Cada código es de uso único. Una vez escaneado,
                                            el boleto queda marcado como utilizado.
                                        </li>
                                    </>
                                ) : isPending ? (
                                    <>
                                        <li>
                                            Tu pago está en proceso de confirmación por PayPal.
                                        </li>
                                        <li>
                                            Los boletos se emiten automáticamente cuando el pago
                                            sea capturado. No se requiere ninguna acción de tu parte.
                                        </li>
                                        <li>
                                            Guarda el número de orden como referencia.
                                        </li>
                                    </>
                                ) : (
                                    <li>
                                        {isFailed
                                            ? 'El pago no pudo completarse. Inténtalo de nuevo desde la página de boletos.'
                                            : 'Esta orden fue cancelada. No se realizó ningún cargo.'}
                                    </li>
                                )}
                            </ul>

                            <div className="mt-8 flex flex-col gap-3">
                                <a
                                    href="/boletos"
                                    className="inline-flex items-center justify-center rounded-md bg-accent px-6 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white transition hover:bg-primary"
                                >
                                    Ver Boletos
                                </a>
                                {orderData?.match && (
                                    <a
                                        href="/calendario"
                                        className="inline-flex items-center justify-center rounded-md border-2 border-primary px-6 py-3 text-sm font-bold uppercase tracking-[0.2em] text-primary transition hover:bg-primary hover:text-white"
                                    >
                                        Calendario
                                    </a>
                                )}
                            </div>
                        </aside>
                    </section>
                </div>
            </AppLayout>
        </>
    );
}

function TicketCard({ ticket }) {
    const isUsed   = ticket.status === 'used';
    const isVoided = ticket.status === 'voided';
    const isActive = ticket.status === 'issued';

    return (
        <div
            className={[
                'relative overflow-hidden rounded-xl border p-5 transition',
                isActive  ? 'border-green-200 bg-green-50'  : '',
                isUsed    ? 'border-slate-200 bg-slate-50 opacity-60' : '',
                isVoided  ? 'border-red-100 bg-red-50 opacity-60'    : '',
            ].join(' ')}
        >
            {/* Status strip */}
            <div className="mb-4 flex items-center justify-between">
                <p className="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">
                    {ticket.seat_label}
                </p>
                <span
                    className={[
                        'rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider',
                        isActive  ? 'bg-green-600 text-white'  : '',
                        isUsed    ? 'bg-slate-500 text-white'  : '',
                        isVoided  ? 'bg-red-600 text-white'    : '',
                    ].join(' ')}
                >
                    {isActive ? 'Válido' : isUsed ? 'Utilizado' : 'Anulado'}
                </span>
            </div>

            {/* QR placeholder */}
            <div className="flex justify-center">
                <QrPlaceholder token={ticket.token} active={isActive} />
            </div>

            {/* Zone */}
            <p className="mt-4 text-center text-xs font-semibold uppercase tracking-[0.15em] text-slate-600">
                {ticket.zone_name}
            </p>
        </div>
    );
}

function QrPlaceholder({ token, active }) {
    const half = Math.ceil(token.length / 2);
    const line1 = token.slice(0, half);
    const line2 = token.slice(half);

    const borderColor = active ? '#166534' : '#94a3b8';
    const bgColor     = active ? '#14532d' : '#334155';
    const textColor   = active ? '#bbf7d0' : '#94a3b8';

    return (
        <div
            style={{
                width: 152,
                height: 152,
                background: bgColor,
                borderRadius: 12,
                border: `2px solid ${borderColor}`,
                position: 'relative',
                display: 'flex',
                flexDirection: 'column',
                alignItems: 'center',
                justifyContent: 'center',
                padding: '12px',
                boxSizing: 'border-box',
            }}
        >
            {/* Corner marks */}
            {[
                { top: 8, left: 8 },
                { top: 8, right: 8 },
                { bottom: 8, left: 8 },
                { bottom: 8, right: 8 },
            ].map((pos, i) => (
                <div
                    key={i}
                    style={{
                        position: 'absolute',
                        width: 16,
                        height: 16,
                        borderColor: active ? '#4ade80' : '#64748b',
                        borderStyle: 'solid',
                        borderTopWidth:    pos.top    !== undefined ? 3 : 0,
                        borderBottomWidth: pos.bottom !== undefined ? 3 : 0,
                        borderLeftWidth:   pos.left   !== undefined ? 3 : 0,
                        borderRightWidth:  pos.right  !== undefined ? 3 : 0,
                        borderRadius: pos.top !== undefined && pos.left !== undefined ? '4px 0 0 0'
                            : pos.top !== undefined ? '0 4px 0 0'
                            : pos.left !== undefined ? '0 0 0 4px'
                            : '0 0 4px 0',
                        ...pos,
                    }}
                />
            ))}

            {/* Token text */}
            <p
                style={{
                    fontFamily: 'monospace',
                    fontSize: 9,
                    color: textColor,
                    textAlign: 'center',
                    lineHeight: 1.6,
                    wordBreak: 'break-all',
                    margin: 0,
                }}
            >
                {line1}
                <br />
                {line2}
            </p>

            {/* Label */}
            <p
                style={{
                    position: 'absolute',
                    bottom: 6,
                    fontSize: 8,
                    fontWeight: 700,
                    letterSpacing: '0.15em',
                    color: active ? '#4ade80' : '#64748b',
                    textTransform: 'uppercase',
                    margin: 0,
                }}
            >
                Código de Entrada
            </p>
        </div>
    );
}

function StatusBadge({ status }) {
    const config = {
        paid:            { label: 'Pagado',            cls: 'bg-green-100 text-green-800' },
        pending_payment: { label: 'Pendiente de pago', cls: 'bg-amber-100 text-amber-800' },
        failed:          { label: 'Fallido',           cls: 'bg-red-100 text-red-800'     },
        cancelled:       { label: 'Cancelado',         cls: 'bg-slate-100 text-slate-600' },
    }[status] ?? { label: status, cls: 'bg-slate-100 text-slate-600' };

    return (
        <span className={`inline-block rounded-full px-2 py-0.5 text-xs font-bold uppercase tracking-wider ${config.cls}`}>
            {config.label}
        </span>
    );
}

function toMenuLinks(items = [], fallback = [], activeUrl = '') {
    if (!items.length) return fallback;

    return items.map((item) => ({
        ...item,
        active: item.url ? item.url === activeUrl : false,
        children: toMenuLinks(item.children ?? [], [], activeUrl),
    }));
}

function statusTitle(status, loading, orderNumber) {
    if (loading) return 'VERIFICANDO ORDEN';

    return {
        paid:            'PAGO CONFIRMADO',
        pending_payment: 'PAGO EN PROCESO',
        failed:          'PAGO FALLIDO',
        cancelled:       'ORDEN CANCELADA',
    }[status] ?? (orderNumber ? `ORDEN ${orderNumber}` : 'ORDEN DE BOLETOS');
}

function statusMessage(status, loading, orderNumber) {
    if (loading) {
        return 'Estamos consultando el estado de tu orden con la información más reciente disponible.';
    }

    return {
        paid: `La orden ${orderNumber} fue pagada. Tus boletos digitales están listos abajo.`,
        pending_payment: `La orden ${orderNumber} está pendiente de confirmación final del pago. Los boletos se emiten automáticamente cuando PayPal confirme la captura.`,
        failed: `La orden ${orderNumber} no pudo completarse. Puedes volver a boletos e intentar nuevamente.`,
        cancelled: `La orden ${orderNumber} fue cancelada o revertida. No se generó ningún boleto digital.`,
    }[status] ?? 'No se encontró una orden válida para mostrar.';
}
