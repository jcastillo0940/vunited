import apiClient from '@/services/apiClient';

const standingService = {
    getStandings: (params = {}) => apiClient.get('/standings', { params }),
};

export default standingService;

const OWN_CLUB_SLUGS = ['veraguas-united-fc', 'veraguas-united'];

export function normalizeStandingsForCard(rows) {
    return rows.map((row) => ({
        position: row.position,
        club:     row.club?.name ?? '—',
        played:   row.played,
        points:   row.points,
        featured: OWN_CLUB_SLUGS.includes(row.club?.slug ?? ''),
    }));
}
