import {defineStore} from 'pinia'
import $api from "../components/api";

export const useDepartmentLeaveStore = defineStore({
    id: 'departmentLeaveStore',
    state: () => ({
        departmentLeaves: [],
        list: [],
        apiCall: false,
    }),
    getters: {},
    actions: {
        async init(id) {
            (! this.apiCall) && await this.getApi(id);
        },

        async getApi(id) {
            await $api.get('/api/department-leave/' + id)
                .then(response => {
                    response.data[0].users.forEach((att) => {
                        this.list.push({
                            department_id: att.department_id,
                            id: att.id,
                            name: att.name,
                            start_date: att.start_date,
                            start_date_type: att.start_date_type,
                            end_date: att.end_date,
                            end_date_type: att.end_date_type,
                            duration: att.duration,
                            overall_status: att.overall_status,
                            leave_balance_id: att.leave_balance_id,
                            leave_type_id: att.leave_type_id,
                            leave_name: att.leave_name,
                            color: this.assignColors(att.leave_name),
                        });

                        this.departmentLeaves.push({
                            highlight: {
                                color: this.assignColors(att.leave_name),
                            },
                            dates: {
                                start: new Date(att.start_date),
                                end: new Date(att.end_date)
                            },
                            popover: {
                                label: `${att.name} ${att.leave_name}`
                            }
                        });
                    })
                    this.apiCall = true;
                })
                .catch((e) => {
                    console.log(e);
                    this.apiCall = true;
                });
        },

        assignColors(leaveType) {
            switch (leaveType) {
                case 'Annual Leave':
                    return 'blue';
                case 'Replacement Leave':
                    return 'blue';
                case 'Offshore Leave':
                    return 'blue';
                case 'Out Of Office Leave':
                    return 'blue';

                case 'Medical Leave':
                    return 'orange';
                case 'Emergency Leave':
                    return 'orange';
                case 'Hospitalisation Leave':
                    return 'orange';

                case 'Maternity Leave':
                    return 'teal';
                case 'Paternity Leave':
                    return 'teal';

                case 'Unpaid Leave':
                    return 'gray';
                case 'Compassionate Leave':
                    return 'gray';

                default: return 'gray';
            }
        },
    },
    persist: true,
});
