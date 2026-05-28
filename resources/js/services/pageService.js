import apiClient from '@/services/apiClient';

export async function fetchPageBySlug(slug) {
    const response = await apiClient.get(`/pages/${slug}`);

    return response.data.data;
}
