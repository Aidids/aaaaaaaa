import {defineStore} from 'pinia'
import $api from "../components/api";

export const useHolidayStore = defineStore({
    id: 'holidayStore',
    state: () => ({
        holidays: [],
        apiCall: false,
    }),
    getters: {},
    actions: {
        async init() {
            (! this.apiCall) && await this.getApi();
        },

        currentDate: function() {
            let dateTime = new Date();
            let year = dateTime.getFullYear();
            let month = (dateTime.getMonth() + 1).toString().padStart(2, '0');
            let day = dateTime.getDate().toString().padStart(2, '0');

            return `${year}-${month}-${day}`;
        },

        async getApi() {
            let params = {
                date: this.currentDate()
            }

            await $api.get('/api/holidays', {params}).then(response => {

                response.data.data.forEach((att) => {
                    this.holidays.push(
                        {
                            content: 'red',
                            dates: new Date(att.date),
                            popover: {
                                label: att.label
                            }
                        }
                    );
                });
                this.apiCall = true;
            });
        },
    },
    persist: true,
});
