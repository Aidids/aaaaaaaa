<template>
    <div class="grid-leave">
        <h4>Leave entitlement summary</h4>
        <div class="card overflow-y-auto" style="height: 310px;">
            <loader v-if="loadApi"/>
            <template v-else v-for="data in leaveBalance" :key="data.id">
                <LeaveProgressBar :excluded="excludedLeaveTypes.includes(data.leave_type_id)" :title="data.name" :total="data.total" :value="data.balance"/>
            </template>
        </div>
    </div>
</template>
<script>
import { mapState } from "pinia";
import { useLeaveBalance } from "../../stores/getLeaveBalance";
import LeaveProgressBar from "./LeaveProgressBar.vue";

export default {
    components: {LeaveProgressBar},

    data() {
        return {
            excludedLeaveTypes: [4,5,10,11,12]
        }
    },

    created() {
      useLeaveBalance().init();
    },

    computed: {
        ...mapState(useLeaveBalance, ['leaveBalance', 'loadApi'])
    }
}
</script>
