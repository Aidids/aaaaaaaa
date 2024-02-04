<template>
    <vue-final-modal :click-to-close="true" v-slot="{ params, close }" v-bind="$attrs" classes="modal-container" content-class="modal-content">
        <div class="modal-header">
            <h5 v-if="data.requester">{{data.requester.name}} leave request</h5>
            <h5 v-else>View Leave Request</h5>
            <button @click="close" type="button" class="btn-close"></button>
        </div>
        <div class="modal-body mt-1" style="overflow: auto; max-height:80vh; width:90vw">
            <hr>
            <label class="form-label mb-2">Leave Details</label>
            <TableMain>
                <thead>
                <tr>
                    <th class="text-start">Leave Type</th>
                    <th class="text-start">Details</th>
                    <th class="text-center">Reason</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="text-start">
                        <div class="d-flex flex-column">
                            {{ data.leave_type_name }}
                            <small class="text-muted">Applied on: {{ displayDate(data.leave_created) }}</small>
                            <small class="text-muted">Status:
                                <span class="badge text-capitalize"
                                      :class="{ 'bg-secondary': data.overall_status === 'pending',
                                    'bg-success': data.overall_status === 'approved',
                                    'bg-warning': data.overall_status === 'canceled',
                                    'bg-danger': data.overall_status === 'rejected',
                                }"
                                >{{ data.overall_status }}</span>
                            </small>
                        </div>
                    </td>
                    <td class="text-start">
                        <div v-if="data.attachment" class="d-flex flex-column align-items-start justify-content-center" style="font-size: 0.8rem;">
                            <span class="mb-0"><span class="fw-light">Start Date:</span> {{ displayDate(data.start_date, data.start_date_selected) }}</span>
                            <span class="mb-0"><span class="fw-light">End Date:</span> {{ displayDate(data.end_date, data.end_date_selected) }}</span>
                            <span class="mb-0"><span class="fw-light">Duration:</span> {{ data.duration }} day(s)</span>
                            <span class="mb-0">
                                <span class="fw-light">Attachment: </span>
                                <a v-if="data.attachment.length > 0" target="_blank" :href="this.attachment_url + data.attachment[0].path">Show Attachment</a>
                                <span class="text-danger" v-else>None</span>
                            </span>
                            <span v-if="! data.first_approver && ! data.second_approver" class="badge bg-danger me-2 mb-1">Applied by HR</span>
                            <span v-if="data.deduct_type" class="badge bg-danger">Balance taken from {{data.deduct_type}} Leave</span>
                        </div>
                    </td>
                    <td class="text-center" v-if="data.reason">{{data.reason}}</td>
                    <td class="text-center fw-light text-danger" v-else>No reason added</td>
                </tr>
                </tbody>
            </TableMain>
            <label class="form-label">Approvers remarks</label>
            <div class="d-xl-flex">
                <CommentBox
                    label="Assigned supervisor"
                    style-class="me-xl-1"
                    :user="data.first_approver"
                    :remark="data.first_approver_remark"
                    :status="data.first_approver_status"
                    :date="data.first_approver_date"
                />
                <CommentBox
                    label="Head of department"
                    style-class="ms-xl-1"
                    :user="data.second_approver"
                    :remark="data.second_approver_remark"
                    :status="data.second_approver_status"
                    :date="data.second_approver_date"
                />
            </div>
        </div>
    </vue-final-modal>
</template>

<script>
import TableMain from "../elements/TableMain.vue";
import CommentBox from "../elements/CommentBox.vue";
import calculateWorkDay from "../../mixins/calculateWorkDay";
export default {
    inheritAttrs: false,
    emits: ['confirm', 'cancel'],
    props: ['data'],
    components: {TableMain, CommentBox},
    mixins: [calculateWorkDay],
    data() {
        return {
            attachment_url : localStorage.getItem('currentUrl') + '/leave-request/',
        }
    }
}
</script>
