import { defineStore } from 'pinia'
import $api from "../components/api";

export const useUserStore = defineStore({
    id: 'userStore',
    state: () => ({
        users: [],
        apiCall: false,
    }),
    getters: {},
    actions: {
        async init() {
            if (!this.apiCall) {
                this.getAllUsers()
            }
        },

        async getAllUsers() {
            await $api.get('/api/approver/all').then(response => {
                this.users = response.data.data;
            });
        },
    },
    persist: true,
});
