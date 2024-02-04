<template>
    <Toast/>
    <h4 class="mb-3">Leave Redemption History</h4>
    <TabView v-model:activeIndex="active" class="eform-tab" @tab-change="updateUrl(tabs, active)">
      <TabPanel>
        <template #header>Replacement Leave</template>
        <keep-alive>
          <RedemptionList ref="replacement-leave-history" v-if="active === 0" :url="getUrl" type="history"
                          @view="viewRequest($event)"
                          @edit="editRequest($event)"
                          @view-attachment="viewAttachment($event)"
                          @add-attachment="addAttachment($event)"/>
        </keep-alive>
      </TabPanel>
      <TabPanel>
        <template #header>Offshore Leave</template>
        <keep-alive>
          <RedemptionList ref="offshore-leave-history" v-if="active === 1" :url="getUrl" type="history"
                          @view="viewRequest($event)"
                          @edit="editRequest($event)"
                          @view-attachment="viewAttachment($event)"
                          @add-attachment="addAttachment($event)"/>
        </keep-alive>
      </TabPanel>
    </TabView>

  <UpdateAttachmentModal
      ref="modal"
      v-model="showAddAttachmentModal" title="Replacement Leave Attachment"
      :attachments="selectedLeaveRedemption.attachment"
      @confirm="uploadAttachmentsAPI($event)" :attachment_url="getAttachmentUrl"
      @delete="deleteAttachmentAPI($event)"/>
  <ViewAttachmentModal
      v-model="showViewAttachmentModal" :attachments="selectedLeaveRedemption.attachment"
      :attachment_url="getAttachmentUrl"/>
  <ViewRequestModal
      v-model="showRequestModal" :data="selectedLeaveRedemption"/>
  <EditRequestModal
      v-model="showEditModal" :redeemReplacementLeave="selectedLeaveRedemption"
      @confirm="editRequestAPI" @cancel="cancel"/>
  <Modal v-model="modal.show" :modal="modal" :is-close="modal.isClose" :has-confirm="modal.hasConfirm"
         @confirm="confirmModal"/>
</template>


<script>
import $api from "../../api";
import RedemptionList from "./RedemptionList.vue";
import UpdateAttachmentModal from "../../elements/attachments/UpdateAttachmentModal.vue";
import ViewAttachmentModal from "../../elements/attachments/ViewAttachmentModal.vue";
import ViewRequestModal from "./ViewRequestModal.vue";
import EditRequestModal from "./EditRequestModal.vue";
import Modal from "../../elements/Modal.vue";
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import Toast from "primevue/toast";
import {mapState} from "pinia";
import {useModalStore} from "../../../stores/modal";
import conversion from "../../../mixins/conversion";
import tabOption from "../../../mixins/tabOption";

export default {
  components: {
    RedemptionList,
    Modal,
    UpdateAttachmentModal,
    ViewAttachmentModal,
    ViewRequestModal,
    EditRequestModal,
    TabView, TabPanel, Toast
  },
  data() {
    return {
      active: 0,
      tabs: [
        'replacement-leave',
        'offshore-leave'
      ],
      selectedLeaveRedemption: {},
      showAddAttachmentModal: false,
      showViewAttachmentModal: false,
      showRequestModal: false,
      showEditModal: false,
    };
  },
  created() {
    this.active = this.getTabIndex(this.tabs);
    useModalStore().init();
  },
  mixins: [tabOption, conversion],
  methods: {
    viewRequest(request) {
      this.selectedLeaveRedemption = request;
      this.showRequestModal = !this.showRequestModal;
    },

    editRequest(request) {
      this.selectedLeaveRedemption = request;
      this.selectedLeaveRedemption.isEdit = true;

      useModalStore().confirm('Editing leave redemption will reset both approver status. Would you like to proceed?')
    },

    confirmModal(close) {
      this.showEditModal = !this.showEditModal;
      close();
    },

    addAttachment(request) {
      this.selectedLeaveRedemption = request;
      this.showAddAttachmentModal = !this.showAddAttachmentModal;
    },

    viewAttachment(request) {
      this.selectedLeaveRedemption = request;
      this.showViewAttachmentModal = !this.showViewAttachmentModal;
    },

    cancel(close) {
      close()
    },

    refreshData() {
      this.active === 0 ? this.$refs["replacement-leave-history"].getLeaveRedemptionAPI() : this.$refs["offshore-leave-history"].getLeaveRedemptionAPI()
    },

    async editRequestAPI() {
      useModalStore().load()

      let formData = new FormData();

      formData.append('id', this.selectedLeaveRedemption.id);
      formData.append('start_date', this.formDataDate(this.selectedLeaveRedemption.start_date));
      formData.append('end_date', this.formDataDate(this.selectedLeaveRedemption.end_date));

      formData.append('remark', this.selectedLeaveRedemption.remark);

      if (this.selectedLeaveRedemption.first_approver) {
        formData.append('first_approver_id', this.selectedLeaveRedemption.first_approver.id);
        formData.append('first_approver_status', 'pending');
      }

      if (this.selectedLeaveRedemption.second_approver) {
        formData.append('second_approver_id', this.selectedLeaveRedemption.second_approver.id);
        formData.append('second_approver_status', 'pending');
      }

      await $api.post(this.getUrl + '/edit', formData)
          .then(response => {
            this.showEditModal = !this.showEditModal
            useModalStore().finishLoad()
            this.$toast.add({
              severity: 'success',
              summary: 'Success',
              detail: response.data.message,
              life: 3000
            })
            this.refreshData()
          }).catch(error => {
            useModalStore().show(error.response.data.message)
          });
    },

    async uploadAttachmentsAPI(files) {

      let formData = new FormData;

      formData.append('id', this.selectedLeaveRedemption.id);

      // Loop over the files array and append each file individually
      for (let i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
      }

      const config = {headers: {'Content-Type': 'multipart/form-data'}};
      await $api.post(this.getUrl + '/attachment/upload', formData, config)
          .then(response => {
            this.showAddAttachmentModal = false;
            this.$toast.add({
              severity: 'success',
              summary: 'Success',
              detail: 'Attachment uploaded',
              life: 3000
            })
            this.refreshData()
          })
          .catch(error => {
            useModalStore().show(error.response.data.message)
          });
    },

    async deleteAttachmentAPI(attachmentId) {
      this.showConfirmationModal = false;

      const params = {
        attachment_id: attachmentId,
        id: this.selectedLeaveRedemption.id
      }

      await $api.delete(this.getUrl + '/attachment/delete', {params})
          .then(response => {
            let index = this.selectedLeaveRedemption.attachment.findIndex(obj => obj.id === attachmentId);
            this.selectedLeaveRedemption.attachment.splice(index, 1);
            this.$toast.add({
              severity: 'success',
              summary: 'Success',
              detail: 'Attachment deleted',
              life: 1000
            })
          })
          .catch(error => {
            useModalStore().show(error.response.data.message)
          });
    },
  },
  computed: {
    ...mapState(useModalStore, ['modal']),
    getUrl() {
      return '/api/' + (this.active === 0 ? 'redeem-replacement-leave' : 'redeem-offshore-leave')
    },
    getAttachmentUrl() {
      return localStorage.getItem('currentUrl') + (this.active === 0 ? '/redeem-replacement-leave/' : ('/redeem-offshore-leave/' + this.selectedLeaveRedemption.id + '/'))
    }
  }
}

</script>
