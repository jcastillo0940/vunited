import { useLayoutSettings } from "@/context/LayoutContext";
import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import BoardHero from '@/components/board/BoardHero';
import PresidentBlock from '@/components/board/PresidentBlock';
import ExecutiveStaffGrid from '@/components/board/ExecutiveStaffGrid';
import BoardMembersGrid from '@/components/board/BoardMembersGrid';
import TransparencyCTA from '@/components/board/TransparencyCTA';
import boardMock from '@/mocks/boardMock';
import boardService from '@/services/boardService';

export default function Board() {
    const settings = useLayoutSettings();
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
