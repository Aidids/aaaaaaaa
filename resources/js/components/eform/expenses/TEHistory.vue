<template>
  <div class="my-3 h-100">
    <loader v-if="pageLoad"/>
    <EmptyState v-else-if="travelRequests.length === 0"
                subtitle="No Request for Travel Claim"/>
    <div v-else>
      <TECard v-for="(request, index) in travelRequests" :key="request.id"
              :request="request" :is-approve-view="isApproveView" @review="viewMore(index)"
              @cancel="cancelRequest(request)"/>

      <Pagination
          class="mt-2"
          v-show="this.totalPages > 1"
          :perPage="6"
          :totalPages="this.totalPages"
          :currentPage="this.currentPage"
          @pagechanged="onPageChange"/>
    </div>
  </div>

  <TEModal
      v-model="showViewRequestModal"
      title="Travel Expense Request"
      :form_id="selectedRequest.id"
      @action="confirmAction($event, selectedRequest)"/>

  <ActionModal v-model="showActionModal" :action='approvalAction' @confirm="actionTravelRequestApi($event)"/>

</template>
<script>

import $api from "../../api";
import Pagination from "../../elements/Pagination.vue";
import ActionModal from "../../elements/ActionModal.vue";
import {mapState} from "pinia";
import {useProfileStore} from "../../../stores/getProfile";
import EmptyState from "../../elements/EmptyState.vue";
import TECard from "./TECard.vue";
import TEModal from "./TEModal.vue";
import {useModalStore} from "../../../stores/modal";


export default {
  components: {
    TEModal,
    TECard,
    EmptyState, ActionModal, Pagination
  },
  props: {
    isApproveView: {
      type: Boolean,
      default: false,
    }
  },
  data() {
    return {
      travelRequests: [],
      selectedRequest: {},
      approvalAction: '',
      showViewRequestModal: false,
      showActionModal: false,
      pageLoad: false,
      totalPages: 1,
      currentPage: 1,
    }
  },
  created() {
    this.isApproveView ? this.getTEApprovalAPI() : this.getTEHistoryAPI();
  },
  methods: {
    confirmAction(action, request) {
      this.showViewRequestModal = false;
      this.selectedRequest = request;
      this.approvalAction = action;
      this.showActionModal = true;
    },

    viewMore(index) {
      this.selectedRequest = this.travelRequests[index];
      this.showViewRequestModal = true;
    },

    onPageChange(page) {
      this.pageLoad = true;
      this.currentPage = page;
      this.isApproveView ? this.TEApprovalPagination() : this.TEPagination();
    },

    cancelRequest(request) {
      this.selectedRequest = request;
      useModalStore().confirm('Canceling your request is irreversible. Do you want to proceed?')
    },

    processBreakdown(req) {
      req.breakdownData = {
        total_allowance: req.total_allowance,
        total_transport: req.total_transport,
        total_expense: req.total_expense,
        allowance: req.allowances,
        expenses: req.expenses,
        transport: req.transports
      }
    },

    async getTEHistoryAPI() {
      await $api.get('/api/travel-claim/history').then((response) => {
        this.travelRequests = response.data.data;
        this.travelRequests.forEach((req) => {
          this.processBreakdown(req)
        })
        if (response.data.meta) {
          this.totalPages = response.data.meta.last_page;
        }

        this.pageLoad = false;
      });
    },

    async TEPagination() {
      await $api
          .get(
              '/api/travel-claim/history?page=' + this.currentPage
          )
          .then((response) => {
            this.travelRequests = response.data.data;
            this.travelRequests.forEach((req) => {
              this.processBreakdown(req)
            })
            this.totalPages = response.data.meta.last_page;
            this.pageLoad = false;
          });
    },

    async cancelTravelRequestApi() {
      let formData = new FormData();
      formData.append("travel_id", this.selectedRequest.id);

      await $api
          .post('/api/travel-claim/cancel', formData)
          .then((response) => {
            this.getTEHistoryAPI();
            this.$toast.add({
              severity: 'success',
              summary: 'Success',
              detail: response.data.message,
              life: 3000
            });
          })
          .catch((error) => {
            console.log(error);
          });
    },

    async getTEApprovalAPI() {
      await $api.get('/api/travel-claim/approve').then((response) => {
        this.travelRequests = response.data.data;
        this.travelRequests.forEach((req) => {
          this.processBreakdown(req)
        })
        if (response.data.meta) {
          this.totalPages = response.data.meta.last_page;
        }
        this.pageLoad = false;
      });
    },

    async TEApprovalPagination() {
      await $api
          .get(
              '/api/travel-claim/approve?page=' + this.currentPage)
          .then((response) => {
            this.travelRequests = response.data.data;
            this.travelRequests.forEach((req) => {
              this.processBreakdown(req)
            })
            this.totalPages = response.data.meta.last_page;
            this.pageLoad = false;
          });
    },

    async actionTravelRequestApi(remark) {

      let profile = useProfileStore().profile;
      let formData = new FormData();
      formData.append('id', this.selectedRequest.id)
      formData.append('current_approver', profile.id)
      formData.append('status', this.approvalAction)
      formData.append('remark', remark)

      await $api
          .post('/api/travel-claim/approve', formData)
          .then((response) => {
            this.getTEApprovalAPI();
            this.showActionModal = false;
            this.$toast.add({
              severity: 'success',
              summary: 'Success',
              detail: response.data.message,
              life: 3000
            });
          })
          .catch((error) => {
            useModalStore().show(error.response.data.message)
          });
    },
  },
  computed: {
    ...mapState(useModalStore, ['modal']),
    ...mapState(useProfileStore, ['profile'])
  }
}
</script>

