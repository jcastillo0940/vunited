import apiClient from '@/services/apiClient';

const fanFestService = {
    getEvent() {
        return apiClient.get('/fanfest');
    },
};

export default fanFestService;
