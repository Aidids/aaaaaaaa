import { mapState } from "pinia";
import { useHolidayStore } from "../stores/holidays";

export default {
    data() {
        return {
            attributes: [],
        }
    },

    async created() {
        await useHolidayStore().init();
        await this.setupCalendar();
    },

    methods: {
        async setupCalendar() {
            this.attributes = [
                {
                    key: 'today',
                    highlight: 'green',
                    popover: {
                        label: 'Today',
                    },
                    dates: new Date(),
                },
                ...this.holidays
            ];
        }
    },

    computed: {
        ...mapState(useHolidayStore, ['holidays'])
    }
}