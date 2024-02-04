<template>
  <vue-final-modal :click-to-close="true" v-slot="{ params, close }" v-bind="$attrs" classes="modal-container"
                   content-class="modal-content">
    <div class="modal-header">
      <h5  class="modal-title">{{ this.isApproveView ? data.requester.name : '' }} Leave Redemption Details</h5>
      <i class="bi bi-x-circle close-icon" @click="close"/>
    </div>
    <div class="modal-body" style="overflow: auto; max-height:80vh; width:90vw">
      <hr>
      <h5 class="mb-2">Request Details</h5>
      <TableMain>
        <thead>
        <tr>
          <th class="text-start" style="min-width: 12rem;">Requested by</th>
          <th class="text-start" style="min-width: 10rem;">Requested created</th>
          <th class="text-start" style="min-width: 14rem;">Details</th>
          <th class="text-start" style="min-width: 14rem;">Remark</th>
        </tr>
        </thead>
        <tbody>
        <tr>
          <td class="text-left">
            <TableProfile :user="data.requester ?? profile"/>
          </td>
          <td class="text-start">
            <p>{{ data.request_created }}</p>
            <div v-if="data.overall_status === 'approved'" class="badge bg-info">
              Pending HR Approval
            </div>
            <div v-else class="badge text-capitalize"
                 :class="getStatusOption(data.overall_status)"
            >{{ data.overall_status }}
            </div>
          </td>
          <td>
            <div class="d-flex flex-column align-items-start justify-content-center">
              <small class="text-muted">Start Date: <span class="fw-bold"> {{ data.start_date }}</span></small>
              <small class="text-muted">End Date: <span class="fw-bold"> {{ data.end_date }}</span></small>
              <small v-if="data.added_qty || data.balance_received" class="text-muted">HR Approved Qty: <span
                  class="fw-bold"> {{ data.added_qty ?? data.balance_received }} day(s)</span></small>
              <small v-if="data.balance_qty" class="text-muted">Balance Qty: <span
                  class="fw-bold"> {{ data.balance_qty }} day(s)</span></small>
              <a class="text-primary fw-bold" target="#" @click="this.$parent.viewAttachment(data)" style="cursor:pointer;">
                <i class="bi bi-paperclip" style="font-size: 1rem;"/>Attachment</a>
            </div>
          </td>
          <td>{{ data.remark ?? 'No remark added' }}</td>
        </tr>
        </tbody>
      </TableMain>
      <h5 class="mb-2">Approvers remarks</h5>
      <div class="d-xl-flex">
        <CommentBox
            label="Assigned Supervisor"
            :user="data.first_approver"
            :remark="data.first_approver_remark"
            :status="data.first_approver_status"
            :date="data.first_approver_date"
        />
        <CommentBox
            label="Head of Department"
            style-class="ms-xl-1"
            :user="data.second_approver"
            :remark="data.second_approver_remark"
            :status="data.second_approver_status"
            :date="data.second_approver_date"
        />
        <CommentBox
            label="Human Resource In-Charge"
            style-class="ms-xl-1"
            :user="data.hr_incharge"
            :remark="data.hr_ic_remark"
            :status="data.hr_ic_status"
            :date="data.hr_ic_date"
        />
      </div>
    </div>
    <div v-if="this.isApproveView" class="modal-footer justify-content-start mt-2">
      <button class="btn btn-success" @click="$emit('action', 'approved')">Approve</button>
      <button class="btn btn-danger ms-2" @click="$emit('action', 'rejected')">Reject</button>
    </div>
  </vue-final-modal>
</template>

<script>
import TableMain from "../../elements/TableMain.vue"
import TableProfile from "../../elements/TableProfile.vue";
import CommentBox from "../../elements/CommentBox.vue";
import {mapState} from "pinia";
import {useProfileStore} from "../../../stores/getProfile";
import statusOption from "../../../mixins/statusOption";

export default {
  inheritAttrs: false,
  components: {TableMain, TableProfile, CommentBox},
  emits: ['action'],
  mixins: [statusOption],
  props: {
    data: {
      type: Object,
      default: {}
    },
    isApproveView: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    ...mapState(useProfileStore, ['profile'])
  }
}
</script>
