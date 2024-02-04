import { defineStore } from 'pinia'
import $api from "../components/api";

export const useDepartmentStore = defineStore({
    id: 'departmentStore',
    state: () => ({
        departmentOption: [],
        apiCall: false
    }),
    getters: {},
    actions: {
        async init() {
            if (!this.apiCall) {
                await this.getDepartment();
            }
        },

        getDepartmentName(deptId) {
            const department = this.departmentOption.find(item => item.id === deptId);
            return department ? department.name : 'Department Not Selected';
        },

        async getDepartment() {
            this.supervisors = [];
            this.headOfDepartments = [];

            await $api.get('/api/administration/departments')
                .then(response => {
                    this.departmentOption = [
                        {
                            id: 0,
                            name: 'All Department'
                        },
                        ... response.data.data
                    ];
                    this.apiCall = true;
                });
        },
    },
    persist: true,
});
