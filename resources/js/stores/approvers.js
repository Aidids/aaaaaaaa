import { defineStore } from 'pinia'
import $api from "../components/api";

export const useApproverStore = defineStore({
    id: 'approverStore',
    state: () => ({
        supervisors: [],
        headOfDepartments: [],
        approver_id: null,
        apiCall: false,
    }),
    getters: {},
    actions: {
        async init() {
            if (!this.apiCall) {
                this.getApproverLevel();
                this.getApprover();
            }
        },

        async getApprover() {
            this.supervisors = [];
            this.headOfDepartments = [];

            await $api.get('/api/approver')
                .then(response => {
                    let approvers = response.data.data;
                    approvers.forEach((approver) => {
                        if (approver.approver_level === 1 && approver.id !== parseInt(localStorage.getItem('user_id'))) {
                            this.supervisors.push(approver);
                        } else if(approver.approver_level === 2 && approver.id !== parseInt(localStorage.getItem('user_id'))) {
                            this.headOfDepartments.push(approver);
                        }
                    });
                    this.apiCall = true;
                });
        },

        async getApproverLevel() {
            await $api.get('/api/approver/' + parseInt(localStorage.getItem('user_id')))
                .then(response => {
                    this.approver_id = response.data.data.approver_id;
                });
        },
    },
    persist: true,
});
