import apiClient from '@/services/apiClient';

export async function fetchSiteSettings() {
    const response = await apiClient.get('/site-settings');

    return response.data.data;
}
