<template>
  <Toast/>
  <h4 class="mb-4">Leave History ({{new Date().getFullYear()}})</h4>
  <EmptyState
      v-if="leaveRequestData.length === 0"
      title="Your leave history is empty"/>
  <div v-else>
    <TableMain>
      <thead>
      <tr>
        <th class="text-start" style="min-width: 12rem">Leave Type</th>
        <th class="text-center" style="min-width: 14rem">Details</th>
        <th class="text-center" style="min-width: 12.5rem">Supervisor</th>
        <th class="text-center" style="min-width: 12.5rem">HOD</th>
        <th class="text-center">Action</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="request in leaveRequestData" :key="request.id">
        <td class="text-start text-capitalize">
          <div class="d-flex flex-column">
            {{ request.leave_type_name }}
            <small class="text-muted">Applied on: {{ this.displayDate(request.leave_created) }}</small>
            <small class="text-muted">Leave deducted:
              <span
                  class="text-capitalize"
                  :class="{
                    'text-danger': (!request.calculated),
                    'text-success': (request.calculated)
                  }">{{ request.calculated ? 'Yes' : 'No' }}</span>
            </small>
            <small class="text-muted">Status:
              <span
                  class="badge text-capitalize"
                  :class="getStatusOption(request.overall_status)">{{ request.overall_status }}</span>
            </small>
          </div>
        </td>
        <td class="text-start">
          <div class="d-flex flex-column align-items-start justify-content-center" style="font-size: 0.8rem">
            <span class="mb-0 text-capitalize">
              <span class="fw-light">Start Date:</span>
              {{ this.displayDate(request.start_date, request.start_date_type) }}
            </span>
            <span class="mb-0 text-capitalize">
              <span class="fw-light">End Date:</span>
              {{ this.displayDate(request.end_date, request.end_date_type) }}
            </span>
            <span class="mb-0">
              <span class="fw-light">Duration:</span>
              {{ request.duration }} day(s)
            </span>
            <span class="mb-0">
              <span class="fw-light">Attachment: </span>
              <PreviewAttachment
                v-if="request.attachment.length > 0"
                :href="request.attachment[0].path"
              />
              <span class="text-danger" v-else>None</span>
            </span>
            <span v-if="! request.first_approver && ! request.second_approver" class="badge bg-danger me-2 mb-1">Applied
              by HR</span>
            <span v-if="request.deduct_type" class="badge bg-danger">Balance taken from {{ request.deduct_type }}
              Leave</span>

          </div>
        </td>
        <td class="text-center" style="font-size: 0.8rem">
          <div v-if="request.first_approver" class="d-flex flex-column">
            {{ request.first_approver.name }}
            <small>
              <span
                  class="badge text-capitalize"
                  style="font-size: 0.75rem"
                  :class="getStatusOption(request.first_approver_status)">{{ request.first_approver_status }}</span>
            </small>
          </div>
          <span class="text-danger" v-else>Not Assigned</span>
        </td>
        <td class="text-center" style="font-size: 0.8rem">
          <div v-if="request.second_approver" class="d-flex flex-column">
            {{ request.second_approver.name }}
            <small class="text-capitalize">
              <span
                  class="badge"
                  style="font-size: 0.75rem"
                  :class="getStatusOption(request.second_approver_status)">{{ request.second_approver_status }}</span>
            </small>
          </div>
          <span class="text-danger" v-else>Not Assigned</span>
        </td>
        <td class="text-center">
          <button
              v-if="request.overall_status === 'pending' && request.leave_type_id !== 10"
              @click="editRequest(request)"
              class="btn btn-outline-success m-1">
            Edit
          </button>
          <button
              v-else
              @click="viewRequest(request)"
              class="btn btn-outline-secondary m-1">
            View
          </button>
          <button
              v-if="
                isAfterToday(request.start_date) &&
                (request.overall_status === 'approved' ||
                  request.overall_status === 'pending')"
              @click="cancelRequest(request)"
              class="btn btn-outline-danger m-1">
            Cancel
          </button>
        </td>
      </tr>
      </tbody>
    </TableMain>
    <Pagination
        :perPage="5"
        :totalPages="this.totalPages"
        :currentPage="this.currentPage"
        @pagechanged="onPageChange"/>
  </div>

  <LeaveRequestModal
      v-if="showLeaveRequestModal"
      :form="leaveRequestModal"
      :attributesProp="attributes"
      v-model="showLeaveRequestModal"
      :leaveBalance="leaveBalanceData"
      @confirm="postLeaveRequestAPI"
      @cancel="cancel"/>
  <Modal v-model="modal.show" :modal="modal" :is-close="modal.isClose" :has-confirm="modal.hasConfirm" @confirm="confirmModalController"/>
  <ViewLeaveModal v-model="showViewLeaveModal" :data="leaveRequestModal"/>
</template>


<script>
import $api from "../api";
import LeaveRequestModal from "./LeaveRequestModal.vue";
import Pagination from "../elements/Pagination.vue";
import ViewLeaveModal from "./ViewLeaveModal.vue";
import TableMain from "../elements/TableMain.vue";
import calculateWorkDay from '../../mixins/calculateWorkDay';
import Modal from "../elements/Modal.vue";
import EmptyState from "../elements/EmptyState.vue";
import Toast from "primevue/toast";
import {mapState} from "pinia";
import {useModalStore} from "../../stores/modal";
import statusOption from "../../mixins/statusOption";
import PreviewAttachment from "../elements/attachments/PreviewAttachment.vue";

