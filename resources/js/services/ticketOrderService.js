import apiClient from '@/services/apiClient';

const ticketOrderService = {
    createOrder(data) {
        return apiClient.post('/ticketing/orders', data);
    },

    getOrder(orderNumber) {
        return apiClient.get(`/ticketing/orders/${orderNumber}`);
    },
};

export default ticketOrderService;
