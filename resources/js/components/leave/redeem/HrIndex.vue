<template>
  <Toast/>
  <h4 class="mb-3">Leave Redemption Summary</h4>
  <TabView v-model:activeIndex="active" class="eform-tab" @tab-change="updateUrl(tabs, active)">
    <TabPanel>
      <template #header>Replacement Leave</template>
      <keep-alive>
        <RedemptionList ref="replacement-leave-summary" v-if="active === 0" :url="getUrl"
                        type="summary" @view="viewRequest($event, false)"
                        @view-attachment="viewAttachment($event)" @approve="viewRequest($event, true)"/>
      </keep-alive>
    </TabPanel>
    <TabPanel>
      <template #header>Offshore Leave</template>
      <keep-alive>
        <RedemptionList ref="offshore-leave-summary" v-if="active === 1" :url="getUrl"
                        type="summary" @view="viewRequest($event, false)"
                        @view-attachment="viewAttachment($event)" @approve="viewRequest($event, true)"/>
      </keep-alive>
    </TabPanel>
  </TabView>
  <Modal v-model="modal.show" :modal="modal" :is-close="modal.isClose"/>
  <ActionModal v-model="showActionModal" :action='approvalAction' @confirm="actionRequestAPI($event)">
    <div v-if="approvalAction === 'approved'">
      <p class="form-label mb-2">Redeemable Days</p>
      <InputNumber v-model="selectedBalance" showButtons
                   :step="0.5" :min="0" :max="20"
                   buttonLayout="horizontal"
                   incrementButtonIcon="pi pi-plus" decrementButtonIcon="pi pi-minus"
                   :inputStyle="{'width': '50px'}"
                   class="mb-3"
                   mode="decimal"/>
    </div>
  </ActionModal>
  <ViewAttachmentModal
      v-model="showViewAttachmentModal" :attachments="selectedLeaveRedemption.attachment"
      :attachment_url="getAttachmentUrl"/>
  <ViewRequestModal
      v-model="showRequestModal" :data="selectedLeaveRedemption" :is-approve-view="isApproveView"
      @action="confirmAction($event)"/>
</template>

<script>

import ViewAttachmentModal from "../../elements/attachments/ViewAttachmentModal.vue";
import ViewRequestModal from "./ViewRequestModal.vue";
import RedemptionList from "./RedemptionList.vue";
import ActionModal from "../../elements/ActionModal.vue";
import Modal from "../../elements/Modal.vue";
import {useModalStore} from "../../../stores/modal";
import tabOption from "../../../mixins/tabOption";
import {mapState} from "pinia";
import Toast from "primevue/toast";
import TabView from "primevue/tabview";
import TabPanel from "primevue/tabpanel";
import InputNumber from "primevue/inputnumber";
import $api from "../../api";
import conversion from "../../../mixins/conversion";

export default {
  components: {
    Modal, ActionModal,
    RedemptionList,
    ViewRequestModal, ViewAttachmentModal,
    Toast, TabView, TabPanel, InputNumber
  },
  mixins: [tabOption, conversion],
  data() {
    return {
      active: 0,
      tabs: [
        'replacement-leave',
        'offshore-leave'
      ],
      selectedLeaveRedemption: {},
      showViewAttachmentModal: false,
      showRequestModal: false,
      showActionModal: false,
      isApproveView: false,
      approvalAction: '',
      selectedBalance: 0,
    };
  },
  created() {
    this.active = this.getTabIndex(this.tabs)
    useModalStore().init()
  },
  methods: {
    viewRequest(request, isApproveView) {
      this.isApproveView = isApproveView
      this.selectedLeaveRedemption = request
      this.showRequestModal = !this.showRequestModal
    },

    confirmAction(action) {
      this.showRequestModal = false
      this.approvalAction = action
      this.selectedBalance = this.active === 0 ? 0 : Math.floor((this.rawDaysBetweenDate(this.selectedLeaveRedemption.start_date, this.selectedLeaveRedemption.end_date) / 7))
      this.showActionModal = true
    },

    viewAttachment(request) {
      this.selectedLeaveRedemption = request
      this.showViewAttachmentModal = !this.showViewAttachmentModal
    },

    refreshData() {
      this.active === 0 ? this.$refs["replacement-leave-summary"].getLeaveRedemptionAPI() : this.$refs["offshore-leave-summary"].getLeaveRedemptionAPI()
    },

    async actionRequestAPI(remark) {

      let formData = new FormData();
      formData.append('id', this.selectedLeaveRedemption.id)
      formData.append('hr_status', this.approvalAction)
      formData.append('hr_date', this.formDataDate(new Date()))
      formData.append('hr_remark', remark)

      if (this.selectedBalance) {
        formData.append('added_qty', this.selectedBalance)
      }

      await $api.post(this.getUrl, formData)
          .then(response => {
            useModalStore().finishLoad()
            this.showActionModal = false
            this.$toast.add({
              severity: 'success',
              summary: 'Success',
              detail: response.data.message,
              life: 3000
            })
            this.refreshData()
          })
          .catch(error => {
            useModalStore().show(error.response.data.message)
          })
    }
  },
  computed: {
    ...mapState(useModalStore, ['modal']),
    getUrl() {
      return '/api/' + (this.active === 0 ? 'redeem-replacement-leave' : 'redeem-offshore-leave') + '/summary'
    },
    getAttachmentUrl() {
      return localStorage.getItem('currentUrl') + (this.active === 0 ? '/redeem-replacement-leave/' : ('/redeem-offshore-leave/' + this.selectedLeaveRedemption.id + '/'))
    },
  }
}
</script>
