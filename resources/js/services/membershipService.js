import apiClient from './apiClient';

const membershipService = {
    getActivePlan: () => apiClient.get('/membership-plans/active'),
    createOrder: (data) => apiClient.post('/membership-orders', data),
    getOrder: (orderNumber) => apiClient.get(`/membership-orders/${orderNumber}`),
};

export default membershipService;
