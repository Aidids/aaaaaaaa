<template>
  <SidebarFilter
      :params="params"
      :toggle="toggleFilter"
      status-option="fixed-approvers"
      @closeToggle="toggleFilter = false"
      @search="search"
      @reset="reset"
  />
  <div class="d-flex justify-content-end gap-2 d-print-none mb-2">
    <Button type="button" label="Print" icon="bi bi-printer-fill" @click="print"/>
    <Button :disabled="pageLoad" type="button" label="Filter" icon="bi bi-filter" :badge="filterBadge"
            @click="toggleFilter = true"/>
  </div>
  <ProgressLoad v-if="pageLoad" :value="progress"/>
  <div v-else-if="travelRequests.length === 0"
       class="h-100 d-flex flex-column align-items-center justify-content-center">
    <EmptyState subtitle="No Claim Request Pending"/>
  </div>
  <div v-else>
    <TableMain class="mt-3">
      <thead>
      <tr>
        <th class="text-start ps-4" style="width: 12.5rem">Requester</th>
        <th style="min-width: 15rem">Travel Claim Summary</th>
        <th class="text-center" style="min-width: 10rem">HR Notes</th>
        <th class="text-center d-print-none" style="width: 12rem">Action</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(request, index) in travelRequests" :key="index">
        <td class="ps-4">
          <TableProfile :user="request.requester"/>
        </td>
        <td>
          <TEHeader class="col border rounded" :request="request" :full-border="true"/>
        </td>
        <td class="text-center">
          <div class="d-flex flex-column" v-if="request.hr_note">
            <span class="mt-2 text-secondary align-self-start mb-1">HR Notes</span>
            <textarea
                disabled
                v-model="request.hr_note"
                cols="28"
                rows="5"
                autoResize
                style="font-size: 0.85rem;"
                placeholder="Add note"
            />
            <button @click="addNote(request)" class="btn btn-outline-success float-start mt-2 d-print-none"
                    style="font-size:0.85rem;">
              {{ (request.hr_note) ? 'Edit' : 'Add' }}
              Note
            </button>
          </div>
          <div v-else>
            <button @click="addNote(request)" class="btn btn-outline-success mt-2 d-print-none"
                    style="font-size:0.85rem;">
              {{ (request.hr_note) ? 'Edit' : 'Add' }}
              Note
            </button>
            <p class="d-none d-print-block">No note added</p>
          </div>
        </td>
        <td class="d-print-none text-center">
          <div class="d-inline-block">
            <button class="btn btn-outline-secondary m-1" @click="preview(request.id)">View</button>
            <button v-show="request.approvers.overall_status !== 'completed'" class="btn btn-secondary m-1"
                    @click="viewMore(request)">Review
            </button>
          </div>
        </td>
      </tr>
      </tbody>
    </TableMain>
    <ManagementSignatureTable/>
    <Pagination
        v-if="! filterBadge"
        class="mt-2 d-print-none"
        v-show="this.totalPages > 1"
        :perPage="6"
        :totalPages="this.totalPages"
        :currentPage="this.currentPage"
        @pagechanged="onPageChange"/>
  </div>

  <TEModal
      v-model="showViewRequestModal"
      :form_id="selectedRequest.id"
      title="Travel Expense Request"
      @action="confirmAction($event, selectedRequest)"/>
  <ActionModal v-model="showActionModal" :action="approvalAction" @confirm="actionTravelRequestApi($event)"/>
  <AddNoteModal
      v-model="noteModal.show"
      :data="noteModal.data"
  />
</template>

<script>
import ActionModal from "../../elements/ActionModal.vue";
import Pagination from "../../elements/Pagination.vue";
import TableMain from "../../elements/TableMain.vue";
import TableProfile from "../../elements/TableProfile.vue";
import $api from "../../api";
import Modal from "../../elements/Modal.vue";
import {mapState} from "pinia";
import TEModal from "./TEModal.vue";
import AddNoteModal from "../../leave/summary/AddNoteModal.vue";
import {useModalStore} from "../../../stores/modal";
import EmptyState from "../../elements/EmptyState.vue";
import TEHeader from "./card/TEHeader.vue";
import SidebarFilter from "../../elements/SidebarFilter.vue";
import Button from 'primevue/button';
import ProgressLoad from "../../elements/ProgressLoad.vue";
import ManagementSignatureTable from "../../elements/ManagementSignatureTable.vue";

