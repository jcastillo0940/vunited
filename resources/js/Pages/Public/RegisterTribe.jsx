import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import { publicPrimaryCta } from '@/config/publicNavigation';
import RegisterTribeHero from '@/components/register-tribe/RegisterTribeHero';
import RegisterTribeForm from '@/components/register-tribe/RegisterTribeForm';
import registerTribeMock from '@/mocks/registerTribeMock';
import membershipService from '@/services/membershipService';

export default function RegisterTribe() {
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
        () => settings.global_seo_title || 'Registro La Tribu | Veraguas United FC',
        [settings.global_seo_title],
    );

    const hero = useMemo(() => ({
        ...registerTribeMock.hero,
        description: activePlan?.headline || activePlan?.description || registerTribeMock.hero.description,
    }), [activePlan]);

    const summary = useMemo(() => {
        if (!activePlan) {
            return registerTribeMock.summary;
        }

        return {
            membership: activePlan.name,
            duration: `${activePlan.duration_months} Meses`,
            access: activePlan.metadata?.access || registerTribeMock.summary.access,
            total: formatMoney(activePlan.price, activePlan.currency),
            note: activePlan.metadata?.billing_note || registerTribeMock.summary.note,
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
                navbarVariant="solid"
                mainClassName="pt-40 pb-24"
            >
                <RegisterTribeHero hero={hero} />
                <RegisterTribeForm
                    summary={summary}
                    membershipPlanCode={activePlan?.code || 'tribu'}
                />
            </AppLayout>
        </>
    );
}

function toMenuLinks(items = [], fallback = []) {
    if (!items.length) {
        return fallback;
    }

    return items.map((item) => ({
        ...item,
        children: toMenuLinks(item.children ?? [], []),
    }));
}

function formatMoney(price, currency) {
    const numeric = Number.parseFloat(price ?? 0);

    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency || 'USD',
    }).format(Number.isNaN(numeric) ? 0 : numeric);
}
