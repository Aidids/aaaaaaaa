<template>
    <h5 class="my-2">Leave Allowance</h5>
    <hr>
    <div class="card-policy mb-4">
        <div class="d-flex mb-3">
            <i class="bi bi-bookmarks fa-lg me-2" style="color: orange;"></i>
            <h6>General Leave Policy</h6>
        </div>
        <p class="m-1">
            1. Leftover leaves from the previous year will be carry forward on the 1st of January
            <br>
            2. The amount you can carry forward is limited based on your terms of service
            <br>
            3. Carry forward will expiry by 1st July
            <br>
            4. Your leave will be assign once HR has configure your joining date profile
            <br>
            5. If you don't have a leave or your leave balance amount is incorrect, please report to HR
        </p>
    </div>

    <TableMain>
        <thead>
        <tr>
            <th class="text-start" style="min-width: 180px;">Leave type</th>
            <th class="text-center" style="min-width: 150px;">Entitlement Earned</th>
            <th class="text-center" style="min-width: 150px;">Pro-rated Earned</th>
            <th class="text-center" style="min-width: 150px;">Carry Forward Balance</th>
            <th class="text-center" style="min-width: 75px;">Taken</th>
            <th class="text-center" style="min-width: 75px;">Balance</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="leaveBalance in leaveBalanceApi" :key="leaveBalanceApi.id">
            <td class="text-left">{{ leaveBalance.name }}</td>

            <td class="text-center">
                <div v-if="leaveBalance.leave_type_id === 1 || leaveBalance.leave_type_id === 2">
                    {{ leaveBalance.entitlement }}
                </div>
                <div class="text-muted" v-else>None</div>
            </td>
            <td class="text-center">
                <div v-if="leaveBalance.leave_type_id === 1">{{ leaveBalance.proRated }}</div>
                <div class="text-muted" v-else>None</div>
            </td>
            <td class="text-center">
                <div v-if="leaveBalance.leave_type_id === 1">{{ leaveBalance.carry_forward }}</div>
                <div class="text-muted" v-else>None</div>
            </td>
            <td class="text-center text-danger fw-bold">{{ leaveBalance.taken }}</td>
            <td class="text-center text-success fw-bold">
                <div v-if="![4,5,11,12].includes(leaveBalance.leave_type_id)">
                    {{leaveBalance.balance}}
                </div>
                <div v-else class="text-secondary">
                    N/A
                </div>
            </td>
        </tr>
        </tbody>
    </TableMain>
</template>

<script>
import $api from "../../api";
import TableMain from '../../elements/TableMain.vue';

export default {
    components: {TableMain},
    props: ['user_id'],
    data() {
        return {
            'leaveBalanceApi': [],
        };
    },
    methods: {
        async getLeaveBalanceAPI() {
            await $api.get('/api/leave-balance/' + this.user_id)
                .then(response => {
                    this.leaveBalanceApi = response.data.data;
                });
        }
    },
    created() {
        this.getLeaveBalanceAPI()
    }
}
</script>

