import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import SponsorTierSection from '@/components/sponsors/SponsorTierSection';
import SponsorValueSection from '@/components/sponsors/SponsorValueSection';
import SponsorLeadForm from '@/components/sponsors/SponsorLeadForm';
import homeMock from '@/mocks/homeMock';
import sponsorsMock from '@/mocks/sponsorsMock';
import sponsorService from '@/services/sponsorService';
import { fetchSiteSettings } from '@/services/siteService';
import { fetchMenu } from '@/services/menuService';
import {
    buildPublicFooterLinks,
    buildPublicHeaderLinks,
    publicLegalLinks,
} from '@/config/publicNavigation';

const TIER_CONFIG = {
    main_partner:     { key: 'main-partners',       title: 'Main Partners',         variant: 'main'     },
    official_sponsor: { key: 'official-sponsors',   title: 'Official Sponsors',      variant: 'official' },
    strategic_ally:   { key: 'strategic-alliances', title: 'Alianzas Estratégicas',  variant: 'alliance' },
};

const TIER_ORDER = ['main_partner', 'official_sponsor', 'strategic_ally'];

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
    global_seo_title: 'Patrocinadores | Veraguas United FC',
    global_seo_description: 'Aliados del Indio y programa comercial del club.',
    maintenance_mode: false,
};

export default function Sponsors() {
    const defaultHeaderLinks = useMemo(() => buildPublicHeaderLinks('/patrocinadores'), []);
    const defaultFooterLinks = useMemo(() => buildPublicFooterLinks(), []);
    const [settings, setSettings]   = useState(fallbackSettings);
    const [headerMenu, setHeaderMenu] = useState(defaultHeaderLinks);
    const [footerMenu, setFooterMenu] = useState(defaultFooterLinks);
    const [hero, setHero]             = useState(sponsorsMock.hero);
    const [valueProps, setValueProps] = useState(sponsorsMock.valueProps);
    const [leadForm, setLeadForm]     = useState(sponsorsMock.leadForm);
    const [tiers, setTiers]           = useState(sponsorsMock.tiers);

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
                setHeaderMenu(toMenuLinks(header?.items ?? [], defaultHeaderLinks));
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
    }, []);

    useEffect(() => {
        let active = true;

        sponsorService
            .getSponsors()
            .then((res) => {
                if (!active) return;
                const apiSponsors = res.data?.data ?? [];
                if (apiSponsors.length) {
                    setTiers(buildTiers(apiSponsors));
                }
            })
            .catch(() => { /* keep mock on error */ });

        return () => { active = false; };
    }, []);

    const pageTitle = useMemo(
        () => settings.global_seo_title || 'Patrocinadores | Veraguas United FC',
        [settings.global_seo_title],
    );

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
                navbarCtaLabel="UNETE A LA TRIBU"
                navbarVariant="light"
                mainClassName="pt-0"
            >
                <HeroSponsorsSection hero={hero} />

                <section className="section-space bg-white">
                    <div className="page-shell max-w-7xl space-y-20">
                        {tiers.map((tier) => (
                            <SponsorTierSection key={tier.key} tier={tier} />
                        ))}
                    </div>
                </section>

                <SponsorValueSection
                    valueProps={valueProps}
                    imageUrl={hero.imageUrl}
                />

                <SponsorLeadForm formConfig={leadForm} />
            </AppLayout>
        </>
    );
}

// ─── Helpers ─────────────────────────────────────────────────────────────────

function normalizeApiSponsor(s) {
    return {
        id:         s.id,
        name:       s.name,
        tier:       s.tier_label ?? s.tier,
        tagline:    s.description ?? '',
        shortLabel: getShortLabel(s.name),
        logoPath:   s.logo_path ?? null,
        websiteUrl: s.website_url ?? null,
    };
}

function getShortLabel(name) {
    return String(name)
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((w) => w[0].toUpperCase())
        .join('');
}

function buildTiers(apiSponsors) {
    const grouped = {};
    for (const s of apiSponsors) {
        if (!grouped[s.tier]) grouped[s.tier] = [];
        grouped[s.tier].push(normalizeApiSponsor(s));
    }

    return TIER_ORDER
        .filter((t) => grouped[t]?.length > 0)
        .map((t) => ({
            ...TIER_CONFIG[t],
            sponsors: grouped[t],
        }));
}

function HeroSponsorsSection({ hero }) {
    return (
        <section className="relative overflow-hidden bg-primary pb-20 pt-40 md:pb-24 md:pt-48">
            <img
                src={hero.imageUrl}
                alt={hero.title}
                className="absolute inset-0 h-full w-full object-cover opacity-30"
            />
            <div className="absolute inset-0 bg-[linear-gradient(115deg,rgba(29,66,138,0.94)_0%,rgba(29,66,138,0.86)_52%,rgba(29,66,138,0.75)_100%)]" />

            <div className="page-shell relative z-10 max-w-7xl">
                <div className="max-w-4xl space-y-8">
                    <span className="inline-flex rounded-sm bg-accent px-4 py-2 text-xs font-bold uppercase tracking-[0.3em] text-white">
                        {hero.badge}
                    </span>
                    <h1 className="font-display text-5xl font-bold uppercase leading-[0.9] tracking-tight text-white md:text-7xl lg:text-[5.5rem]">
                        {hero.title}
                    </h1>
                    <div className="max-w-2xl border-l-2 border-accent/80 pl-6">
                        <p className="text-base leading-8 text-white/85 md:text-lg">
                            {hero.description}
                        </p>
                    </div>
                </div>
            </div>
        </section>
    );
}

function toMenuLinks(items = [], fallback = []) {
    if (!items.length) return fallback;
    return items.map((item) => ({
        label:    item.label,
        url:      item.url,
        active:   item.url === '/patrocinadores',
        children: toMenuLinks(item.children ?? []),
    }));
}
