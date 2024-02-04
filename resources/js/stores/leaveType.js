import { defineStore } from 'pinia'
import $api from "../components/api";

export const useLeaveTypeStore = defineStore({
    id: 'leaveTypeStore',
    state: () => ({
        leaveType: [],
        adminLeaveType: [],
        apiCall: false,
    }),
    getters: {},
    actions: {
        async init() {
            if (!this.apiCall) {
                this.getAllLeaveType();
            }
        },

        async getAllLeaveType() {
            await $api.get('/api/leave-type').then(response => {
                this.leaveType = response.data.data;

                // for Admin/UpdateAnnualLeave
                this.adminLeaveType = this.leaveType.filter(item => {
                    return [1,8].includes(item.id);
                });
            });
        },
    },
    persist: true,
});
