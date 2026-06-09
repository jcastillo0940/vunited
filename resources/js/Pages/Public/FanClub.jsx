const defaultHeaderLinks = [];
const defaultFooterLinks = [];
import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import { publicPrimaryCta } from '@/config/publicNavigation';
import { useLayoutSettings } from '@/context/LayoutContext';
import FanClubHero from '@/components/fanclub/FanClubHero';
import MembershipBenefits from '@/components/fanclub/MembershipBenefits';
import MembershipPlanCard from '@/components/fanclub/MembershipPlanCard';
import WelcomeKit from '@/components/fanclub/WelcomeKit';
import MemberAllies from '@/components/fanclub/MemberAllies';
import FanClubCTA from '@/components/fanclub/FanClubCTA';
import fanClubMock from '@/mocks/fanClubMock';
import membershipService from '@/services/membershipService';

export default function FanClub() {
    const settings = useLayoutSettings();
    const [activePlan, setActivePlan] = useState(null);

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

        async function loadPlan() {
            try {
                const response = await membershipService.getActivePlan();

                if (!active) {
                    return;
                }

                setActivePlan(response.data?.data ?? null);
            } catch {
                if (!active) {
                    return;
                }

                setActivePlan(null);
            }
        }

        loadPlan();

        return () => {
            active = false;
        };
    }, []);

    const pageTitle = useMemo(
        () => settings.global_seo_title || 'FanClub La Tribu | Veraguas United FC',
        [settings.global_seo_title],
    );

    const planView = useMemo(() => {
        if (!activePlan) {
            return fanClubMock;
        }

        const icons = ['confirmation_number', 'shopping_bag', 'handshake', 'event_available', 'stadium'];
        const fallbackKit = fanClubMock.welcomeKit;

        return {
            ...fanClubMock,
            hero: {
                ...fanClubMock.hero,
                description: activePlan.headline || activePlan.description || fanClubMock.hero.description,
            },
            annualPass: {
                ...fanClubMock.annualPass,
                name: activePlan.name,
                price: formatMoney(activePlan.price, activePlan.currency),
                cadence: `/ ${activePlan.duration_months} meses`,
                badge: activePlan.metadata?.badge || fanClubMock.annualPass.badge,
                bullets: (activePlan.benefits?.length ? activePlan.benefits : fanClubMock.annualPass.bullets.map((item) => item.text))
                    .slice(0, 3)
                    .map((benefit, index) => ({
                        icon: icons[index] || 'star',
                        text: benefit,
                    })),
            },
            salesCopy: {
                ...fanClubMock.salesCopy,
                eyebrow: activePlan.metadata?.season_label || fanClubMock.salesCopy.eyebrow,
                title: activePlan.metadata?.sales_title || fanClubMock.salesCopy.title,
                highlight: activePlan.metadata?.sales_highlight || fanClubMock.salesCopy.highlight,
                description: activePlan.description || fanClubMock.salesCopy.description,
                stats: [
                    { value: `${activePlan.duration_months}`, label: 'Meses de vigencia' },
                    { value: activePlan.currency, label: 'Moneda activa' },
                ],
            },
            benefits: (activePlan.benefits?.length ? activePlan.benefits : fanClubMock.benefits.map((item) => item.title))
                .map((benefit, index) => ({
                    id: index + 1,
                    icon: icons[index] || 'workspace_premium',
                    title: String(benefit).toUpperCase(),
                    description: activePlan.description || fanClubMock.benefits[index]?.description || 'Beneficio activo del plan de membresia.',
                })),
            welcomeKit: (activePlan.kit_items?.length ? activePlan.kit_items : fallbackKit.map((item) => item.title))
                .map((item, index) => ({
                    id: index + 1,
                    title: item,
                    description: 'Incluido en el plan activo de membresia.',
                    imageUrl: fallbackKit[index % fallbackKit.length].imageUrl,
                })),
            allies: (activePlan.partner_discounts?.length ? activePlan.partner_discounts : fanClubMock.allies.map((item) => item.name))
                .map((ally, index) => ({
                    id: index + 1,
                    name: ally,
                    shortLabel: buildShortLabel(ally),
                })),
            finalCta: {
                ...fanClubMock.finalCta,
                description: activePlan.description || fanClubMock.finalCta.description,
            },
        };
    }, [activePlan]);

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
                <FanClubHero hero={planView.hero} />
                <MembershipPlanCard annualPass={planView.annualPass} salesCopy={planView.salesCopy} />
                <WelcomeKit items={planView.welcomeKit} />
                <MembershipBenefits benefits={planView.benefits} />
                <MemberAllies allies={planView.allies} />
                <FanClubCTA cta={planView.finalCta} />
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

function formatMoney(price, currency) {
    const numeric = Number.parseFloat(price ?? 0);

    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency || 'USD',
    }).format(Number.isNaN(numeric) ? 0 : numeric);
}

function buildShortLabel(value = '') {
    const letters = String(value)
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase() || '')
        .join('');

    return letters || 'VU';
}
