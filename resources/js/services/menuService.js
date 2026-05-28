import apiClient from '@/services/apiClient';

export async function fetchMenu(location) {
    const response = await apiClient.get(`/menu/${location}`);

    return response.data.data;
}
