<template>
  <profile-card :data="this.data" :api-call="true"/>
  <div class="w-100 mt-4">
    <TabView v-model:activeIndex="active" class="custom-tab">
      <TabPanel>
        <template #header>
          <i class="bi bi-person-circle me-2"></i>
          <span class="custom-tab-label" :class="{ active: active === 0 }">Personal Information</span>
        </template>
        <PersonalInfo :user_id="id"/>
      </TabPanel>
      <TabPanel>
        <template #header>
          <i class="bi bi-people me-2"></i>
          <span class="custom-tab-label" :class="{ active: active === 1 }">Family Information</span>
        </template>
        <FamilyInfo :user_id="id"/>
      </TabPanel>
      <TabPanel>
        <template #header>
          <i class="bi bi-geo-alt me-2"></i>
          <span class="custom-tab-label" :class="{ active: active === 2 }">Address Details</span>
        </template>
        <Address :user_id="id"/>
      </TabPanel>
      <TabPanel>
        <template #header>
          <i class="bi bi-telephone me-2"></i>
          <span class="custom-tab-label" :class="{ active: active === 3 }">Emergency Contact</span>
        </template>
        <Emergency :user_id="id"/>
      </TabPanel>
      <TabPanel>
        <template #header>
          <i class="bi bi-calendar-week me-2"></i>
          <span class="custom-tab-label" :class="{ active: active === 4 }">Leave Summary</span>
        </template>
        <LeaveSummary :user_id="id"/>
      </TabPanel>
      <TabPanel>
        <template #header>
          <i class="bi bi-folder2-open me-2"></i>
          <span class="custom-tab-label" :class="{ active: active === 5 }">Documents</span>
        </template>
        <Documents :user_id="id"/>
      </TabPanel>
      <TabPanel>
        <template #header>
          <i class="bi bi-person-vcard me-2"></i>
          <span class="custom-tab-label" :class="{ active: active === 6 }">Job Profile</span>
        </template>
        <JobProfile :jobProfile="data"/>
      </TabPanel>
      <TabPanel>
        <template #header>
          <i class="bi bi-person-fill-lock me-2"></i>
          <span class="custom-tab-label" :class="{ active: active === 7 }">Fixed Approvers</span>
        </template>
        <FixedApprovers :user="data" v-if="data.id"/>
      </TabPanel>
    </TabView>
  </div>

</template>

<script>
import ProfileCard from "../dashboard/ProfileCard.vue";
import $api from '../api.js';
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import JobProfile from "./tabs/JobProfile.vue";
import PersonalInfo from "./tabs/PersonalInfo.vue";
import LeaveSummary from "./tabs/LeaveSummary.vue"
import Documents from "./tabs/Documents.vue";
import Address from "./tabs/Address.vue";
import Emergency from "./tabs/Emergency.vue";
import FamilyInfo from "./tabs/FamilyInfo.vue";
import FixedApprovers from "./tabs/FixedApprovers.vue";
import {usePersonalInfo} from "../../stores/getPersonalInfo";
import {useDynamicUsersStore} from "../../stores/dynamicUsers";


export default {
  components: {
    FamilyInfo,
    Emergency,
    Address,
    Documents,
    JobProfile,
    ProfileCard,
    TabView,
    TabPanel,
    PersonalInfo,
    LeaveSummary,
    FixedApprovers
  },
  data() {
    return {
      data: [],
      id: null,
      showModal: false,
      active: 0,
    }
  },
  created() {
    let url = window.location.href;
    this.id = url.split('/')[4];
    this.getUser(this.id);
    usePersonalInfo().getPersonalInformationApi(this.id);
  },
  methods: {
    async getUser(id) {
      await $api.get('/api/profile-settings/' + id)
          .then(response => {
            this.data = response.data.data;
            useDynamicUsersStore().init(this.data,true);
          });
    },
  }
}
</script>
