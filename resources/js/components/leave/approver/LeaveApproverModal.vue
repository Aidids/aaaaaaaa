<template>
    <vue-final-modal :click-to-close="false" v-slot="{ params, close }" v-bind="$attrs" classes="modal-container" content-class="modal-content">
            <div class="modal-header">
                <h5>Review Leave</h5>
                <button @click="resetStatus(close)" type="button" class="btn-close"></button>
            </div>
            <div class="modal-body" style="overflow: auto; max-height: 80vh; width: 80vw;">
                <hr>
                <label class="form-label">Leave Information</label>
                <TableMain>
                    <thead>
                        <tr>
                            <th class="text-center" style="min-width: 10rem;">Requested by</th>
                            <th class="text-start" style="min-width: 12rem;">Leave Type</th>
                            <th class="text-center" style="min-width: 14rem;">Details</th>
                            <th class="text-center">Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-left">
                                <div class="media align-items-center">
                                    <div v-if="data.requester" class="ml-3">
                                        <p class="mb-0 text-truncate fw-bold" :title="data.requester.name">{{ data.requester.name }}</p>
                                        <p class="mb-0 text-secondary text-truncate" :title="data.requester.department">{{ data.requester.department }}</p>
                                        <small class="text-muted mb-0">Applied on: {{ data.leave_created }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-start">
                                <div class="d-flex flex-column">
                                    {{ data.leave_type_name }}
                                    <small class="text-muted">Applied on: {{ data.leave_created }}</small>
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
                            <td class="text-center">
                                <div v-if="data.attachment" class="d-flex flex-column align-items-start justify-content-center" style="font-size: 0.8rem;">
                                    <span class="mb-0"><span class="fw-light">Start Date:</span> {{ data.start_date }} <span class="text-capitalize">({{ data.start_date_selected }})</span></span>
                                    <span class="mb-0"><span class="fw-light">End Date:</span> {{ data.end_date }} <span class="text-capitalize">({{ data.end_date_selected }})</span></span>
                                    <span class="mb-0"><span class="fw-light">Duration:</span> {{ data.duration }} day(s)</span>
                                    <span class="mb-0">
                                        <span class="fw-light">Attachment: </span>
                                        <a v-if="data.attachment.length > 0" target="_blank" :href="this.attachment_url + data.attachment[0].path">Show Attachment</a>
                                        <span class="text-danger" v-else>None</span>
                                    </span>
                                </div>
                            </td>
                            <td class="text-center" v-if="data.reason">{{data.reason}}</td>
                            <td class="text-center fw-light text-danger" v-else>No reason added</td>
                        </tr>
                    </tbody>
                </TableMain>
                <label class="form-label">Approvers remarks</label>
                <div class="d-xl-flex mb-2">
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
                <label class="form-label">Change Status</label>
                <div class="form-control pt-3">
                    <div class="form-group mb-3">
                        <label class="form-label">Select Status</label>
                        <select v-model="statusSelected" @change="changeStatus" class="form-control w-50">
                            <option disabled :value="null">Select Status</option>
                            <option v-for="status in statuses" :value="status.value" :key="status">{{ status.name }}</option>
                        </select>
                        <p class="text-secondary mt-1"><i class="bi bi-exclamation-circle text-warning me-1">  Reminder :</i> Status assigned is irreversible & please be aware if the requester has assigned the appropriate approver(s).</p>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Remark</label>
                        <textarea v-model="remark" cols="30" rows="3" class="form-control" style="resize: none;"
                                  placeholder="You can leave additional remark regarding your status choices (optional)">
                        </textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer mt-4">
                <button @click="resetStatus(close)" type="button" class="btn btn-secondary me-2">Cancel</button>
                <button :disabled="disableModal" @click="confirm(close)" type="submit" class="btn text-center btn-success">Save</button>
            </div>
    </vue-final-modal>
</template>

<script>
import $api from "../../api";
import TableMain from "../../elements/TableMain.vue";
import CommentBox from "../../elements/CommentBox.vue";

export default {
    components: {TableMain, CommentBox},
    inheritAttrs: false,
    props: ['data', 'approver_level', 'user_id', 'attachment_url'],
    data() {
        return {
            disableModal: true,
            statusSelected: null,
            remark: null,
            statuses: [
                {name: 'Approve Leave', value: 'approved'},
                {name: 'Reject Leave', value: 'rejected'},
            ],
        }
    },
    watch: {
        statusSelected: {
            handler (n,o) {
                this.disableModal = n === null;
            }
        }
    },
    methods: {
        async confirm(close) {
            const dateTime = new Date();
            let year = dateTime.getFullYear();
            let month = (dateTime.getMonth() + 1).toString().padStart(2, '0');
            let day = dateTime.getDate().toString().padStart(2, '0');
            let today = `${year}-${month}-${day}`;

            let formData = new FormData();
            formData.append('id', this.data.id);
            formData.append(this.approver_level + '_id', this.statusSelected);
            formData.append(this.approver_level + '_status', this.statusSelected);
            formData.append(this.approver_level + '_remark', this.remark);
            formData.append(this.approver_level + '_date', today);

            await $api.post('/api/leave-request/' + this.user_id + '/approve', formData)
                .then(response => {
                    this.$parent.getPendingLeave();
                    this.$parent.messageMethod(response.data.message);
                    this.resetStatus(close);
                });
        },
        resetStatus(close) {
            this.statusSelected = null;
            this.remark = null;
            close();
        }
    },

    computed: {
    }
}
</script>
