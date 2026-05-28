import apiClient from '@/services/apiClient';

const staffService = {
    getStaff(params = {}) {
        return apiClient.get('/staff', { params });
    },
};

export default staffService;
