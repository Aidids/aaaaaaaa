<template>
    <Toast/>
    <h4 class="mb-3">Leave Redemption Approval</h4>
    <TabView v-model:activeIndex="active" class="eform-tab" @tab-change="updateUrl(tabs, active)">
      <TabPanel>
        <template #header>Replacement Leave</template>
        <keep-alive>
          <RedemptionList ref="replacement-leave-approval" v-if="active === 0" :url="getUrl" :approver_id="approver_id"
                          type="approval" @view="viewRequest($event, false)"
                          @view-attachment="viewAttachment($event)" @approve="viewRequest($event, true)"/>
        </keep-alive>
      </TabPanel>
      <TabPanel>
        <template #header>Offshore Leave</template>
        <keep-alive>
          <RedemptionList ref="offshore-leave-approval" v-if="active === 1" :url="getUrl" :approver_id="approver_id"
                          type="approval" @view="viewRequest($event, false)"
                          @view-attachment="viewAttachment($event)" @approve="viewRequest($event, true)"/>
        </keep-alive>
      </TabPanel>
    </TabView>

  <Modal v-model="modal.show" :modal="modal" :is-close="modal.isClose"/>
  <ActionModal v-model="showActionModal" :action='approvalAction' @confirm="actionRequestAPI($event)"/>
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
import {useModalStore} from "../../../stores/modal";
import tabOption from "../../../mixins/tabOption";
import TabView from "primevue/tabview";
import TabPanel from "primevue/tabpanel";
import Toast from "primevue/toast";
import Modal from "../../elements/Modal.vue";
import $api from "../../api";
import {mapState} from "pinia";
import conversion from "../../../mixins/conversion";
import ActionModal from "../../elements/ActionModal.vue";

export default {
  components: {
    ActionModal,
    Modal,
    RedemptionList, ViewAttachmentModal, ViewRequestModal,
    TabView, TabPanel, Toast
  },
  mixins: [tabOption, conversion],
  data() {
    return {
      active: 0,
      tabs: [
        'replacement-leave',
        'offshore-leave'
      ],
      user_id: parseInt(localStorage.getItem('user_id')),
      selectedLeaveRedemption: {},
      showViewAttachmentModal: false,
      showRequestModal: false,
      showActionModal: false,
      isApproveView: false,
      approvalAction: '',
      approver_level: null,
      approver_id: null,
    }
  },
  created() {
    this.active = this.getTabIndex(this.tabs)
    useModalStore().init()
    this.getApproverLevelAPI(this.user_id)
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
      this.showActionModal = true
    },

    viewAttachment(request) {
      this.selectedLeaveRedemption = request
      this.showViewAttachmentModal = !this.showViewAttachmentModal
    },

    refreshData() {
      this.active === 0 ? this.$refs["replacement-leave-approval"].getLeaveRedemptionAPI() : this.$refs["offshore-leave-approval"].getLeaveRedemptionAPI()
    },

    async actionRequestAPI(remark) {
      useModalStore().load()

      let formData = new FormData()
      formData.append('id', this.selectedLeaveRedemption.id)
      formData.append(this.approver_level + '_id', this.user_id)
      formData.append(this.approver_level + '_status', this.approvalAction)
      formData.append(this.approver_level + '_remark', remark)
      formData.append(this.approver_level + '_date', this.formDataDate(new Date()))

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
    },

    async getApproverLevelAPI(userId) {
      await $api.get('/api/approver/' + userId)
          .then(response => {
            this.approver_id = response.data.data.approver_id
            this.approver_level = response.data.data.approver_level
          })
    },

  },
  computed: {
    ...mapState(useModalStore, ['modal']),
    getUrl() {
      return '/api/' + (this.active === 0 ? 'redeem-replacement-leave' : 'redeem-offshore-leave') + '/approve'
    },
    getAttachmentUrl() {
      return localStorage.getItem('currentUrl') + (this.active === 0 ? '/redeem-replacement-leave/' : ('/redeem-offshore-leave/' + this.selectedLeaveRedemption.id + '/'))
    }
  }
}
</script>
