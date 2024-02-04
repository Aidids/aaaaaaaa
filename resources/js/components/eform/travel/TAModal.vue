<template>
  <vue-final-modal
      :click-to-close="true"
      v-slot="{ params, close }"
      v-bind="$attrs"
      classes="modal-container"
      content-class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title">{{ title }}</h5>
      <i class="bi bi-x-circle close-icon" @click="close"/>
    </div>
    <hr>
    <div class="modal-body" style="overflow: auto; height: 70vh; width: 90vw;">
      <div class="d-xl-flex justify-content-around gap-3">
        <div class="col-xl-7 mb-4 mb-xl-0">
          <h5 class="mb-2">Request Details</h5>
          <TACard :request="request" :is-view-more="true" :is-approve-view="true"
                  @viewAttachment="viewAttachment($event)" @action="$emit('action', $event)"/>
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
    </div>
    <div class="modal-footer justify-content-start mt-3">
      <button v-if="isApproveView && request.approvers && request.approvers.overall_status !== 'completed'"
              class="btn btn-success m-1" @click="$emit('action', 'approved')">Approve
      </button>
      <button v-if="isApproveView && request.approvers && request.approvers.overall_status !== 'completed'"
              class="btn btn-danger m-1" @click="$emit('action', 'rejected')">Reject
      </button>
    </div>
  </vue-final-modal>

  <ViewAttachmentModal v-if="request.approvers"
                       title="Travel Authorization Attachments"
                       v-model="showViewAttachmentModal" :attachments="attachments"
                       :attachment_url="attachment_url"/>
</template>

<script>
import TACard from "./card/TACard.vue";
import CommentBox from "../../elements/CommentBox.vue";
import ViewAttachmentModal from "../../elements/attachments/ViewAttachmentModal.vue";
import FormRemarks from "../../form/FormRemarks.vue";

export default {
    components: {FormRemarks, ViewAttachmentModal, CommentBox, TACard},
    emits: ['action'],

    props: {
        request: {
            type: Object,
            default: {}
        },
        title: {
            type: String,
            default: ''
        },
        isApproveView: {
            type: Boolean,
            default: false,
        }
    },
    data() {
        return {
            attachment_url: localStorage.getItem('currentUrl') + '/e-form/travel-authorization/',
            showViewAttachmentModal: false,
            attachments: []
        }
    },
    methods: {
        viewAttachment(attachmentType) {
            this.attachments = this.request.approvers.attachments.filter((attachment) => attachment.hr_upload === attachmentType);
            this.showViewAttachmentModal = !this.showViewAttachmentModal;
        },

    }
}
</script>
