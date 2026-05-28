import apiClient from '@/services/apiClient';

const playerService = {
    getPlayers(params = {}) {
        return apiClient.get('/players', { params });
    },

    getPlayer(slug) {
        return apiClient.get(`/players/${slug}`);
    },
};

export default playerService;
