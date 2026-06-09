import { Head } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import SponsorsHero from '@/components/sponsors/SponsorsHero';
import SponsorTierSection from '@/components/sponsors/SponsorTierSection';
import SponsorValueSection from '@/components/sponsors/SponsorValueSection';
import SponsorLeadForm from '@/components/sponsors/SponsorLeadForm';
import sponsorsMock from '@/mocks/sponsorsMock';
import sponsorService from '@/services/sponsorService';

const TIER_CONFIG = {
    main_partner:     { key: 'main-partners',       title: 'Main Partners',         variant: 'main'     },
    official_sponsor: { key: 'official-sponsors',   title: 'Official Sponsors',      variant: 'official' },
    strategic_ally:   { key: 'strategic-alliances', title: 'Alianzas Estratégicas',  variant: 'alliance' },
};

const TIER_ORDER = ['main_partner', 'official_sponsor', 'strategic_ally'];

export default function Sponsors() {
    const [hero, setHero]             = useState(sponsorsMock.hero);
    const [valueProps, setValueProps] = useState(sponsorsMock.valueProps);
    const [leadForm, setLeadForm]     = useState(sponsorsMock.leadForm);
    const [tiers, setTiers]           = useState(sponsorsMock.tiers);

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

    const pageTitle = 'Patrocinadores | Veraguas United FC';

    return (
        <>
            <Head title={pageTitle} />
            <AppLayout
                navbarBrandName="VERAGUAS UNITED"
                navbarCtaLabel="UNETE A LA TRIBU"
                navbarVariant="light"
                mainClassName="pt-0"
            >
                <SponsorsHero hero={hero} />

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


function toMenuLinks(items = [], fallback = []) {
    if (!items.length) return fallback;
    return items.map((item) => ({
        label:    item.label,
        url:      item.url,
        active:   item.url === '/patrocinadores',
        children: toMenuLinks(item.children ?? []),
    }));
}
