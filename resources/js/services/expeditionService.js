import apiClient from '@/services/apiClient';

const expeditionService = {
    getTrips() {
        return apiClient.get('/expeditions');
    },
};

export default expeditionService;
