import { defineStore } from 'pinia'
import $api from "../components/api";

export const useProfileStore = defineStore({
    id: 'profileStore',
    state: () => ({
        profile: [],
        apiCall: false,
    }),
    getters: {},
    actions: {
        async init() {
            if (! this.apiCall) {
                this.getProfile()
            }
        },

        async getProfile() {
            await $api.get('/api/profile-settings/' + parseInt(localStorage.getItem('user_id')))
                .then(response => {
                    this.profile = response.data.data;
                    setTimeout(() => {
                        this.apiCall = true;
                    }, 500);
                });
        },
    },
    persist: true,
});