export default {
  components: {
    EmptyState,
    LeaveRequestModal,
    Pagination,
    ViewLeaveModal,
    TableMain,
    Modal,
    Toast,
    PreviewAttachment
  },
  mixins: [calculateWorkDay, statusOption],
  data() {
    return {
      user_id: null,
      leaveRequestData: {},
      showLeaveRequestModal: false,
      showViewLeaveModal: false,
      confirmType: '',
      leaveBalanceData: [],
      leaveRequestModal: [],
      currentPage: 1,
      totalPages: 1,
    };
  },
  created() {
    useModalStore().init()
    this.user_id = localStorage.getItem("user_id");
    this.getAllLeaveRequest();
    this.getAllLeaveBalance();
  },
  methods: {
    isAfterToday(date) {
      // Convert the date string to a Date object
      const dateObj = new Date(date);
      // Get today's date (23 Jun 2023)
      const today = new Date();
      // Compare the two dates
      return dateObj > today;
    },

    cancel(close) {
      close();
    },

    onPageChange(page) {
      this.currentPage = page;
      this.leaveRequestPagination();
    },

    editRequest(request) {
      this.leaveRequestModal = request;
      this.leaveRequestModal.isEdit = true;
      this.leaveRequestModal.hasAttachment = request.attachment.length > 0;
      this.setCalendarRange(this.leaveRequestModal.start_date, this.leaveRequestModal.end_date, true);
      useModalStore().confirm('Editing leave will reset the approver status. Would you like to proceed?')
      this.confirmType = 'edit';
    },

    viewRequest(request) {
      this.leaveRequestModal = request;
      this.showViewLeaveModal = !this.showViewLeaveModal;
    },

    cancelRequest(request) {
      this.leaveRequestModal = request;
      useModalStore().confirm('Canceling your leave is irreversible. Do you want to proceed?')
      this.confirmType = 'cancel';
    },

    confirmModalController(close) {
      if (this.confirmType === 'edit') {
        this.showLeaveRequestModal = !this.showLeaveRequestModal;
      } else if (this.confirmType === 'cancel') {
        this.cancelLeaveRequestApi();
      }
      close();
    },

    async postLeaveRequestAPI() {
      useModalStore().load()

      let formData = new FormData();
      formData.append("id", this.leaveRequestModal.id);
      formData.append("leave_balance_id", this.leaveRequestModal.leave_balance_id);
      formData.append("duration", this.leaveRequestModal.duration);
      formData.append("start_date", this.convertDate(this.leaveRequestModal.start_date));
      formData.append("start_date_type", this.leaveRequestModal.start_date_type);
      formData.append("end_date", this.convertDate(this.leaveRequestModal.end_date));
      formData.append("end_date_type", this.leaveRequestModal.end_date_type);

      if (this.leaveRequestModal.first_approver) {
        formData.append("first_approver_id", this.leaveRequestModal.first_approver.id);
        formData.append("first_approver_status", "pending");
      }

      if (this.leaveRequestModal.second_approver) {
        formData.append("second_approver_id", this.leaveRequestModal.second_approver.id);
        formData.append("second_approver_status", "pending");
      }

      if (this.leaveRequestModal.file) {
        formData.append("file", this.leaveRequestModal.file);
      }

      if (this.leaveRequestModal.hasAttachment) {
        formData.append("file_id", this.leaveRequestModal.attachment[0].id);
      }

      if (this.leaveRequestModal.reason) {
        formData.append("reason", this.leaveRequestModal.reason);
      }
      let url = this.leaveRequestModal.leave_type_id === 10 ? 'compassionate-leave/' : 'leave-request/'
      await $api
          .post("/api/" + url + this.user_id, formData)
          .then((response) => {
            useModalStore().finishLoad()
            this.getAllLeaveRequest()
            this.$toast.add(
                {
                  severity: 'success',
                  summary: 'Success',
                  detail: response.data.message,
                  life: 3000
                }
            )
          })
          .catch((error) => {
            useModalStore().show(error.response.data.message)
          });
      this.showLeaveRequestModal = false;
    },

    async cancelLeaveRequestApi() {
      useModalStore().load()
      let formData = new FormData();
      formData.append("id", this.leaveRequestModal.id);

      await $api
          .post("/api/leave-request/" + this.user_id + "/cancel", formData)
          .then((response) => {
            useModalStore().finishLoad()
            this.getAllLeaveRequest();
            this.$toast.add(
                {
                  severity: 'success',
                  summary: 'Success',
                  detail: response.data.message,
                  life: 3000
                }
            )
          })
          .catch((error) => {
            console.log(error);
          });
    },

    async getAllLeaveRequest() {
      await $api
          .get("/api/leave-request/" + this.user_id + "/all?page=1")
          .then((response) => {
            this.leaveRequestData = response.data.data;
            this.totalPages = response.data.meta.last_page;
          });
    },

    async getAllLeaveBalance() {
      await $api.get("/api/leave-request/" + this.user_id).then((response) => {
        this.leaveBalanceData = response.data.data;

        let unpaidLeaveType = this.leaveBalanceData.findIndex(
            (obj) => obj.leave_type_id === 4
        );

        this.leaveBalanceData.forEach((obj) => {
          if (obj.leave_type_id === 1 && obj.balance > 0) {
            this.leaveBalanceData.splice(unpaidLeaveType, 1);
          }
        });
      });
    },

    async leaveRequestPagination() {
      await $api
          .get(
              "/api/leave-request/" + this.user_id + "/all?page=" + this.currentPage
          )
          .then((response) => {
            this.leaveRequestData = response.data.data;
            this.totalPages = response.data.meta.last_page;
          });
    },
  },
  computed: {
    ...mapState(useModalStore, ['modal'])
  }
};
</script>


