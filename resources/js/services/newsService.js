import apiClient from '@/services/apiClient';

export async function fetchNews() {
    const response = await apiClient.get('/news');

    return response.data.data;
}

export async function fetchNewsBySlug(slug) {
    const response = await apiClient.get(`/news/${slug}`);

    return response.data.data;
}