export default {
  components: {
    EmptyState,
    AddNoteModal,
    TEModal,
    Modal, ActionModal, Pagination, TableMain, TableProfile,
    TEHeader, SidebarFilter, Button, ProgressLoad,
    ManagementSignatureTable
  },

  data() {
    return {
      params: {},
      toggleFilter: false,
      travelRequests: [],
      selectedRequest: {},
      showViewRequestModal: false,
      showActionModal: false,
      approvalAction: '',
      showAttachmentModal: false,
      noteModal: {
        show: false,
        data: {}
      },
      pageLoad: false,
      totalPages: 1,
      currentPage: 1,
      attachment_url: localStorage.getItem('currentUrl') + '/e-form/travel-authorization/',
    }
  },
  created() {
    this.getTravelApi();
  },
  methods: {
    viewMore(request) {
      this.selectedRequest = request;
      this.showViewRequestModal = true;
    },

    preview(id) {
      window.open(
          '/travel-claim/' + id + '/show',
          '_blank'
      );
    },

    addNote(data) {
      this.noteModal = {
        show: true,
        data: data
      }
    },

    onPageChange(page) {
      this.pageLoad = true;
      this.currentPage = page;
      this.getTravelApi();
    },

    confirmAction(action, request) {
      this.showViewRequestModal = false;
      this.selectedRequest = request;
      this.approvalAction = action;
      this.showActionModal = true;
    },

    print() {
      window.print()
    },

    async search() {
      this.toggleFilter = false;
      this.pageLoad = true;
      this.currentPage = 1;
      this.travelRequests = [];
      await this.filterTravelApi();
    },

    async reset() {
      this.pageLoad = true;
      this.currentPage = 1;
      this.params = {}
      await this.getTravelApi();
    },

    processBreakdown(req) {
      req.breakdownData = {
        total_allowance: req.total_allowance,
        total_transport: req.total_transport,
        total_expense: req.total_expense,
        allowance: req.allowances,
        expenses: req.expenses,
        transport: req.transport
      }
    },


    async getTravelApi() {
      await $api.get('/api/travel-claim/summary?page=' + this.currentPage)
          .then((response) => {
            this.travelRequests = response.data.data;
            this.totalPages = response.data.meta.last_page;
            this.pageLoad = false;
          });
    },

    async filterTravelApi() {
      let params = this.params;

      await $api.get('/api/travel-claim/summary?page=' + this.currentPage, {params})
          .then((response) => {
            this.pageLoad = true;
            response.data.data.forEach((data) => this.travelRequests.push(data));
            this.travelRequests.forEach((req) => this.processBreakdown(req));
            this.totalPages = response.data.meta.last_page;

            if (this.currentPage < this.totalPages)
            {
              this.currentPage++;
              setTimeout(() => {
                this.filterTravelApi()
              }, 1000)
            }
            else
            {
              this.pageLoad = false;
            }

          });
    },

    async actionTravelRequestApi(remark) {
      let formData = new FormData();
      formData.append('id', this.selectedRequest.id)
      formData.append('current_approver', 0)
      formData.append('status', this.approvalAction)
      formData.append('remark', remark)
      await $api
          .post('/api/travel-claim/approve', formData)
          .then((response) => {
            this.filterTravelApi();
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

    filterBadge() {
      let count = Object.keys(this.params).length;

      if (count === 0) {
        return null;
      }

      return count.toString();
    },

    progress() {
      let progress = (this.currentPage / this.totalPages * 100).toFixed(2);
      return parseInt(progress);
    }
  }
}
</script>

