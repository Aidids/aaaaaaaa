import { defineStore } from 'pinia'
import $api from "../components/api";

export const usePersonalInfo = defineStore({
    id: 'personalInfo',
    state: () => ({
        user: {},
        apiCall: false,
    }),
    getters: {},
    actions: {
        async init(user_id) {
            if (! this.apiCall) {
                this.getPersonalInformationApi(user_id)
            }
        },

        async getPersonalInformationApi(user_id) {
            await $api.get('/api/profile-settings/' + parseInt(user_id) + '/personal-information')
                .then(response => {
                    this.user = response.data.data;
                    if (this.user.educations === undefined) {
                        this.user.educations = []
                    }
                    if (this.user.children === undefined) {
                        this.user.children = []
                    }
                    this.apiCall = true;
                });
        },
    },
    persist: true,
});
