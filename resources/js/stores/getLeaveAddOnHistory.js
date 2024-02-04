import { defineStore } from 'pinia'
import $api from "../components/api";

export const useLeaveAddOnStore = defineStore({
    id: 'leaveAddOnStore',
    state: () => ({
        leaveAddOnHistory: [],
        currentPage: 1,
        totalPages: 1,
        apiCall: false,
    }),
    getters: {},
    actions: {
        async init() {
            if (!this.apiCall) {
                await this.getLeaveAddOnHistory()
            }
        },

        async getLeaveAddOnHistory() {
            await $api.get('/api/administration/update-annual-leave?page=' + this.currentPage)
                .then(response => {
                    this.leaveAddOnHistory = response.data.data;
                    this.totalPages = response.data.meta.last_page;
                    this.apiCall = true;
                });
        },
    },
    persist: true,
});
