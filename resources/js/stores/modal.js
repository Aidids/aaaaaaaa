import {defineStore} from 'pinia'
import $api from "../components/api";

export const useModalStore = defineStore({
    id: 'modalStore',
    state: () => ({
        modal: {
            show: false,
            message: '',
            isClose: true,
            loader: false,
            hasConfirm: false
        }
    }),
    getters: {},
    actions: {
        init() {
            this.modal = {
                show: false,
                message: '',
                isClose: true,
                loader: false,
                hasConfirm: false,
            }
        },
        load() {
            this.modal = {
                show: true,
                loader: true,
                message: '',
                isClose: true,
                hasConfirm: false,
            }
        },
        finishLoad() {
            this.modal = {
                show: false,
                loader: false,
                message: '',
                isClose: true,
                hasConfirm: false,
            }
        },
        show(message) {
            this.modal = {
                show: true,
                loader: false,
                message: message,
                isClose: true,
                hasConfirm: false,
            }
        },
        action(message) {
            this.modal = {
                show: true,
                loader: false,
                message: message,
                isClose: false,
                hasConfirm: false,
            }
        },
        confirm(message) {
            this.modal = {
                show: true,
                loader: false,
                message: message,
                isClose: true,
                hasConfirm: true
            }
        }
    },
    persist: true,
});
