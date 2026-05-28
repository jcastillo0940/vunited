import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import BoardHero from '@/components/board/BoardHero';
import PresidentBlock from '@/components/board/PresidentBlock';
import ExecutiveStaffGrid from '@/components/board/ExecutiveStaffGrid';
import BoardMembersGrid from '@/components/board/BoardMembersGrid';
import TransparencyCTA from '@/components/board/TransparencyCTA';
import homeMock from '@/mocks/homeMock';
import boardMock from '@/mocks/boardMock';
import boardService from '@/services/boardService';
import { fetchSiteSettings } from '@/services/siteService';
import { fetchMenu } from '@/services/menuService';
import {
    buildPublicFooterLinks,
    buildPublicHeaderLinks,
    publicLegalLinks,
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
    global_seo_title: 'Directiva | Veraguas United FC',
    global_seo_description: 'Liderazgo y visión institucional del club.',
    maintenance_mode: false,
};

export default function Board() {
    const defaultHeaderLinks = useMemo(() => buildPublicHeaderLinks('/directiva'), []);
    const defaultFooterLinks = useMemo(() => buildPublicFooterLinks(), []);
    const [settings, setSettings]     = useState(fallbackSettings);
    const [headerMenu, setHeaderMenu] = useState(defaultHeaderLinks);
    const [footerMenu, setFooterMenu] = useState(defaultFooterLinks);
    const [hero, setHero]             = useState(boardMock.hero);
    const [transparency, setTransparency] = useState(boardMock.transparency);
    const [president, setPresident]   = useState(boardMock.president);
    const [executives, setExecutives] = useState(boardMock.executives);
    const [members, setMembers]       = useState(boardMock.members);

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
                setHeaderMenu(toMenuLinks(header?.items ?? [], defaultHeaderLinks, '/directiva'));
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

        boardService
            .getBoardMembers()
            .then((res) => {
                if (!active) return;
                const apiMembers = res.data?.data ?? [];
                if (!apiMembers.length) return;

                const grouped = groupByGroup(apiMembers);

                const presidencyMembers = grouped.presidency ?? [];
                if (presidencyMembers.length) {
                    setPresident(normalizePresident(presidencyMembers[0]));
                }

                const execMembers = grouped.executive_staff ?? [];
                if (execMembers.length) {
                    setExecutives(execMembers.map((m, i) => normalizeExecutive(m, i)));
                }

                const boardMembers = [
                    ...(grouped.board ?? []),
                    ...(grouped.transparency ?? []),
                ];
                if (boardMembers.length) {
                    setMembers(boardMembers.map(normalizeBoardMember));
                }
            })
            .catch(() => { /* keep mock */ });

        return () => { active = false; };
    }, []);

    const pageTitle = useMemo(
        () => settings.global_seo_title || 'Directiva | Veraguas United FC',
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
                <BoardHero hero={hero} />
                <PresidentBlock president={president} />
                <ExecutiveStaffGrid executives={executives} />
                <BoardMembersGrid members={members} />
                <TransparencyCTA transparency={transparency} />
            </AppLayout>
        </>
    );
}

// ─── Normalization helpers ────────────────────────────────────────────────────

function groupByGroup(apiMembers) {
    const grouped = {};
    for (const m of apiMembers) {
        if (!grouped[m.group]) grouped[m.group] = [];
        grouped[m.group].push(m);
    }
    return grouped;
}

function normalizePresident(m) {
    const meta = m.metadata ?? {};
    return {
        name:          m.name,
        role:          m.group_label ?? 'Presidencia',
        title:         meta.title ?? m.role,
        message:       m.biography ?? '',
        imageUrl:      m.photo_path ?? boardMock.president.imageUrl,
        primaryAction: meta.primary_action ?? { label: 'Ver trayectoria', href: null },
        socialActions: meta.social_actions ?? boardMock.president.socialActions,
    };
}

function normalizeExecutive(m, index) {
    const meta  = m.metadata ?? {};
    const tones = ['primary', 'accent', 'primary'];
    return {
        id:          m.id,
        name:        m.name,
        role:        m.role,
        area:        meta.area ?? '',
        description: m.biography ?? '',
        imageUrl:    m.photo_path ?? boardMock.executives[index]?.imageUrl ?? null,
        tone:        meta.tone ?? tones[index % tones.length],
        icons:       meta.icons ?? ['groups', 'mail'],
    };
}

function normalizeBoardMember(m) {
    const meta = m.metadata ?? {};
    return {
        id:       m.id,
        name:     m.name,
        role:     m.role,
        category: meta.category ?? m.group_label ?? 'Directiva',
    };
}

function toMenuLinks(items = [], fallback = [], activeUrl = '') {
    if (!items.length) return fallback;
    return items.map((item) => ({
        label:    item.label,
        url:      item.url,
        active:   item.url === activeUrl,
        children: toMenuLinks(item.children ?? [], [], activeUrl),
    }));
}
