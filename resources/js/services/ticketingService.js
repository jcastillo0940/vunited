import apiClient from '@/services/apiClient';

const ticketingService = {
    getMatches(params = {}) {
        return apiClient.get('/api/ticketing/matches', { params });
    },

    getFeaturedMatch() {
        return apiClient.get('/api/ticketing/matches/featured');
    },

    getMatch(code) {
        return apiClient.get(`/api/ticketing/matches/${code}`);
    },

    getMatchZones(code) {
        return apiClient.get(`/api/ticketing/matches/${code}/zones`);
    },

    createOrder(data) {
        return apiClient.post('/ticketing/orders', data);
    },

    getOrder(orderNumber) {
        return apiClient.get(`/ticketing/orders/${orderNumber}`);
    },

    getOrderTickets(orderNumber) {
        return apiClient.get(`/ticketing/orders/${orderNumber}/tickets`);
    },
};

export default ticketingService;
