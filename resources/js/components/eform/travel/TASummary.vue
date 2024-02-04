<template>
  <SidebarFilter
      :params="params"
      :toggle="toggleFilter"
      status-option="hr-approval"
      @closeToggle="toggleFilter = false"
      @search="search"
      @reset="reset"
  >
    <div class="my-2">
      <label class="form-label">Travel Purpose</label>
      <Dropdown
          class="w-100"
          placeholder="Click here to purpose type"
          v-model="params.purpose"
          :options="travelPurpose"
          optionLabel="label"
          optionValue="value"
      />
      <Divider/>
    </div>
  </SidebarFilter>
  <div class="d-flex justify-content-end gap-2 d-print-none mb-2">
    <Button type="button" label="Print" icon="bi bi-printer-fill" @click="print"/>
    <Button :disabled="pageLoad" type="button" label="Filter" icon="bi bi-filter" :badge="filterBadge"
            @click="toggleFilter = true"/>
  </div>
  <ProgressLoad v-if="pageLoad" :value="progress"/>
  <div v-else-if="travelRequests.length === 0"
       class="h-100 d-flex flex-column justify-content-center align-items-center">
    <i class="bi bi-clipboard-data text-danger" style="font-size: 5.5rem;"/>
    <h6 class="text-danger">No Travel Request Pending</h6>
  </div>
  <div v-else>
    <TableMain class="mt-3">
      <thead>
      <tr>
        <th></th>
        <th class="text-center d-print-none" style="min-width: 15rem">Action</th>
        <th class="text-center" style="min-width: 20rem">Requested By</th>
        <th style="min-width: 20rem">Request Details</th>
      </tr>
      </thead>
      <tbody v-for="(request, index) in travelRequests" :key="index">
      <tr>
        <td>
          <i class="mx-2" :class="request.showDetails ? `bi bi-chevron-down` : `bi bi-chevron-right`"
             @click="toggleDetails(index)" style="cursor: pointer; font-size: 1.2rem;"/>
        </td>
        <td class="d-print-none text-center">
          <div class="d-inline-block">
            <button class="btn btn-outline-secondary m-1" @click="preview(request.id)">View</button>
            <button v-show="request.approvers.overall_status !== 'completed'" class="btn btn-secondary m-1"
                    @click="viewMore(request)">Review
            </button>
            <button v-show="request.approvers.overall_status !== 'completed'"
                    class="btn btn-success m-1" @click="addAttachment(request)">Upload File
            </button>
          </div>
        </td>
        <td>
          <TableProfile :user="request.approvers.requester"/>
        </td>
        <td class="text-start">
          <div class="d-inline-flex flex-column">
            <h5 v-if="request.travel_purpose" class="card-title">{{ request.department_name }}</h5>
            <h5 v-else class="card-title">{{ request.project_name }}<span
                style="font-size: 0.8rem">({{ request.project_location }})</span></h5>
            <small class="text-muted">Main Office:
              <span class="fw-bold">{{ mainOfficeList[request.main_office].label }}</span>
            </small>
            <small class="text-muted">Request Date:
              <span class="fw-bold">{{ this.displayDate(request.created_at) }}</span>
            </small>
            <small class="text-muted">Reimbursement:
              <span class="fw-bold">{{ request.reimbursement ? 'Yes' : 'No' }}</span>
            </small>
            <small class="text-muted">Status:
              <span v-if="request.approvers.overall_status === 'approved'"
                    class="badge bg-info">Pending HR Approval</span>
              <span v-else class="badge text-capitalize"
                    :class="this.getStatusOption(request.approvers.overall_status)">{{
                  request.approvers.overall_status
                }}</span>
            </small>
          </div>
        </td>
      </tr>
      <tr class="d-print">
        <td colspan="6" v-if="request.showDetails"
            :class="{'open-details': request.showDetails, 'close-details': !request.showDetails}">
          <h6 class="mb-2">Travel Details</h6>
          <div class="d-flex flex-column my-3">
            <div class="d-flex bg-success p-2 rounded-top">
              <div class="col-2 text-center text-white fw-bold" style="min-width: 10rem">From</div>
              <div class="col-2 text-center text-white fw-bold" style="min-width: 10rem">To</div>
              <div class="col-2 text-center text-white fw-bold" style="min-width: 10rem">Start Date</div>
              <div class="col-2 text-center text-white fw-bold" style="min-width: 10rem">End Date</div>
              <div class="col-2 text-center text-white fw-bold" style="min-width: 10rem">Accommodation
              </div>
            </div>

            <div v-for="(location, index) in request.location" :key="index"
                 class="d-flex p-2 rounded-bottom bg-body-tertiary">
              <div class="col-2 text-center" style="min-width: 10rem">{{ location.from }}</div>
              <div class="col-2 text-center" style="min-width: 10rem">{{ location.to }}</div>
              <div class="col-2 text-center" style="min-width: 10rem">
                {{ this.displayDate(location.start_date) }}
              </div>
              <div class="col-2 text-center" style="min-width: 10rem">
                {{ this.displayDate(location.end_date) }}
              </div>
              <div class="col-2 text-center" style="min-width: 10rem">
                {{ location.accomodation ? 'Yes' : 'No' }}
              </div>
            </div>
          </div>
        </td>
      </tr>
      </tbody>
    </TableMain>
    <div class="d-flex flex-column d-none d-print-block" style="height: 100vh;"></div>
    <Pagination
        v-if="! filterBadge"
        class="mt-2 d-print-none"
        v-show="this.totalPages > 1"
        :perPage="6"
        :totalPages="this.totalPages"
        :currentPage="this.currentPage"
        @pagechanged="onPageChange"/>
  </div>

  <TAModal
      :request="selectedRequest"
      v-model="showViewRequestModal"
      :is-approve-view="true"
      title="Travel Expense Request"
      @action="confirmAction($event, selectedRequest)"/>

  <ActionModal v-model="showActionModal" :action='approvalAction' @confirm="actionTravelRequestApi($event)">
    <div class="d-flex align-items-center my-2">
      <p class="me-2">Assign out of office leave</p>
      <InputSwitch v-model="isOutOfOffice.switch" :disabled="isOutOfOffice.disabled"/>
    </div>
    <main class="form-control" v-if="isOutOfOffice.switch">
      <div class="d-flex gap-3">
        <div class="form-group mt-2 w-100">
          <label class="form-label mb-2">Start Date</label>
          <Datepicker
              :model-value="outOfOfficeObject.start_date"
              :enable-time-picker="false"
              placeholder="Please select start date"
              auto-apply
              :state="null"
              format="dd/MM/yyyy"
              :clearable="false"
              :max-date="outOfOfficeObject.end_date"
              @update:modelValue="outOfOfficeObject.start_date = convertDate($event)"
          />
        </div>
        <div class="form-group mt-2 w-100">
          <label class="form-label mb-2">End Date</label>
          <Datepicker
              :model-value="outOfOfficeObject.end_date"
              :enable-time-picker="false"
              placeholder="Please select end date"
              auto-apply
              :state="null"
              format="dd/MM/yyyy"
              :clearable="false"
              :min-date="outOfOfficeObject.start_date"
              @update:modelValue="outOfOfficeObject.end_date = convertDate($event)"
          />
        </div>
      </div>
      <div class="d-flex gap-3 mb-3">
        <div class="form-group mt-2 w-100">
          <label class="mb-2">Start Date Period</label>
          <Dropdown
              v-model="outOfOfficeObject.start_date_type"
              class="w-100"
              :options="dateType"
              option-value="value"
              optionLabel="label"
              placeholder="Please select period"
              style="height: 37.1px"/>
        </div>
        <div class="form-group mt-2 w-100">
          <label class="mb-2">End Date Period</label>
          <Dropdown
              :model-value="restrictDatePeriod"
              class="w-100"
              :options="dateType"
              option-value="value"
              optionLabel="label"
              placeholder="Please select period"
              style="height: 37.1px"
              @update:modelValue="outOfOfficeObject.end_date_type = $event"
          />
        </div>
      </div>
    </main>
  </ActionModal>

  <UpdateAttachmentModal
      v-if="selectedRequest.approvers"
      ref="modal"
      v-model="showAttachmentModal" title="Travel Authorization Attachment"
      :attachments="selectedRequest.approvers.attachments.filter((attachment) => attachment.hr_upload)"
      @confirm="uploadAttachment($event)" :attachment_url="attachment_url"
      @delete="deleteAttachmentAPI($event)"/>
