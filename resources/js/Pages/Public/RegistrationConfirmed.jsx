import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import { publicPrimaryCta } from '@/config/publicNavigation';
import ConfirmationHero from '@/components/registration-confirmed/ConfirmationHero';
import DigitalMemberCard from '@/components/registration-confirmed/DigitalMemberCard';
import BenefitsSummary from '@/components/registration-confirmed/BenefitsSummary';
import NextSteps from '@/components/registration-confirmed/NextSteps';
import registrationConfirmationMock from '@/mocks/registrationConfirmationMock';
import membershipService from '@/services/membershipService';

export default function RegistrationConfirmed() {
    const [orderData, setOrderData] = useState(null);
    const [orderLoading, setOrderLoading] = useState(false);

    const orderNumber = useMemo(
        () => new URLSearchParams(window.location.search).get('order'),
        [],
    );

    useEffect(() => {
        let active = true;

        async function loadShell() {
            try {
                const [siteSettings, header, footer] = await Promise.all([
                ]);
                if (!active) return;
            } catch {
                if (!active) return;
            }
        }

        loadShell();
        return () => { active = false; };
    }, [defaultFooterLinks, defaultHeaderLinks]);

    useEffect(() => {
        if (!orderNumber) return;

        let active = true;
        setOrderLoading(true);

        membershipService
            .getOrder(orderNumber)
            .then((res) => { if (active) setOrderData(res.data); })
            .catch(() => { if (active) setOrderData(null); })
            .finally(() => { if (active) setOrderLoading(false); });

        return () => { active = false; };
    }, [orderNumber]);

    const pageTitle = useMemo(
        () => settings.global_seo_title || 'Registro Confirmado | Veraguas United FC',
        [settings.global_seo_title],
    );

    // Determine which content variant to render
    const status = orderData?.status ?? null;
    const isPaid      = status === 'paid';
    const isPending   = status === 'pending_payment';
    const isFailed    = status === 'failed';
    const isCancelled = status === 'cancelled';
    // Show full confirmation if: no order param (direct visit), order not found (neutral), or paid
    const showFullContent = !orderNumber || (!orderLoading && (isPaid || (!orderData && !orderLoading)));

    return (
        <>
            <Head title={pageTitle} />
            <AppLayout
                navbarBrandName="VERAGUAS UNITED"
                navbarCtaLabel={publicPrimaryCta.label}
                navbarCtaHref={publicPrimaryCta.url}
                navbarCtaPending={publicPrimaryCta.pending}
                navbarCtaPendingLabel={publicPrimaryCta.pendingLabel}
                navbarVariant="solid"
                mainClassName="min-h-screen px-margin-mobile pb-24 pt-40 md:px-margin-desktop"
            >
                <div className="mx-auto flex max-w-7xl flex-col items-center">

                    {/* Loading state */}
                    {orderNumber && orderLoading && (
                        <div className="mb-12 w-full max-w-2xl rounded-xl border border-outline bg-surface p-12 text-center">
                            <span className="material-symbols-outlined animate-spin text-4xl text-accent">
                                autorenew
                            </span>
                            <p className="mt-4 text-sm text-text-main/70">
                                Verificando estado de la orden {orderNumber}…
                            </p>
                        </div>
                    )}

                    {/* Status banner — shown for all non-loading states when order exists */}
                    {orderNumber && !orderLoading && orderData && (
                        <OrderStatusBanner
                            orderNumber={orderNumber}
                            orderData={orderData}
                        />
                    )}

                    {/* Full confirmation experience: paid or direct visit */}
                    {showFullContent && !orderLoading && (
                        <>
                            <ConfirmationHero hero={registrationConfirmationMock.hero} />
                            <DigitalMemberCard card={registrationConfirmationMock.memberCard} />
                            <BenefitsSummary benefits={registrationConfirmationMock.benefits} />
                            <NextSteps
                                steps={registrationConfirmationMock.nextSteps}
                                actions={registrationConfirmationMock.actions}
                            />
                        </>
                    )}

                    {/* Pending: show next steps without confirmation branding */}
                    {!orderLoading && isPending && (
                        <PendingBlock orderNumber={orderNumber} />
                    )}

                    {/* Failed / cancelled: show retry prompt */}
                    {!orderLoading && (isFailed || isCancelled) && (
                        <RetryBlock status={status} />
                    )}
                </div>
            </AppLayout>
        </>
    );
}

