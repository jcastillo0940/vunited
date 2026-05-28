import apiClient from '@/services/apiClient';

const sponsorService = {
    getSponsors(params = {}) {
        return apiClient.get('/sponsors', { params });
    },
};

export default sponsorService;
