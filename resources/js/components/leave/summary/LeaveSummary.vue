<template>
  <SidebarFilter
      :params="params"
      :toggle="toggleFilter"
      :is-management-status="false"
      :is-leave-type="true"
      @closeToggle="toggleFilter = false"
      @search="search"
      @reset="reset"
  />
  <ProgressLoad v-if="pageLoad" :value="progress"/>
  <template v-else>
    <div class="d-flex justify-content-between">
      <h4 class="d-inline-block">Company leave summary</h4>
      <div class="d-inline-block d-print-none">
        <Button type="button" label="Print" icon="bi bi-printer-fill" @click="print" class="me-1"/>
        <Button :disabled="pageLoad" type="button" label="Filter" icon="bi bi-filter" :badge="filterBadge"
                @click="toggleFilter = true"/>
      </div>
    </div>
    <div class="mb-3">
      <Button class="me-1" type="button" label="Download Leave Request" icon="bi bi-file-earmark-spreadsheet-fill" @click="downloadLeaveRequest"/>
      <Button type="button" label="Download Leave Balance" icon="bi bi-file-earmark-spreadsheet-fill" @click="downloadLeaveBalance"/>
    </div>
    <span v-if="leaveRequestData.length > 0">
    <TableMain>
        <thead>
        <tr>
            <th class="text-center d-print-none">Action</th>
            <th class="text-start" @click="sortByName">
                <div class="d-flex justify-content-between">
                    Requested by
                    <i class="bi bi-chevron-expand"></i>
                </div>
            </th>
            <th class="text-start" style="min-width: 12rem;">Leave Type</th>
            <th class="text-center" style="min-width: 14rem;">Details</th>
            <th class="text-center d-print-none" style="min-width: 12.5rem;">Supervisor</th>
            <th class="text-center d-print-none" style="min-width: 12.5rem;">HOD</th>
        </tr>

        </thead>
        <tbody>
        <tr v-for="data in sortedData" :key="data.id">
            <td class="text-center d-print-none">
                <button @click="showLeave(data)" class="btn btn-outline-success">View</button>
            </td>
            <td class="text-left">
                <TableProfile :user="data.requester"/>
            </td>

            <td class="text-start">
                <div class="d-flex flex-column">
                    {{ data.leave_type_name }}
                    <small class="text-muted">Applied on: {{ displayDate(data.leave_created) }}</small>
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

            <td class="text-start text-truncate">
                <div class="d-flex flex-column align-items-start justify-content-center" style="font-size: 0.8rem;">
                    <span class="mb-0 text-capitalize"><span class="fw-light">Start Date:</span> {{
                        displayDate(data.start_date, data.start_date_selected)
                      }}</span>
                    <span class="mb-0 text-capitalize"><span class="fw-light">End Date:</span> {{
                        displayDate(data.end_date, data.end_date_selected)
                      }}</span>
                    <span class="mb-0"><span class="fw-light">Duration:</span> {{ data.duration }} day(s)</span>
                    <span class="mb-0"><span class="fw-light">Attachment: </span>
                        <PreviewAttachment v-if="data.attachment.length > 0" :href="attachment_url + data.attachment[0].path"/>
                        <span class="text-danger" v-else>None</span>
                    </span>

                    <span v-if="! data.first_approver && ! data.second_approver" class="badge bg-danger me-2 mb-1">Applied by HR</span>
                    <span v-if="data.deduct_type" class="badge bg-danger">Balance taken from {{ data.deduct_type }} Leave</span>

                    <div class="d-flex flex-column" v-if="data.hr_note">
                        <span class="mt-2">HR Notes</span>
                        <Textarea
                            disabled
                            v-model="data.hr_note"
                            cols="28"
                            rows="1"
                            autoResize
                            style="font-size: 0.85rem;"
                        />
                    </div>
                    <button @click="addNote(data)" class="btn btn-outline-success mt-2 d-print-none"
                            style="font-size:0.85rem;">
                        {{ (data.hr_note) ? 'Edit' : 'Add' }}
                        Note
                    </button>
                </div>
            </td>

            <td class="text-center d-print-none" style="font-size: 0.8rem;">
                <div v-if="data.first_approver" class="d-flex flex-column">
                    {{ data.first_approver.name }}
                    <small>
                        <span class="badge text-capitalize" style="font-size: 0.75rem;"
                              :class="{ 'bg-secondary': data.first_approver_status === 'pending',
                                        'bg-success': data.first_approver_status === 'approved',
                                        'bg-warning': data.first_approver_status === 'canceled',
                                        'bg-danger': data.first_approver_status === 'rejected',
                                            }"
                        >{{ data.first_approver_status }}</span>
                    </small>
                </div>
                <span class="text-danger" v-else>Not Assigned</span>
            </td>

            <td class="text-center d-print-none" style="font-size: 0.8rem;">
                <div v-if="data.second_approver" class="d-flex flex-column">
                    {{ data.second_approver.name }}
                    <small class="text-capitalize">
                        <span class="badge" style="font-size: 0.75rem;"
                              :class="{   'bg-secondary': data.second_approver_status === 'pending',
                                        'bg-success': data.second_approver_status === 'approved',
                                        'bg-warning': data.second_approver_status === 'canceled',
                                        'bg-danger': data.second_approver_status === 'rejected',
                            }"
                        >{{ data.second_approver_status }}</span>
                    </small>
                </div>
                <span class="text-danger" v-else>Not Assigned</span>
            </td>
        </tr>
        </tbody>
    </TableMain>
    <Pagination
        v-if="! filterBadge"
        :perPage="10"
        :totalPages="this.totalPages"
        :currentPage="this.currentPage"
        @pagechanged="onPageChange"
    />
    </span>
    <EmptyScreen v-else text="There are no leave summary available"/>
    <ViewLeaveModal v-model="showViewLeaveModal" :data="selectedLeaveRequestData"/>
    <msg-modal :message="message" v-model="showMessageModal" @cancel="closeModal"/>
    <AddNoteModal
        v-model="noteModal.show"
        :data="noteModal.data"
    />
  </template>
