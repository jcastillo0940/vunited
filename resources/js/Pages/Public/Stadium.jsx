import { useLayoutSettings } from "@/context/LayoutContext";
import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import { publicPrimaryCta } from '@/config/publicNavigation';
import StadiumHero from '@/components/stadium/StadiumHero';
import StadiumInfo from '@/components/stadium/StadiumInfo';
import StadiumMap from '@/components/stadium/StadiumMap';
import StadiumZones from '@/components/stadium/StadiumZones';
import MatchdayExperience from '@/components/stadium/MatchdayExperience';
import StadiumRules from '@/components/stadium/StadiumRules';
import StadiumCTA from '@/components/stadium/StadiumCTA';
import stadiumMock from '@/mocks/stadiumMock';
import stadiumService, { normalizeStadium } from '@/services/stadiumService';

export default function Stadium() {
    const settings = useLayoutSettings();
    const [stadium, setStadium]       = useState(stadiumMock);

    useEffect(() => {
        let active = true;

        async function load() {
            try {
                const [siteSettings, header, footer, stadiumRes] = await Promise.all([
                    stadiumService.getStadium().catch(() => null),
                ]);

                if (!active) return;

                const raw = stadiumRes?.data?.data ?? null;
                if (raw) setStadium(normalizeStadium(raw));
            } catch {
                if (!active) return;
            }
        }

        load();
        return () => { active = false; };
    }, [defaultFooterLinks, defaultHeaderLinks]);

    const pageTitle = useMemo(
        () => settings.global_seo_title || 'Estadio Atalaya | Veraguas United FC',
        [settings.global_seo_title],
    );

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
                <StadiumHero hero={stadium.hero} />
                <StadiumInfo info={stadium.info} />
                <StadiumMap map={stadium.map} />
                <StadiumZones zones={stadium.zones} />
                <MatchdayExperience items={stadium.matchday} />
                <StadiumRules rules={stadium.rules} />
                <StadiumCTA cta={stadium.cta} />
            </AppLayout>
        </>
    );
}

function toMenuLinks(items = [], fallback = [], activeUrl = '') {
    if (!items.length) return fallback;
    return items.map((item) => ({
        ...item,
        active:   item.url ? item.url === activeUrl : false,
        children: toMenuLinks(item.children ?? [], [], activeUrl),
    }));
}
