import {defineStore} from 'pinia'
import $api from "../components/api";

export const useDynamicUsersStore = defineStore({
    id: 'dynamicUsers',
    state: () => ({
        users: [],
        apiCall: false,
    }),
    getters: {},
    actions: {
        async init(selectedUser,addHR) {
            this.getAllUsers(selectedUser,addHR);
        },

        async getAllUsers(selectedUser, addHr) {
            await $api.get('/api/approver/all').then(response => {
                addHr && response.data.data.push({
                    id: 0,
                    name: 'HRA department',
                    department: 'HRA'
                })
                this.users = response.data.data;
                this.users = this.users.filter(user => user.id !== selectedUser.id);
                this.apiCall = true;
            });
        },

        addUser(selected) {
            if (selected === null) {
                return;
            }
            this.users.push(selected);
        },

        removeUser(selected) {
            if (selected === null) {
                return;
            }
            this.users = this.users.filter(user => user.id !== selected.id);
        }
    },
    persist: true,
});
