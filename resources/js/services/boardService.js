import apiClient from '@/services/apiClient';

const boardService = {
    getBoardMembers(params = {}) {
        return apiClient.get('/board-members', { params });
    },
};

export default boardService;