</template>
<script>
import ProgressLoad from "../../elements/ProgressLoad.vue";
import ViewLeaveModal from "../ViewLeaveModal.vue";
import $api from "../../api";
import Pagination from "../../elements/Pagination.vue";
import sortFrontEnd from "../../../mixins/sortFrontEnd";
import TableMain from "../../elements/TableMain.vue";
import TableProfile from "../../elements/TableProfile.vue";
import EmptyScreen from "../../elements/EmptyScreen.vue";
import calculateWorkDay from "../../../mixins/calculateWorkDay";
import Textarea from 'primevue/textarea';
import AddNoteModal from "./AddNoteModal.vue";
import SidebarFilter from "../../elements/SidebarFilter.vue";
import Button from 'primevue/button';
import PreviewAttachment from "../../elements/attachments/PreviewAttachment.vue";

export default {
  components: {
    ProgressLoad, ViewLeaveModal, Pagination, TableMain,
    TableProfile, EmptyScreen, Textarea, AddNoteModal,
    SidebarFilter, Button, PreviewAttachment
  },
  mixins: [sortFrontEnd, calculateWorkDay],
  data() {
    return {
      params: {},
      pageLoad: false,
      toggleFilter: false,
      user_id: parseInt(localStorage.getItem('user_id')),
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
      currentPage: 1,
      noteModal: {
        show: false,
        data: {}
      }
    };
  },
  created() {
    let url = window.location.href;
    let base_url = url.split('/')[2];
    this.attachment_url = 'http://' + base_url + '/leave-request/';
    this.getLeaveSummary();
  },
  methods: {
    closeModal(close) {
      close();
    },

    /*use in add note modal children*/
    messageMethod(msg) {
      this.message = msg;
      this.showMessageModal = !this.showMessageModal;
    },

    onPageChange(page) {
      this.pageLoad = true;
      this.currentPage = page;
      this.getLeaveSummary();
    },

    showLeave(data) {
      this.selectedLeaveRequestData = data;
      this.showViewLeaveModal = !this.showViewLeaveModal;
    },

    print() {
      window.print()
    },

    addNote(data) {
      this.noteModal = {
        show: true,
        data: data
      }
    },

    async search() {
      this.leaveRequestData = [];
      this.toggleFilter = false;
      this.currentPage = 1;
      await this.filterLeaveApi();
    },

    async reset() {
      this.pageLoad = true;
      this.currentPage = 1;
      this.params = {}
      await this.getLeaveSummary();
    },

    async downloadLeaveRequest() {
        const params = this.params;

        // Use Axios to make the API request
        $api.get('/api/administration/excel-leave-summary', {
            params: params,
            responseType: 'blob'
        })
            .then(response => {
                // Create a blob from the response data
                const blob = new Blob([response.data], { type: response.headers['content-type'] });

                // Create a link element and trigger a download
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'excel-leave-summary.xlsx';
                link.click();
            })
            .catch(error => {
                console.error('Error downloading Leave Request Excel file:', error);
                // Handle error as needed
            });
    },

    async downloadLeaveBalance() {
      const params = this.params;

      // Use Axios to make the API request
      $api.get('/api/administration/excel-leave-balance', {
        params: params,
        responseType: 'blob'
      })
          .then(response => {
            // Create a blob from the response data
            const blob = new Blob([response.data], { type: response.headers['content-type'] });

            // Create a link element and trigger a download
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = 'excel-leave-balance.xlsx';
            link.click();
          })
          .catch(error => {
            console.error('Error downloading Leave Balance Excel file:', error);
            // Handle error as needed
          });
    },

    async getLeaveSummary() {
      await $api.get('/api/leave-request/' + this.user_id + '/summary?page=' + this.currentPage)
          .then(response => {
            this.pageLoad = false;
            this.leaveRequestData = response.data.data;
            this.totalPages = response.data.meta.last_page;
          });
    },

    async filterLeaveApi() {
      let params = this.params;

      await $api.get('/api/leave-request/' + this.user_id + '/summary?page=' + this.currentPage, {params})
        .then(response => {

            this.pageLoad = true;
          response.data.data.forEach((data) => this.leaveRequestData.push(data));
          this.totalPages = response.data.meta.last_page;


          if (this.currentPage < this.totalPages) {
            this.currentPage++;
            setTimeout(() => {
              this.filterLeaveApi()
            }, 1000)
          } else {
            this.pageLoad = false;
          }
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

    sortedData() {
      if (this.sortByNameOrder === 1) {
        return this.leaveRequestData.slice().sort((a, b) => a.requester.name.localeCompare(b.requester.name))
      } else if (this.sortByNameOrder === -1) {
        return this.leaveRequestData.slice().sort((a, b) => b.requester.name.localeCompare(a.requester.name))
      } else {
        return this.leaveRequestData
      }
    },

    progress() {
      let progress = (this.currentPage / this.totalPages * 100).toFixed(2);
      return parseInt(progress);
    }
  }
}
</script>
