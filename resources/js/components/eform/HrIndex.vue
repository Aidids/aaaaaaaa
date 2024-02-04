<template>
  <Toast/>
  <h4 class="mb-2">E-Form Request Summary</h4>
  <TabView v-model:activeIndex="active" class="eform-tab" @tab-change="updateUrl(tabs, active)">
    <TabPanel :disabled="isProjectManager === 1">
      <template #header>
        <i class="bi bi-currency-exchange me-2"></i>
        <span>Travel Claim</span>
      </template>
      <keep-alive>
        <TESummary v-if="active === 0"/>
      </keep-alive>
    </TabPanel>
    <TabPanel>
      <template #header>
        <i class="bi bi-airplane-engines me-2"/>
        <span>Travel Authorization</span>
      </template>
      <keep-alive>
        <TASummary v-if="active === 1" :is-project="isProjectManager === 1"/>
      </keep-alive>
    </TabPanel>
  </TabView>
  <Modal v-model="modal.show" :modal="modal" :is-close="modal.isClose"/>
</template>

<script>
import TabView from "primevue/tabview";
import TabPanel from "primevue/tabpanel";
import TASummary from "./travel/TASummary.vue";
import TESummary from "./expenses/TESummary.vue";
import tabOption from "../../mixins/tabOption";
import Toast from "primevue/toast";
import Modal from "../elements/Modal.vue";
import {mapState} from "pinia";
import {useModalStore} from "../../stores/modal";

export default {
  props: {
    isProjectManager: {
      type: Number,
      default: 0,
    },
    projectAdminId: {
      type: Number,
    }
  },
  components: {Modal, TESummary, TASummary, TabView, TabPanel, Toast},
  mixins: [tabOption],
  data() {
    return {
      active: 1,
      tabs: [
        'travel-claim',
        'travel-authorization'
      ]
    }
  },
  created() {
    useModalStore().init()
    this.active = this.getTabIndex(this.tabs);
  },
  computed: {
    ...mapState(useModalStore, ['modal'])
  }
}
</script>