function OrderStatusBanner({ orderNumber, orderData }) {
    const statusConfig = {
        paid: {
            icon: 'check_circle',
            iconClass: 'text-green-500',
            bg: 'bg-green-50 border-green-200',
            title: '¡Pago confirmado!',
            message: `Tu membresía La Tribu está activa. Orden: ${orderNumber}`,
        },
        pending_payment: {
            icon: 'schedule',
            iconClass: 'text-accent',
            bg: 'bg-accent/10 border-accent/30',
            title: 'Pago en proceso',
            message: `La orden ${orderNumber} está pendiente de confirmación de pago. Si ya pagaste en PayPal, el proceso puede tardar unos minutos.`,
        },
        failed: {
            icon: 'cancel',
            iconClass: 'text-red-500',
            bg: 'bg-red-50 border-red-200',
            title: 'Pago no completado',
            message: `La orden ${orderNumber} no pudo procesarse. Puedes intentarlo nuevamente desde la página de registro.`,
        },
        cancelled: {
            icon: 'do_not_disturb_on',
            iconClass: 'text-slate-400',
            bg: 'bg-slate-50 border-slate-200',
            title: 'Orden cancelada',
            message: `La orden ${orderNumber} fue cancelada. No se realizó ningún cargo.`,
        },
    };

    const config = statusConfig[orderData.status] ?? statusConfig.pending_payment;

    return (
        <div className={`mb-12 w-full max-w-2xl rounded-xl border p-8 ${config.bg}`}>
            <div className="flex items-start gap-4">
                <span className={`material-symbols-outlined text-3xl ${config.iconClass}`}
                    style={{ fontVariationSettings: "'FILL' 1" }}>
                    {config.icon}
                </span>
                <div>
                    <p className="font-display text-xl font-bold uppercase text-primary">
                        {config.title}
                    </p>
                    <p className="mt-2 text-sm leading-relaxed text-text-main/80">
                        {config.message}
                    </p>
                    {orderData.expires_at ? (
                        <p className="mt-2 text-xs text-text-main/50">
                            Válida hasta: {new Date(orderData.expires_at).toLocaleDateString('es-PA')}
                        </p>
                    ) : null}
                </div>
            </div>
        </div>
    );
}

function PendingBlock({ orderNumber }) {
    return (
        <div className="w-full max-w-2xl space-y-8 text-center">
            <div className="rounded-xl border border-blue-100 bg-blue-50 p-8">
                <p className="font-display text-xl font-bold uppercase text-primary">
                    ¿Qué pasa ahora?
                </p>
                <ul className="mt-6 space-y-3 text-left text-sm leading-6 text-slate-600">
                    <li className="flex items-start gap-3">
                        <span className="material-symbols-outlined mt-0.5 text-base text-accent">payments</span>
                        PayPal confirmará la captura del pago automáticamente.
                    </li>
                    <li className="flex items-start gap-3">
                        <span className="material-symbols-outlined mt-0.5 text-base text-accent">bolt</span>
                        Una vez confirmado, tu membresía quedará activada de inmediato.
                    </li>
                    <li className="flex items-start gap-3">
                        <span className="material-symbols-outlined mt-0.5 text-base text-accent">bookmark</span>
                        Guarda el número de orden <strong>{orderNumber}</strong> como referencia.
                    </li>
                </ul>
            </div>
            <a
                href="/fanclub"
                className="inline-flex items-center gap-2 text-sm font-bold uppercase tracking-[0.2em] text-primary underline underline-offset-4 hover:text-accent"
            >
                Volver al FanClub
            </a>
        </div>
    );
}

function RetryBlock({ status }) {
    return (
        <div className="w-full max-w-2xl text-center">
            <div className="rounded-xl border border-red-100 bg-red-50 p-8">
                <p className="font-display text-xl font-bold uppercase text-primary">
                    {status === 'failed' ? 'No se completó el pago' : 'Registro cancelado'}
                </p>
                <p className="mt-4 text-sm leading-relaxed text-slate-600">
                    {status === 'failed'
                        ? 'El pago no pudo procesarse. Puedes volver al formulario e intentar nuevamente cuando quieras.'
                        : 'Cancelaste el proceso de pago. No se realizó ningún cargo. Puedes volver a registrarte cuando quieras.'}
                </p>
                <a
                    href="/registro-tribu"
                    className="mt-6 inline-flex items-center gap-2 rounded-md bg-primary px-6 py-3 text-sm font-bold uppercase tracking-[0.2em] text-white transition hover:bg-accent"
                >
                    <span className="material-symbols-outlined text-base">arrow_back</span>
                    Intentar nuevamente
                </a>
            </div>
        </div>
    );
}

function toMenuLinks(items = [], fallback = []) {
    if (!items.length) return fallback;
    return items.map((item) => ({
        ...item,
        children: toMenuLinks(item.children ?? [], []),
    }));
}
