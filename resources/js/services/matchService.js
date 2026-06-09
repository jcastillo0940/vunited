import apiClient from '@/services/apiClient';

const matchService = {
    getMatches: (params = {}) => apiClient.get('/matches', { params }),
    getFeatured: ()           => apiClient.get('/matches/featured'),
    getMatch:    (code)       => apiClient.get(`/matches/${code}`),
};

export default matchService;

// ─── Normalizers ─────────────────────────────────────────────────────────────

const OWN_CLUB_SLUGS = ['veraguas-united-fc', 'veraguas-united'];

function isOwnClub(team, club) {
    if (club?.slug && OWN_CLUB_SLUGS.includes(club.slug)) return true;
    if (typeof team === 'string') return team.toUpperCase().includes('VERAGUAS');
    return false;
}

export function normalizeMatchForBar(match) {
    if (!match) return null;

    const homeCode = match.home_club?.short_name ?? match.home_team?.substring(0, 3).toUpperCase() ?? '???';
    const awayCode = match.away_club?.short_name ?? match.away_team?.substring(0, 3).toUpperCase() ?? '???';
    const homeName = match.home_club?.name ?? match.home_team ?? '';
    const awayName = match.away_club?.name ?? match.away_team ?? '';

    if (match.status === 'finished') {
        const homeIsVU = isOwnClub(match.home_team, match.home_club);
        const vuScore  = homeIsVU ? match.home_score : match.away_score;
        const rivScore = homeIsVU ? match.away_score : match.home_score;
        let note = 'Resultado';
        if (vuScore !== null && rivScore !== null) {
            if (vuScore > rivScore) note = homeIsVU ? 'Victoria en Casa' : 'Victoria de Visita';
            else if (vuScore < rivScore) note = 'Derrota';
            else note = 'Empate';
        }

        return {
            label:     'ÚLTIMO RESULTADO',
            homeCode,
            homeScore: match.home_score ?? '—',
            awayCode,
            awayScore: match.away_score ?? '—',
            note,
            date:      match.date_label ?? '',
        };
    }

    const venueNote = isOwnClub(match.home_team, match.home_club) ? 'LOCAL' : 'DE VISITA';
    return {
        label:      'PRÓXIMO PARTIDO',
        homeCode,
        homeName:   homeName.substring(0, 12),
        awayCode,
        awayName:   awayName.substring(0, 12),
        note:       match.round_label ?? venueNote,
        date:       match.date_label ? `${match.date_label}, ${match.time_label}` : '',
        ticketHref: '/boletos',
    };
}

export function normalizeMatchForCalendar(match) {
    const homeIsVU  = isOwnClub(match.home_team, match.home_club);
    const venueType = homeIsVU ? 'Local' : 'Visitante';

    const statusMap = {
        finished:  { status: 'finalizado', label: 'Finalizado' },
        live:      { status: 'en vivo',    label: 'En Vivo' },
        postponed: { status: 'aplazado',   label: 'Aplazado' },
        cancelled: { status: 'cancelado',  label: 'Cancelado' },
        scheduled: { status: 'proximo',    label: 'Próximo' },
    };
    const { status, label: statusLabel } = statusMap[match.status] ?? { status: match.status, label: match.status };

    return {
        id:          match.code,
        competition: [match.round_label, match.competition].filter(Boolean).join(' — '),
        dateLabel:   match.date_label ?? '',
        timeLabel:   match.time_label ?? '',
        stadium:     match.stadium_name ?? '',
        venueType,
        homeTeam:    match.home_club?.name ?? match.home_team,
        awayTeam:    match.away_club?.name ?? match.away_team,
        homeShort:   match.home_club?.short_name ?? match.home_team?.substring(0, 3).toUpperCase(),
        awayShort:   match.away_club?.short_name ?? match.away_team?.substring(0, 3).toUpperCase(),
        homeScore:   match.home_score ?? null,
        awayScore:   match.away_score ?? null,
        status,
        statusLabel,
        ctaLabel:    status === 'proximo' ? 'Comprar boletos' : null,
        ctaHref:     status === 'proximo' ? `/boletos` : null,
    };
}

export function normalizeMatchForTicker(match) {
    if (!match) return null;

    const homeIsVU = isOwnClub(match.home_team, match.home_club);
    const rival    = homeIsVU
        ? (match.away_club?.short_name ?? match.away_team ?? '?')
        : (match.home_club?.short_name ?? match.home_team ?? '?');
    const prefix = homeIsVU ? 'VS' : '@';

    return {
        label: 'PRÓXIMO',
        text:  `${prefix} ${rival.toUpperCase()}`,
    };
}
