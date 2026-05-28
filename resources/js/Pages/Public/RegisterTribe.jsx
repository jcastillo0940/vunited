import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import RegisterTribeHero from '@/components/register-tribe/RegisterTribeHero';
import RegisterTribeForm from '@/components/register-tribe/RegisterTribeForm';
import homeMock from '@/mocks/homeMock';
import registerTribeMock from '@/mocks/registerTribeMock';
import membershipService from '@/services/membershipService';
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
    global_seo_title: 'Registro La Tribu | Veraguas United FC',
    global_seo_description: 'Registro visual y local para socios del FanClub.',
    maintenance_mode: false,
};

export default function RegisterTribe() {
    const defaultHeaderLinks = useMemo(() => buildPublicHeaderLinks('/fanclub'), []);
    const defaultFooterLinks = useMemo(() => buildPublicFooterLinks(), []);
    const [settings, setSettings] = useState(fallbackSettings);
    const [headerMenu, setHeaderMenu] = useState(defaultHeaderLinks);
    const [footerMenu, setFooterMenu] = useState(defaultFooterLinks);
    const [activePlan, setActivePlan] = useState(null);

    useEffect(() => {
        let active = true;

        async function loadShell() {
            try {
                const [siteSettings, header, footer] = await Promise.all([
                    fetchSiteSettings(),
                    fetchMenu('header'),
                    fetchMenu('footer'),
                ]);

                if (!active) {
                    return;
                }

                setSettings(siteSettings ?? fallbackSettings);
                setHeaderMenu(toMenuLinks(header?.items ?? [], defaultHeaderLinks));
                setFooterMenu(toMenuLinks(footer?.items ?? [], defaultFooterLinks));
            } catch {
                if (!active) {
                    return;
                }

                setSettings(fallbackSettings);
                setHeaderMenu(defaultHeaderLinks);
                setFooterMenu(defaultFooterLinks);
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
