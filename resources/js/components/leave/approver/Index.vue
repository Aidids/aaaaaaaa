<template>
    <h4 class="mb-4">Review Leave</h4>
    <div v-if="leaveRequestData.length > 0 " class="table-responsive custom-scrollbar table-view-responsive px-0">
        <TableMain>
            <thead>
            <tr>
                <th class="text-center">Action</th>
                <th class="text-center" style="min-width: 10rem;">Requested by</th>
                <th class="text-start" style="min-width: 12rem;">Leave Type</th>
                <th class="text-center" style="min-width: 14rem;">Details</th>
                <th class="text-center" style="min-width: 12.5rem;">Supervisor</th>
                <th class="text-center" style="min-width: 12.5rem;">HOD</th>
            </tr>
            </thead>
            <tbody>
                <tr v-for="data in leaveRequestData" :key="data.id">
                <td class="text-center">
                    <button v-if="isReviewed(data)"
                            @click="reviewLeave(data)" class="btn btn-success">Review</button>
                    <button v-else @click="showLeave(data)" class="btn btn-outline-secondary">View</button>
                </td>
                <td class="text-left">
                    <TableProfile :user="data.requester"/>
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
                    <div class="d-flex flex-column align-items-start justify-content-center" style="font-size: 0.8rem;">
                        <span class="mb-0"><span class="fw-light">Start Date:</span> {{ data.start_date }} <span class="text-capitalize">({{ data.start_date_selected }})</span></span>
                        <span class="mb-0"><span class="fw-light">End Date:</span> {{ data.end_date }} <span class="text-capitalize">({{ data.end_date_selected }})</span></span>
                        <span class="mb-0"><span class="fw-light">Duration:</span> {{ data.duration }} day(s)</span>
                        <span class="mb-0"><span class="fw-light">Attachment: </span>
                            <PreviewAttachment v-if="data.attachment.length > 0" :href="this.attachment_url + data.attachment[0].path"/>
                            <span class="text-danger" v-else>None</span>
                        </span>
                    </div>
                </td>
                <td class="text-center" style="font-size: 0.8rem;">
                    <div v-if="data.first_approver" class="d-flex flex-column">
                        {{ data.first_approver.name }}
                        <small class="text-capitalize">
                            <span class="badge" style="font-size: 0.75rem;" :class="{ 'bg-secondary': data.first_approver_status === 'pending',
                                                                'bg-success': data.first_approver_status === 'approved',
                                                                'bg-warning': data.first_approver_status === 'canceled',
                                                                'bg-danger': data.first_approver_status === 'rejected',
                                                    }"
                            >{{ data.first_approver_status }}</span>
                        </small>
                    </div>
                    <span class="text-muted" v-else>Not Assigned</span>
                </td>
                <td class="text-center" style="font-size: 0.8rem;">
                    <div v-if="data.second_approver" class="d-flex flex-column">
                        {{ data.second_approver.name }}
                        <small class="text-capitalize">
                            <span class="badge" style="font-size: 0.75rem;" :class="{ 'bg-secondary': data.second_approver_status === 'pending',
                                                                'bg-success': data.second_approver_status === 'approved',
                                                                'bg-warning': data.second_approver_status === 'canceled',
                                                                'bg-danger': data.second_approver_status === 'rejected',
                                                    }"
                            >{{ data.second_approver_status }}</span>
                        </small>
                    </div>
                    <span class="text-muted" v-else>Not Assigned</span>
                </td>
            </tr>
            </tbody>
        </TableMain>
    </div>
    <EmptyScreen v-else text="There are no leave request assigned to you."/>
    <Pagination
        :perPage="10"
        :totalPages="this.totalPages"
        :currentPage="this.currentPage"
        @pagechanged="onPageChange"
    />
    <LeaveApproverModal v-model="showLeaveApproverModal" :data="selectedLeaveRequestData"
                        :approver_level="approver_level" :user_id="user_id" :attachment_url="attachment_url"
    />
    <ViewLeaveModal v-model="showViewLeaveModal" :data="selectedLeaveRequestData"/>
    <msg-modal :message="message" v-model="showMessageModal" @cancel="closeModal"/>

</template>
<script>
import LeaveApproverModal from "./LeaveApproverModal.vue";
import ViewLeaveModal from "../ViewLeaveModal.vue";
import $api from "../../api";
import Pagination from "../../elements/Pagination.vue";
import TableMain from "../../elements/TableMain.vue";
import TableProfile from "../../elements/TableProfile.vue";
import EmptyScreen from "../../elements/EmptyScreen.vue";
import PreviewAttachment from "../../elements/attachments/PreviewAttachment.vue";

export default {
    components: {LeaveApproverModal, ViewLeaveModal, Pagination, TableMain, EmptyScreen, TableProfile, PreviewAttachment},
    data() {
        return {
            user_id: null,
            attachment_url: null,
            approver_id: null,
            approver_level: null,
            message: null,
            leaveRequestData: [],
            selectedLeaveRequestData: [],
            showLeaveApproverModal: false,
            showViewLeaveModal: false,
            showMessageModal: false,
            totalPages: 1,
            currentPage: 1
        };
    },
    created() {
        let url = window.location.href;
        let base_url = url.split('/')[2];

        this.user_id = url.split('/')[4];
        // TODO change to https
        this.attachment_url = 'http://' + base_url + '/leave-request/';
        this.getApproverLevel(this.user_id);
        this.getPendingLeave();
    },
    methods: {
        closeModal(close){
            close();
        },
        messageMethod(msg){
            this.message = msg;
            this.showMessageModal = !this.showMessageModal;
        },
        onPageChange(page) {
            this.pageLoad = !this.pageLoad;
            this.currentPage = page;
            this.getPendingLeave();
        },
        reviewLeave(data) {
            this.selectedLeaveRequestData = data;
            this.showLeaveApproverModal = !this.showLeaveApproverModal;
        },
        showLeave(data) {
            this.selectedLeaveRequestData = data;
            this.showViewLeaveModal = !this.showViewLeaveModal;
        },
        isReviewed(data) {
            if (data.overall_status === 'rejected') {
                return false;
            }

            if (this.approver_id === 1) {
                if (data.first_approver_status === 'pending') {
                    return true;
                }
            } else {
                if (data.second_approver_status === 'pending') {
                    return true;
                }
            }

            return false;
        },
        async getApproverLevel(userId) {
            await $api.get('/api/approver/' + userId)
                .then(response => {
                    this.approver_level = response.data.data.approver_level;
                    this.approver_id = response.data.data.approver_id;
                });
        },
        async getPendingLeave() {
            await $api.get('/api/leave-request/' + this.user_id + '/approve?page=' + this.currentPage)
                .then(response => {
                    this.leaveRequestData = response.data.data;
                    this.totalPages = response.data.meta.last_page;
                });
        },
    },
    computed: {

    }
}
</script>
