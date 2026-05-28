import { useLayoutSettings } from "@/context/LayoutContext";
import { Head } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import AppLayout from '@/components/layout/AppLayout';
import EmptyState from '@/components/common/EmptyState';
import playersMock from '@/mocks/playersMock';
import SquadHero from '@/components/squad/SquadHero';
import SquadFilters from '@/components/squad/SquadFilters';
import SquadGrid from '@/components/squad/SquadGrid';
import StaffGrid from '@/components/squad/StaffGrid';
import playerService from '@/services/playerService';
import staffService from '@/services/staffService';

const CATEGORY_LABELS = {
    'first-team':  'Primer Equipo (LPF)',
    'women-team':  'Equipo Femenino (LFF)',
    'academy':     'Cantera',
};

export default function Squad() {
    const settings = useLayoutSettings();
    const [activeSquadId, setActiveSquadId]       = useState(playersMock.squadFilters[0].id);
    const [activePositionId, setActivePositionId] = useState(playersMock.positionFilters[0].id);
    const [squadData, setSquadData] = useState(buildMockSquadData());
    const [staffData, setStaffData] = useState(buildMockStaffData());

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

        async function loadSquad() {
            try {
                const [playersRes, staffRes] = await Promise.all([
                    playerService.getPlayers(),
                    staffService.getStaff(),
                ]);

                if (!active) return;

                const apiPlayers = playersRes.data?.data ?? [];
                const apiStaff   = staffRes.data?.data ?? [];

                if (apiPlayers.length) {
                    setSquadData(buildApiSquadData(apiPlayers));
                }

                if (apiStaff.length) {
                    setStaffData(buildApiStaffShape(apiStaff));
                }
            } catch {
                // keep mock data on error
            }
        }

        loadSquad();
        return () => { active = false; };
    }, []);

    const activeSquad = useMemo(
        () => activeSquadId === 'staff'
            ? null
            : squadData.squads.find((s) => s.id === activeSquadId) ?? squadData.squads[0],
        [activeSquadId, squadData],
    );

    const visiblePlayers = useMemo(() => {
        if (!activeSquad) return [];
        if (activePositionId === 'all') return activeSquad.players;
        return activeSquad.players.filter((p) => p.positionKey === activePositionId);
    }, [activePositionId, activeSquad]);

    const pageTitle = useMemo(
        () => settings.global_seo_title || 'Plantilla | Veraguas United FC',
        [settings.global_seo_title],
    );

    return (
        <>
            <Head title={pageTitle} />
            <AppLayout
                navbarBrandName="VERAGUAS UNITED"
                navbarCtaLabel="SOCIO INDIO"
                navbarVariant="light"
                mainClassName="pt-0"
            >
                <SquadHero hero={playersMock.hero} />

                <main className="page-shell min-h-screen max-w-7xl px-margin-mobile pb-24 pt-14 md:px-margin-desktop">
                    <SquadFilters
                        squadFilters={squadData.squadFilters}
                        activeSquadId={activeSquadId}
                        onSquadChange={setActiveSquadId}
                        positionFilters={playersMock.positionFilters}
                        activePositionId={activePositionId}
                        onPositionChange={setActivePositionId}
                    />

                    <StaffGrid staff={staffData} />

                    {visiblePlayers.length ? (
                        <SquadGrid players={visiblePlayers} />
                    ) : (
                        <EmptyState
                            title={
                                activeSquadId === 'staff'
                                    ? 'Vista enfocada en cuerpo técnico'
                                    : 'Sin jugadores para este filtro'
                            }
                            description={
                                activeSquadId === 'staff'
                                    ? 'Selecciona Primer Equipo, Equipo Femenino o Cantera para volver al grid de futbolistas.'
                                    : 'Cambia la categoría o la posición para ver otros perfiles de la plantilla.'
                            }
                        />
                    )}
                </main>
            </AppLayout>
        </>
    );
}

// ─── Normalization helpers ────────────────────────────────────────────────────

function normalizeApiPlayer(p) {
    const parts = p.name.split(' ');
    return {
        id:          p.id,
        slug:        p.slug,
        name:        p.name,
        firstName:   p.first_name ?? parts[0] ?? p.name,
        lastName:    p.last_name  ?? parts.slice(1).join(' ') ?? '',
        position:    p.position ?? '',
        positionKey: p.position_key ?? 'midfielder',
        number:      p.number ?? '',
        nationality: p.nationality ?? '',
        meta:        [p.nationality, CATEGORY_LABELS[p.category]].filter(Boolean).join(' • '),
        imageUrl:    p.photo_path ?? null,
        profile: {
            team:          'Veraguas United FC',
            age:           p.birth_date ? calculateAge(p.birth_date) : null,
            height:        p.height ?? null,
            weight:        p.weight ?? null,
            dominantFoot:  p.dominant_foot ?? null,
            biography:     p.biography ?? '',
            stats:         p.stats ?? [],
            attributes:    p.attributes ?? [],
            gallery:       p.gallery ?? [],
            socialActions: [
                { id: 'share', icon: 'share', label: 'Compartir' },
                { id: 'favorite', icon: 'favorite', label: 'Favorito' },
            ],
        },
    };
}

function normalizeApiStaffMember(s) {
    const parts = s.name.split(' ');
    return {
        id:          s.id,
        name:        s.name,
        firstName:   s.first_name ?? parts[0] ?? s.name,
        lastName:    s.last_name  ?? parts.slice(1).join(' ') ?? '',
        role:        s.role,
        description: s.biography ?? '',
        imageUrl:    s.photo_path ?? null,
    };
}

function buildApiSquadData(apiPlayers) {
    const grouped = {};
    for (const p of apiPlayers) {
        if (!grouped[p.category]) grouped[p.category] = [];
        grouped[p.category].push(normalizeApiPlayer(p));
    }

    const squads = Object.entries(grouped).map(([cat, players]) => ({
        id:      cat,
        label:   CATEGORY_LABELS[cat] ?? cat,
        players,
    }));

    const squadFilters = [
        ...squads.map((s) => ({ id: s.id, label: s.label })),
        { id: 'staff', label: 'Cuerpo Técnico' },
    ];

    return { squads, squadFilters };
}

function buildApiStaffShape(apiStaff) {
    const sorted  = [...apiStaff].sort((a, b) => a.sort_order - b.sort_order);
    const [head, ...rest] = sorted.map(normalizeApiStaffMember);

    return {
        featured:   head ?? buildMockStaffData().featured,
        assistants: rest,
    };
}

function buildMockSquadData() {
    return {
        squads:       playersMock.squads,
        squadFilters: playersMock.squadFilters,
    };
}

function buildMockStaffData() {
    return playersMock.staff;
}

function calculateAge(birthDateStr) {
    const diff = Date.now() - new Date(birthDateStr).getTime();
    return Math.floor(diff / (1000 * 60 * 60 * 24 * 365.25));
}

function toMenuLinks(items = [], fallback = []) {
    if (!items.length) return fallback;
    return items.map((item) => ({
        label:    item.label,
        url:      item.url,
        active:   item.url === '/plantilla',
        children: toMenuLinks(item.children ?? []),
    }));
}
