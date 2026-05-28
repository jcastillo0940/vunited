import apiClient from './apiClient';

function buildQuery(params = {}) {
    const searchParams = new URLSearchParams();

    Object.entries(params).forEach(([key, value]) => {
        if (value === null || value === undefined || value === '' || value === false) {
            return;
        }

        searchParams.set(key, String(value));
    });

    const query = searchParams.toString();

    return query ? `?${query}` : '';
}

const productService = {
    getProducts: (params = {}) => apiClient.get(`/store/products${buildQuery(params)}`),
    getProduct: (slug) => apiClient.get(`/store/products/${slug}`),
    getCategories: () => apiClient.get('/store/categories'),
    getFeaturedProduct: () => apiClient.get('/store/featured-product'),
};

export default productService;
