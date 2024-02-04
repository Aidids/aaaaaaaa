import { defineStore } from 'pinia'

export const useLoadButton = defineStore({
    id: 'loadButton',
    state: () => ({
        load: false,
    }),
    getters: {},
    actions: {
        init () {
            this.load = false;
        },
        start () {
            this.load = true;
        },
        finish() {
            this.load = false;
        }
    },
    persist: true,
});
