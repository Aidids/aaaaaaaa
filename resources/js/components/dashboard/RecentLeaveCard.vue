<template>
    <h4 class="mt-3">Leave History</h4>
    <TableMain v-if="leaveBalance.length > 0" :card="true">
        <thead>
        <tr>
            <th class="text-start" style="min-width: 12rem;">Leave Type</th>
            <th class="text-center" style="min-width: 14rem;">Details</th>
            <th class="text-center" style="min-width: 12.5rem;">Supervisor</th>
            <th class="text-center" style="min-width: 12.5rem;">HOD</th>
        </tr>
        </thead>
        <tbody>
            <tr v-for="request in leaveBalance" :key="request.id">
            <td class="text-start text-capitalize">
                <div class="d-flex flex-column">
                    {{ request.leave_type_name }}
                    <small class="text-muted">Applied on: {{ request.leave_created }}</small>
                    <small class="text-muted">Status:
                        <span class="badge text-capitalize"
                              :class="{ 'bg-secondary': request.overall_status === 'pending',
                                    'bg-success': request.overall_status === 'approved',
                                    'bg-warning': request.overall_status === 'canceled',
                                    'bg-danger': request.overall_status === 'rejected',
                                }"
                        >{{ request.overall_status }}</span>
                    </small>
                </div>
            </td>
            <td class="text-center">
                <div class="d-flex flex-column align-items-start justify-content-center" style="font-size: 0.8rem;">
                    <span class="mb-0"><span class="fw-light">Start Date:</span> {{ request.start_date }} <span class="text-capitalize">({{ request.start_date_selected }})</span></span>
                    <span class="mb-0"><span class="fw-light">End Date:</span> {{ request.end_date }} <span class="text-capitalize">({{ request.end_date_selected }})</span></span>
                    <span class="mb-0"><span class="fw-light">Duration:</span> {{ request.duration }} day(s)</span>
                    <span class="mb-0"><span class="fw-light">Attachment: </span>
                        <a v-if="request.attachment.length > 0" target="_blank" :href="this.attachment_url + request.attachment[0].path">Show Attachment</a>
                        <span class="text-danger" v-else>None</span>
                    </span>
                </div>
            </td>
            <td class="text-center" style="font-size: 0.8rem;">
                <div v-if="request.first_approver" class="d-flex flex-column">
                    {{ request.first_approver.name }}
                    <small>
                            <span class="badge text-capitalize" style="font-size: 0.75rem;"
                                  :class="{ 'bg-secondary': request.first_approver_status === 'pending',
                                            'bg-success': request.first_approver_status === 'approved',
                                            'bg-warning': request.first_approver_status === 'canceled',
                                            'bg-danger': request.first_approver_status === 'rejected',
                                                }"
                            >{{ request.first_approver_status }}</span>
                    </small>
                </div>
                <span class="text-danger" v-else>Not Assigned</span>
            </td>
            <td class="text-center" style="font-size: 0.8rem;">
                <div v-if="request.second_approver" class="d-flex flex-column">
                    {{ request.second_approver.name }}
                    <small class="text-capitalize">
                            <span class="badge" style="font-size: 0.75rem;"
                                  :class="{   'bg-secondary': request.second_approver_status === 'pending',
                                            'bg-success': request.second_approver_status === 'approved',
                                            'bg-warning': request.second_approver_status === 'canceled',
                                            'bg-danger': request.second_approver_status === 'rejected',
                                }"
                            >{{ request.second_approver_status }}</span>
                    </small>
                </div>
                <span class="text-danger" v-else>Not Assigned</span>
            </td>
        </tr>
        </tbody>
    </TableMain>
    <div v-else class="card">
        <EmptyScreen text="You haven't applied any leave"/>
    </div>
</template>
<script>
import TableMain from "../elements/TableMain.vue";
import EmptyScreen from "../elements/EmptyScreen.vue";
import $api from "../api";
export default {
    components: {TableMain, EmptyScreen},

    data() {
        return {
            leaveBalance: [],
            attachment_url: localStorage.getItem('currentUrl') + '/leave-request/',
        };
    },

    created() {
        this.getAllLeaveBalance();
    },

    methods: {
        async getAllLeaveBalance() {
            await $api.get('/api/leave-request/' + parseInt(localStorage.getItem('user_id')) + '/all?page=1')
                .then(response => {
                    this.leaveBalance = response.data.data;
                });
        },
    },
}
</script>
