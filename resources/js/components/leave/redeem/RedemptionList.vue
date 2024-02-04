<template>
  <div v-if="this.type === 'summary'">
    <SidebarFilter
        :params="params"
        status-option="hr-approval"
        :toggle="toggleFilter"
        @closeToggle="toggleFilter = false"
        @search="search"
        @reset="reset"
    />
    <div class="d-flex justify-content-end gap-2 d-print-none mb-2">
      <Button type="button" class="btn-success" label="Print" icon="bi bi-printer-fill" @click="print"/>
      <Button :disabled="pageLoad" type="button" label="Filter" icon="bi bi-filter" :badge="filterBadge"
              @click="toggleFilter = true"/>
    </div>
  </div>

  <ProgressLoad v-if="pageLoad" :value="progress"/>
  <EmptyState
      v-else-if="leaveRedemptionList.length === 0" subtitle="There are no leave redemption summary available"/>
  <div v-else>
    <TableMain>
      <thead>
      <tr>
        <th class="text-center d-print-none">Action</th>
        <th v-if="this.type !== 'history'" class="text-start" style="min-width: 12rem;">Requested by</th>
        <th style="min-width: 10rem;">Request created</th>
        <th style="min-width: 14rem;">Details</th>
        <th class="text-center" style="min-width: 11rem;">Supervisor</th>
        <th class="text-center" style="min-width: 11rem;">HOD</th>

      </tr>
      </thead>
      <tbody>
      <tr v-for="request in leaveRedemptionList" :key="request.id">
        <td class="text-center d-print-none">
          <div v-if="this.type === 'history'" class="align-items-center">
            <button v-if="request.overall_status === 'pending'" @click="$emit('edit', request)"
                    class="btn btn-outline-success m-1">Edit request
            </button>
            <button v-else @click="$emit('view', request)" class="btn btn-outline-success m-1">View request</button>
            <button v-if="request.attachment.length > 0 && request.overall_status === 'pending'"
                    @click="$emit('addAttachment', request)" class="btn btn-outline-secondary m-1">Edit attachments
            </button>
          </div>

          <div v-else class="align-items-center">
            <button
                v-if="(isApproved(request) && this.type === 'approval') || (request.overall_status === 'approved' && this.type === 'summary')"
                @click="$emit('approve', request)" class="btn btn-success">Review
            </button>
            <button v-else @click="$emit('view', request)" class="btn btn-outline-success">View</button>
          </div>

        </td>
        <td v-if="this.type !== 'history'" class="text-left">
          <TableProfile :user="request.requester"/>
        </td>
        <td class="text-start">
          <p>{{ request.request_created }}</p>
          <div v-if="request.overall_status === 'approved'" style="font-size: 0.75rem;" class="badge bg-info">Pending HR
            Approval
          </div>
          <div v-else class="badge text-capitalize" style="font-size: 0.75rem;"
               :class="getStatusOption(request.overall_status)">{{ request.overall_status }}
          </div>
        </td>
        <td>
          <div class="d-flex flex-column align-items-start">
            <small class="text-muted">Start Date: <span class="fw-bold"> {{ request.start_date }}</span></small>
            <small class="text-muted">End Date: <span class="fw-bold"> {{ request.end_date }}</span></small>
            <small v-if="request.added_qty || request.balance_received" class="text-muted">HR Approved Qty: <span
                class="fw-bold">{{ request.added_qty ?? request.balance_received }} day(s)</span></small>
            <small v-if="request.balance_qty" class="text-muted">Balance Qty: <span
                class="fw-bold">{{ request.balance_qty }} day(s)</span></small>
            <a class="text-primary fw-bold" target="#" @click="$emit('viewAttachment', request)" style="cursor:pointer;">
              <i class="bi bi-paperclip" style="font-size: 1rem;"/>Attachment
            </a>
          </div>
        </td>
        <td class="text-center">
          <div v-if="request.first_approver" class="d-flex flex-column">
            {{ request.first_approver.name }}
            <small>
                <span class="badge text-capitalize" style="font-size: 0.75rem;"
                      :class="getStatusOption(request.first_approver_status)"
                >{{ request.first_approver_status }}</span>
            </small>
          </div>
          <span class="text-danger" v-else>Not Assigned</span>
        </td>
        <td class="text-center">
          <div v-if="request.second_approver" class="d-flex flex-column">
            {{ request.second_approver.name }}
            <small class="text-capitalize">
                <span class="badge" style="font-size: 0.75rem;"
                      :class="getStatusOption(request.second_approver_status)"
                >{{ request.second_approver_status }}</span>
            </small>
          </div>
          <span class="text-danger" v-else>Not Assigned</span>
        </td>
      </tr>
      </tbody>
    </TableMain>
    <Pagination
        class="d-print-none"
        v-if="!filterBadge && this.totalPages > 1"
        :perPage="5"
        :totalPages="this.totalPages"
        :currentPage="this.currentPage"
        @pagechanged="onPageChange"
    />
  </div>
