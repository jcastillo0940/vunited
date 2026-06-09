const defaultHeaderLinks = [];
const defaultFooterLinks = [];
import { useLayoutSettings } from "@/context/LayoutContext";
import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import { publicPrimaryCta } from '@/config/publicNavigation';
import TicketMatchHeader from '@/components/tickets/TicketMatchHeader';
import TicketZoneSelector from '@/components/tickets/TicketZoneSelector';
import TicketQuantitySelector from '@/components/tickets/TicketQuantitySelector';
import TicketCheckoutSummary from '@/components/tickets/TicketCheckoutSummary';
import TicketSuccessMock from '@/components/tickets/TicketSuccessMock';
import ticketsMock from '@/mocks/ticketsMock';
import ticketingService from '@/services/ticketingService';

export default function Tickets() {
    const settings = useLayoutSettings();
    const [selectedZoneId, setSelectedZoneId] = useState(ticketsMock.zones[2].id);
    const [quantity, setQuantity] = useState(1);
    const [showSuccess, setShowSuccess] = useState(false);
    const [customerEmail, setCustomerEmail] = useState('');
    const [termsAccepted, setTermsAccepted] = useState(false);
    const [checkoutLoading, setCheckoutLoading] = useState(false);
    const [checkoutError, setCheckoutError] = useState(null);
    const [cancelledOrder, setCancelledOrder] = useState('');
    const [ticketing, setTicketing] = useState({
        match: normalizeMockMatch(ticketsMock.match),
        zones: ticketsMock.zones.map(normalizeMockZone),
        quantityLimit: ticketsMock.quantityLimit,
    });

    useEffect(() => {
        const searchParams = new URLSearchParams(window.location.search);
        if (searchParams.get('cancelled') === '1') {
            setCancelledOrder(searchParams.get('order') ?? '');
        }
    }, []);

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
        let active = true;

        async function loadTicketingCatalog() {
            try {
                const featuredResponse = await ticketingService.getFeaturedMatch();
                const featuredMatch = featuredResponse.data?.data ?? null;

                if (!featuredMatch) {
                    throw new Error('No featured match');
                }

                const zonesFromFeatured = featuredMatch.zones ?? [];
                const zonesResponse = zonesFromFeatured.length
                    ? null
                    : await ticketingService.getMatchZones(featuredMatch.code);
                const zones = zonesFromFeatured.length
                    ? zonesFromFeatured
                    : zonesResponse?.data?.data ?? [];

                if (!active) {
                    return;
                }

                const normalizedMatch = normalizeApiMatch(featuredMatch);
                const normalizedZones = zones.length
                    ? zones.map(normalizeApiZone)
                    : ticketsMock.zones.map(normalizeMockZone);

                setTicketing({
                    match: normalizedMatch,
                    zones: normalizedZones,
                    quantityLimit: 6,
                });
                setSelectedZoneId(normalizedZones[0]?.id ?? ticketsMock.zones[2].id);
            } catch {
                if (!active) {
                    return;
                }

                setTicketing({
                    match: normalizeMockMatch(ticketsMock.match),
                    zones: ticketsMock.zones.map(normalizeMockZone),
                    quantityLimit: ticketsMock.quantityLimit,
                });
                setSelectedZoneId(ticketsMock.zones[2].id);
            }
        }

        loadTicketingCatalog();

        return () => {
            active = false;
        };
    }, []);

    const pageTitle = useMemo(
        () => settings.global_seo_title || 'Boletos | Veraguas United FC',
        [settings.global_seo_title],
    );

    const selectedZone =
        ticketing.zones.find((zone) => zone.id === selectedZoneId) ?? ticketing.zones[0];
    const total = selectedZone.price * quantity;

    function handleDecrease() {
        setQuantity((current) => Math.max(current - 1, 1));
    }

    function handleIncrease() {
        setQuantity((current) => Math.min(current + 1, ticketing.quantityLimit));
    }

    async function handlePayNow() {
        if (checkoutLoading) return;
        setCheckoutLoading(true);
        setCheckoutError(null);

        try {
            const response = await ticketingService.createOrder({
                match_event_code: ticketing.match.code,
                ticket_zone_id: selectedZoneId,
                quantity: quantity,
                customer_email: customerEmail,
                accept_terms: termsAccepted,
                customer_name: null,
                customer_phone: null,
            });

            const approveUrl = response.data?.approve_url || response.data?.data?.approve_url;

            if (!approveUrl) {
                throw new Error('No se recibió la URL de aprobación de PayPal.');
            }

            window.location.assign(approveUrl);
        } catch (err) {
            setCheckoutError(
                err?.response?.data?.message ||
                err?.response?.data?.error ||
                err?.message ||
                'No se pudo crear la orden de boletos. Intenta nuevamente.'
            );
            setCheckoutLoading(false);
        }
    }

    function handleReset() {
        setShowSuccess(false);
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
                <main className="min-h-screen pb-24 pt-40">
                    {showSuccess ? (
                        <TicketSuccessMock
                            successTicket={ticketsMock.successTicket}
                            match={ticketsMock.match}
                            selectedZone={selectedZone}
                            quantity={quantity}
                            total={total}
                            onReset={handleReset}
                        />
                    ) : (
                        <div className="mx-auto max-w-7xl px-margin-mobile md:px-margin-desktop">
                            {cancelledOrder ? (
                                <div className="mb-8 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                                    El pago de PayPal fue cancelado para la orden {cancelledOrder}. Puedes revisar la selección y reintentar cuando quieras.
                                </div>
                            ) : null}

                            <TicketMatchHeader match={ticketing.match} />

                            <div className="grid grid-cols-1 items-start gap-gutter lg:grid-cols-12">
                                <div className="space-y-12 lg:col-span-8">
                                    <TicketZoneSelector
                                        zones={ticketing.zones}
                                        selectedZoneId={selectedZoneId}
                                        onSelectZone={setSelectedZoneId}
                                    />
                                    <TicketQuantitySelector
                                        quantity={quantity}
                                        limit={ticketing.quantityLimit}
                                        onDecrease={handleDecrease}
                                        onIncrease={handleIncrease}
                                    />
                                </div>

                                <div className="lg:col-span-4">
                                    <TicketCheckoutSummary
                                        selectedZone={selectedZone}
                                        quantity={quantity}
                                        total={total}
                                        customerEmail={customerEmail}
                                        onEmailChange={setCustomerEmail}
                                        termsAccepted={termsAccepted}
                                        onTermsChange={setTermsAccepted}
                                        onPayNow={handlePayNow}
                                        loading={checkoutLoading}
                                        error={checkoutError}
                                    />
                                </div>
                            </div>
                        </div>
                    )}
                </main>
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

function normalizeMockMatch(match) {
    return {
        competition: match.competition,
        roundLabel: match.competition,
        dateLabel: match.dateLabel,
        timeLabel: match.timeLabel,
        stadium: match.stadium,
        homeTeam: match.homeTeam,
        awayTeam: match.awayTeam,
        homeLogoLabel: match.homeLogoLabel,
        awayLogoLabel: match.awayLogoLabel,
        status: 'scheduled',
    };
}

function normalizeMockZone(zone) {
    return {
        ...zone,
        slug: zone.id,
        availableQuantity: null,
        outOfStock: false,
    };
}

function normalizeApiMatch(match) {
    return {
        competition: [match.round_label, match.competition].filter(Boolean).join(' - '),
        roundLabel: match.round_label,
        dateLabel: formatDateLabel(match.match_date, match.date_label),
        timeLabel: formatTimeLabel(match.match_date, match.time_label),
        stadium: [match.stadium_name, match.stadium_location].filter(Boolean).join(', '),
        homeTeam: match.home_team,
        awayTeam: match.away_team,
        homeLogoLabel: match.metadata?.home_logo_label ?? buildTeamLabel(match.home_team),
        awayLogoLabel: match.metadata?.away_logo_label ?? buildTeamLabel(match.away_team),
        status: match.status,
        homeScore: match.home_score,
        awayScore: match.away_score,
        code: match.code,
    };
}

function normalizeApiZone(zone) {
    return {
        id: zone.id,
        name: zone.name,
        slug: zone.slug,
        displayName: zone.metadata?.display_name ?? zone.name.toUpperCase(),
        area: zone.metadata?.area ?? 'ZONA',
        price: Number.parseFloat(zone.price ?? 0),
        description: zone.description ?? 'Acceso al partido.',
        tone: zone.metadata?.tone ?? 'neutral',
        availableQuantity: zone.available_quantity,
        outOfStock: zone.out_of_stock,
    };
}

function buildTeamLabel(team) {
    return String(team)
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0])
        .join('')
        .toUpperCase();
}

function formatDateLabel(date, fallback) {
    if (fallback) {
        return String(fallback).toUpperCase();
    }

    if (!date) {
        return '';
    }

    return new Intl.DateTimeFormat('es-PA', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    }).format(new Date(date)).toUpperCase();
}

function formatTimeLabel(date, fallback) {
    if (fallback) {
        return fallback;
    }

    if (!date) {
        return '';
    }

    return new Intl.DateTimeFormat('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
    }).format(new Date(date));
}