</template>

<script>
import ProgressLoad from "../../elements/ProgressLoad.vue";
import SidebarFilter from "../../elements/SidebarFilter.vue";
import Button from "primevue/button";
import ActionModal from "../../elements/ActionModal.vue";
import Pagination from "../../elements/Pagination.vue";
import TableMain from "../../elements/TableMain.vue";
import TAModal from "./TAModal.vue";
import TableProfile from "../../elements/TableProfile.vue";
import statusOption from "../../../mixins/statusOption";
import calculateWorkDay from "../../../mixins/calculateWorkDay";
import $api from "../../api";
import UpdateAttachmentModal from "../../elements/attachments/UpdateAttachmentModal.vue";
import travelOption from "../../../mixins/travelOption";
import {useProfileStore} from "../../../stores/getProfile";
import {mapState} from "pinia";
import {useDepartmentStore} from "../../../stores/getDepartment";
import {useModalStore} from "../../../stores/modal";
import InputSwitch from 'primevue/inputswitch';
import Divider from 'primevue/divider';
import dateType from "../../../mixins/dateType";
import {useVuelidate} from "@vuelidate/core";

export default {
  props: {
    isProject: {
      type: Boolean,
      default: false,
    }
  },

  components: {
    ProgressLoad, UpdateAttachmentModal, ActionModal, Pagination, TableMain,
    TAModal, TableProfile, InputSwitch, SidebarFilter, Button, Divider
  },

  mixins: [statusOption, calculateWorkDay, travelOption, dateType],

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
      pageLoad: false,
      outOfOfficeObject: {
        start_date_type: 'full day',
        end_date_type: 'full day'
      },
      isOutOfOffice: {
        switch: true,
        disabled: false,
      },
      totalPages: 1,
      currentPage: 1,
      attachment_url: localStorage.getItem('currentUrl') + '/e-form/travel-authorization/',
    }
  },

  setup() {
    return {v$: useVuelidate()}
  },

  validations() {
    return {
      outOfOfficeObject: {
        start_date: {
          required: function () {
            if (!this.isOutOfOffice.switch) {
              return true;
            }

            return this.outOfOfficeObject.start_date;
          }
        },
        end_date: {
          required: function () {
            if (!this.isOutOfOffice.switch) {
              return true;
            }

            return this.outOfOfficeObject.end_date;
          }
        },
        start_date_type: {
          required: function () {
            if (!this.isOutOfOffice.switch) {
              return true;
            }

            return this.outOfOfficeObject.start_date_type;
          }
        },
        end_date_type: {
          required: function () {
            if (!this.isOutOfOffice.switch) {
              return true;
            }

            return this.outOfOfficeObject.end_date_type;
          }
        },

      }
    };
  },

  created() {
    this.setPurposeParams();
    this.getTravelApi();
    useDepartmentStore().init();
  },

  methods: {
    setPurposeParams() {
      if (this.isProject) {
        this.params = {
          purpose: false
        }
      } else {
        this.params = {
          purpose: true
        }
      }
    },

    toggleDetails(index) {
      this.travelRequests[index].showDetails = !this.travelRequests[index].showDetails;
    },

    viewMore(request) {
      this.selectedRequest = request;
      this.showViewRequestModal = true;
    },

    preview(id) {
      window.open(
          '/travel-authorization/' + id + '/show',
          '_blank'
      );
    },

    onPageChange(page) {
      this.pageLoad = true;
      this.currentPage = page;

    },

    confirmAction(action, request) {
      this.showViewRequestModal = false;
      this.selectedRequest = request;
      this.approvalAction = action;
      this.showActionModal = true;

      if (action === 'rejected') {
        this.isOutOfOffice = {
          switch: false,
          disabled: true,
        }
      } else {
        this.isOutOfOffice = {
          switch: true,
          disabled: false,
        }
      }
    },

    addAttachment(request) {
      this.selectedRequest = request;
      this.showAttachmentModal = true;
    },

    print() {
      window.print();
    },

    async search() {
      this.currentPage = 1;
      this.toggleFilter = false;
      this.travelRequests = [];
      this.pageLoad = true;
      await this.filterTravelApi();
    },

    async reset() {
      this.pageLoad = true;
      this.currentPage = 1;
      this.params = {}
      await this.getTravelApi();
    },

    async getTravelApi() {
      let params = this.params;
      await $api.get('/api/travel-authorization/summary?page=' + this.currentPage, {params}).then((response) => {
        this.travelRequests = response.data.data;
        this.totalPages = response.data.meta.last_page;
        this.pageLoad = false;
      });
    },

    async filterTravelApi() {
      let params = this.params;

      await $api.get('/api/travel-authorization/summary?page=' + this.currentPage, {params}).then((response) => {
        this.pageLoad = true;

        response.data.data.forEach((data) => this.travelRequests.push(data));
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
      const validated = await this.v$.$validate()

      if (!validated) {
        return alert('Please fill in missing forms');
      }

      let profile = useProfileStore().profile;
      let formData = new FormData();
      formData.append('hr_id', profile.id);
      formData.append('hr_status', this.approvalAction);
      formData.append('hr_remark', remark);
      formData.append('hr_date', this.convertDate(new Date()));

      if (this.isOutOfOffice.switch) {
        let duration = this.calculateEveryDay(
            this.outOfOfficeObject.start_date,
            this.outOfOfficeObject.end_date,
            this.outOfOfficeObject.start_date_type,
            this.outOfOfficeObject.end_date_type
        );

        formData.append('start_date', this.convertDate(this.outOfOfficeObject.start_date));
        formData.append('end_date', this.convertDate(this.outOfOfficeObject.end_date));
        formData.append('start_date_type', this.outOfOfficeObject.start_date_type);
        formData.append('end_date_type', this.outOfOfficeObject.end_date_type);
        formData.append('duration', duration);
      }


      await $api
          .post('/api/travel-authorization/' + this.selectedRequest.id + '/hrReview', formData)
          .then((response) => {
            this.getTravelApi();
            this.$toast.add({
              severity: 'success',
              summary: 'Success',
              detail: response.data.message,
              life: 3000
            });
            this.showActionModal = false;
          })
          .catch((error) => {
            useModalStore().show(error.response.data.message)
          });
    },

    async uploadAttachment(files) {
      let formData = new FormData;

      // Loop over the files array and append each file individually
      for (let i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
      }

      const config = {headers: {'Content-Type': 'multipart/form-data'}};
      await $api.post('/api/travel-authorization/' + this.selectedRequest.id + '/upload', formData, config)
          .then(response => {
            this.$toast.add({
              severity: 'success',
              summary: 'Success',
              detail: response.data,
              life: 3000
            });
            this.getTravelApi();
            this.showAttachmentModal = false;
          })
          .catch(error => {
            useModalStore().show(error.response.data.message)
          });
    },

    async deleteAttachmentAPI(attachmentId) {
      const params = {
        attachment_id: attachmentId,
      };


      await $api.delete('/api/travel-authorization/' + this.selectedRequest.id + '/deleteAttachment', {params})
          .then(response => {
            let index = this.selectedRequest.approvers.attachments.findIndex(obj => obj.id === attachmentId);
            this.selectedRequest.approvers.attachments.splice(index, 1);
            this.$toast.add({
              severity: 'success',
              summary: 'Success',
              detail: response.data.message,
              life: 3000
            });
          })
          .catch(error => {
            useModalStore().show(error.response.data.message)
          });
    },
  },
  computed: {
    ...mapState(useModalStore, ['modal']),

    ...mapState(useDepartmentStore, ['departmentOption']),

    filterBadge() {
      let count = Object.keys(this.params).length;

      if (count === 0) {
        return null;
      }

      return count.toString();
    },

    restrictDatePeriod() {
      if (this.outOfOfficeObject.start_date === this.outOfOfficeObject.end_date) {
        return (this.outOfOfficeObject.end_date_type = this.outOfOfficeObject.start_date_type);
      }

      return this.outOfOfficeObject.end_date_type;
    },

    progress() {
      let progress = (this.currentPage / this.totalPages * 100).toFixed(2);
      return parseInt(progress);
    }
  }
}
</script>

