import apiClient from './apiClient';

const storeOrderService = {
    createOrder: (data) => apiClient.post('/store/orders', data),
    getOrder: (orderNumber) => apiClient.get(`/store/orders/${orderNumber}`),
};

export default storeOrderService;