</template>

<script>
import EmptyState from "../../elements/EmptyState.vue";
import TableMain from "../../elements/TableMain.vue";
import Pagination from "../../elements/Pagination.vue";
import statusOption from "../../../mixins/statusOption";
import calculateWorkDay from "../../../mixins/calculateWorkDay";
import tabOption from "../../../mixins/tabOption";
import $api from "../../api";
import sortFrontEnd from "../../../mixins/sortFrontEnd";
import TableProfile from "../../elements/TableProfile.vue";
import SidebarFilter from "../../elements/SidebarFilter.vue";
import ProgressLoad from "../../elements/ProgressLoad.vue";
import Button from "primevue/button";

export default {
  components: {ProgressLoad, SidebarFilter, TableProfile, Pagination, TableMain, EmptyState, Button},
  props: ['url', 'type', 'approver_id'], // type is to determine whether user in on history / approval / summary page
  emits: ['view', 'edit', 'approve', 'viewAttachment', 'addAttachment'],
  mixins: [statusOption, calculateWorkDay, tabOption, sortFrontEnd],
  data() {
    return {
      params: {},
      toggleFilter: false,
      pageLoad: false,
      leaveRedemptionList: [],
      currentPage: 1,
      totalPages: 1,
    }
  },
  created() {
    this.getLeaveRedemptionAPI();
  },
  methods: {
    print() {
      window.print()
    },

    async search() {
      this.toggleFilter = false;
      this.pageLoad = true;
      this.currentPage = 1;
      this.leaveRedemptionList = [];
      await this.filterLeaveRedemptionApi();
    },

    async reset() {
      this.pageLoad = true;
      this.currentPage = 1;
      this.params = {}
      await this.getLeaveRedemptionAPI();
    },

    isApproved(request) {
      if (request.overall_status !== 'pending') {
        return false;
      }

      if (this.approver_id === 1 && request.first_approver_status === 'pending') {
        return true;
      } else {
        if (request.second_approver_status === 'pending') {
          return true;
        }
      }

      return false;
    },

    onPageChange(page) {
      this.pageLoad = !this.pageLoad;
      this.currentPage = page;
      this.getLeaveRedemptionAPI();
    },

    async filterLeaveRedemptionApi() {
      let params = this.params;

      await $api.get(this.url + '?page=' + this.currentPage, {params})
          .then((response) => {
            this.pageLoad = true;
            response.data.data.forEach((data) => this.leaveRedemptionList.push(data));
            this.totalPages = response.data.meta.last_page;

            if (this.currentPage < this.totalPages) {
              this.currentPage++;
              setTimeout(() => {
                this.filterLeaveRedemptionApi()
              }, 1000)
            } else {
              this.pageLoad = false;
            }
          }).catch((error) => {
            this.pageLoad = false;
            console.log(error)
          });
    },

    async getLeaveRedemptionAPI() {
      this.pageLoad = true;

      await $api.get(this.url + '?page=' + this.currentPage)
          .then(response => {
            this.leaveRedemptionList = response.data.data;
            this.totalPages = response.data.meta.last_page;
            this.pageLoad = false;
          }).catch((error) => {
            this.pageLoad = false;
            console.log(error)
          });
    },
  },
  computed: {
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
