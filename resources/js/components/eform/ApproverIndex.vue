<template>
  <Toast/>
  <h4 class="mb-2 me-2">Review E-Form</h4>
  <TabView v-model:activeIndex="active" class="eform-tab" @tab-change="updateUrl(tabs, active)">
      <TabPanel>
          <template #header>
              <i class="bi bi-currency-exchange me-2"></i>
              <span>Travel Claim</span>
          </template>
          <keep-alive>
              <TEHistory :is-approve-view="true" v-if="active === 0"/>
          </keep-alive>
      </TabPanel>
      <TabPanel>
      <template #header>
        <i class="bi bi-airplane-engines me-2"/>
        <span>Travel Authorization</span>
      </template>
      <keep-alive>
        <TAHistory :is-approve-view="true" v-if="active === 1"/>
      </keep-alive>
    </TabPanel>
  </TabView>

</template>
<script>

import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import TAHistory from "./travel/TAHistory.vue";
import TEHistory from "./expenses/TEHistory.vue";
import tabOption from "../../mixins/tabOption";
import Toast from "primevue/toast";

export default {
  components: {
    TEHistory,
    TAHistory,
    TabView,
    TabPanel,
    Toast
  },
  mixins: [tabOption],
  data() {
    return {
      active: 0,
      tabs: [
          'travel-claim',
          'travel-authorization'
      ]
    }
  },
  created() {
    this.active = this.getTabIndex(this.tabs);
  }
}
</script>
