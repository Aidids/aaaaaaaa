<template>
  <div class="card-header border-0">
    <h4 v-if="request.travel_purpose" class="card-title mb-1">{{ request.department_name }}</h4>

    <div v-else>
      <h4 class="mb-1 card-title">{{ request.project_name }}<span
          class="h6 text-secondary"> ({{ request.project_location }})</span></h4>
      <div class="d-flex justify-content-between align-items-center">
      </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h6 v-if='isApproveView && request.approvers' class="text-secondary mb-0">{{ request.approvers.requester.name }} on
        {{ humanReadableDate(request.created_at) }}</h6>
      <h6 v-else class="text-secondary mb-0">Requested on
        {{ humanReadableDate(request.created_at) }}</h6>
      <span v-if='request.approvers' class="badge text-capitalize"
            :class="getStatusOption(request.approvers.overall_status)">{{
          request.approvers.overall_status
        }}</span>
    </div>

    <div class="p-1 badge border border-secondary-subtle border-2 me-1">
      <i class="bi bi-buildings fst-normal text-secondary" style="font-size: 1rem">
        <span v-if='request.main_office !== undefined' class="ms-1">{{ mainOfficeList[request.main_office].label }}</span>
      </i>
    </div>
    <BooleanBadge :bool-state="request.reimbursement === 1" label="Reimbursable"></BooleanBadge>
    <div class="d-flex justify-content-between mt-3">
      <a class="text-primary fw-bold" target="#" @click="$emit('viewAttachment', false)" style="cursor:pointer;">
        <i class="bi bi-paperclip" style="font-size: 1rem;"/>Attachment
      </a>
      <a class="text-primary fw-bold" target="#" @click="$emit('viewAttachment', true)" style="cursor:pointer;">
        <i class="bi bi-paperclip" style="font-size: 1rem;"/>HR Attachment
      </a>
    </div>
    <div v-if="isViewMore" class="mb-2 p-2 border-2 rounded border-secondary-subtle h-100">
      <h6 class="text-secondary mb-0">Purpose</h6>
      <p class="text-justify" style="overflow-wrap: break-word;">{{ request.purpose }}</p>
    </div>
  </div>

</template>

<script>
import BooleanBadge from "../../../elements/BooleanBadge.vue";
import statusOption from "../../../../mixins/statusOption";
import travelOption from "../../../../mixins/travelOption";
import conversion from "../../../../mixins/conversion";

export default {
  components: {BooleanBadge},
  mixins: [statusOption, conversion, travelOption],
  emits: ['viewAttachment'],
  props: {
    request: {
      type: Object,
      default: null
    },
    isViewMore: {
      type: Boolean,
      default: false,
    },
    isApproveView: {
      type: Boolean,
      default: false
    },
  },
  computed: {
    getApprovalProgress() {
      let approvalProgress = [];
      if (this.request.approvers) {
        let approvers = this.request.approvers;
        (approvers.first_approver_status) && approvalProgress.push(approvers.first_approver_status);
        (approvers.second_approver_status) && approvalProgress.push(approvers.second_approver_status);
      }

      return approvalProgress;
    }
  },
}
</script>
