<script>
import TACard from "./card/TACard.vue";
import CommentBox from "../../elements/CommentBox.vue";
import $api from "../../api";
import ViewAttachmentModal from "../../elements/attachments/ViewAttachmentModal.vue";
import Button from "primevue/button";

export default {
    components: {ViewAttachmentModal, CommentBox, TACard, Button},
    props: {
        form_id: {
            type: Number,
            default: null,
        }
    },
    data() {
        return {
            request: {},
            attachments: [],
            attachment_url: localStorage.getItem('currentUrl') + '/e-form/travel-authorization/',
            showViewAttachmentModal: false
        }
    },
    created() {
        if (this.form_id) {
            this.showTravelAPI(this.form_id)
        }
    },
    methods: {
        printPage() {
            window.print();
        },

        viewAttachment(attachmentType) {
            this.attachments = this.request.approvers.attachments.filter((attachment) => attachment.hr_upload === attachmentType);
            this.showViewAttachmentModal = !this.showViewAttachmentModal;
        },

        async showTravelAPI(id) {
            await $api.get('/api/travel-authorization/' + id)
                .then(response => {
                    this.request = response.data.data;
                });
        },
    }
}
</script>

<template>
    <hr>
  <div class="d-flex justify-content-end">
    <Button type="button" label="Print" icon="bi bi-printer-fill" @click="printPage"/>
  </div>
    <div class="d-xl-flex justify-content-around gap-3 mx-2">
        <div class="col-xl-7 mb-4 mb-xl-0">
            <h5 class="mb-2">Request Details</h5>
            <TACard :request="request" :is-view-more="true" :is-approve-view="true"
                    @viewAttachment="viewAttachment($event)"/>
        </div>
        <div v-if='this.request.approvers' class="col-xl-5">
            <h5 class="mb-2">Approver's Remark</h5>
            <CommentBox
                label="Supervisor"
                style-class="ms-xl-1"
                :user="this.request.approvers.first_approver"
                :remark="this.request.approvers.first_approver_remark"
                :status="this.request.approvers.first_approver_status"
                :date="this.request.approvers.first_approver_date"/>
            <CommentBox
                label="Head of department"
                style-class="ms-xl-1"
                :user="this.request.approvers.second_approver"
                :remark="this.request.approvers.second_approver_remark"
                :status="this.request.approvers.second_approver_status"
                :date="this.request.approvers.second_approver_date"/>
            <CommentBox
                label="Human Resource In-Charge"
                style-class="ms-xl-1"
                :user="this.request.approvers.hrIncharge"
                :remark="this.request.approvers.hr_ic_remark"
                :status="this.request.approvers.hr_ic_status"
                :date="this.request.approvers.hr_ic_date"/>
        </div>
    </div>

    <ViewAttachmentModal v-if="request.approvers"
                         v-model="showViewAttachmentModal"
                         title="Travel Authorization Attachments"
                         :attachments="attachments"
                         :attachment_url="attachment_url"/>
</template>

