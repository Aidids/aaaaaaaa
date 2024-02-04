import { defineStore } from 'pinia'
import $api from "../components/api";

export const useLeaveBalance = defineStore({
    id: 'leaveBalanceStore',
    state: () => ({
        leaveBalance: [],
        loadApi: true,
    }),
    getters: {},
    actions: {
        async init() {
            await this.getLeaveBalance();
        },

        async getLeaveBalance() {
            await $api.get('/api/leave-balance/' + parseInt(localStorage.getItem('user_id')))
                .then(response => {
                    this.leaveBalance = response.data.data;
                    this.loadApi = false;
                });
        },
    },
    persist: true,
});
