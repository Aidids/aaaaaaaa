<template>
    <Form/>
    <h4>History</h4>
    <div class="w-100">
        <TableMain>
            <thead>
            <tr>
                <th style="min-width: 12rem">Staff</th>
                <th style="min-width: 15rem">Leave Balance Details</th>
                <th style="min-width: 12rem">PIC</th>
                <th style="min-width: 12rem">Remarks</th>
            </tr>
            </thead>
            <tbody v-if="leaveAddOnHistory.length > 0">
            <tr v-for="data in leaveAddOnHistory" :key="data.id" class="text-center">
                <td class="text-start">
                    <TableProfile :user="data.user_selected" :navigate="true"/>
                </td>
                <td>
                    <div class="d-flex flex-column align-items-start">
                        <p class="mb-0">Leave Type: <span class="fw-bold">{{ data.leave_balance.leave_type_resource.name }}</span></p>
                        <p class="mb-0">Added Balance: <span class="fw-bold"></span>{{ data.added_qty }} day(s)</p>
                        <p class="mb-0">New Balance: <span class="fw-bold"></span>{{ data.new_balance }} day(s)</p>
                    </div>
                </td>
                <td>
                    <div class="d-flex flex-column align-items-start">
                        <span class="mb-0"><span class="fw-light">PIC: </span><span class="fw-bold">{{ data.person_in_charge.name }}</span></span>
                        <span class="mb-0"><span class="fw-light">Created at:</span> {{data.addon_created}}</span>
                    </div>
                </td>
                <td class="text-start">
                    {{ data.remark }}
                </td>
            </tr>
            </tbody>
        </TableMain>
    </div>
    <Pagination
        :perPage="10"
        :totalPages="totalPages"
        :currentPage="currentPage"
        @pagechanged="onPageChange"
    />
</template>


<script>
import ApiErrorModal from "../../elements/ApiErrorModal.vue";
import Form from "./Form.vue";
import Pagination from "../../elements/Pagination.vue";
import TableMain from "../../elements/TableMain.vue";
import TableProfile from "../../elements/TableProfile.vue";
import {mapState, mapActions} from "pinia";
import {useLeaveAddOnStore} from "../../../stores/getLeaveAddOnHistory";

export default {
    components: {Pagination, ApiErrorModal, Form, TableMain, TableProfile},

    created() {
        useLeaveAddOnStore().init();
    },

    methods: {
        onPageChange(page) {
            useLeaveAddOnStore().currentPage = page;
            useLeaveAddOnStore().getLeaveAddOnHistory();
        }
    },

    computed: {
        ...mapState(useLeaveAddOnStore, ['leaveAddOnHistory', 'currentPage', 'totalPages']),

        ...mapActions(useLeaveAddOnStore, ['getLeaveAddOnHistory']),
    }
}

</script>
