<template>
  <loader v-if="loading" :large="false"/>
  <div v-else>
    <h4>{{ this.data.department }} Department Calendar</h4>
    <div class="d-xl-flex bg-white shadow rounded mb-2">
      <VCalendar :attributes="this.attributes" class="w-100" borderless/>
      <div class="border-xl-start w-100" v-if="list.length > 0">
        <div class="d-flex align-items-center mt-3">
          <div class="border flex-fill"></div>
          <strong class="text-uppercase text-secondary mx-2" style="font-size: 10.5px;">staff on leave</strong>
          <div class="border flex-fill"></div>
        </div>
        <div class="overflow-y-auto" style="max-height: 334px;">
          <StaffLeaveList v-for="i in list" :user="i" :key="i"/>
          <EmptyState v-if="list.length === 0" title="" subtitle="Currently, no one in your department is on leave"/>
        </div>
      </div>
    </div>
    <small class="text-success ms-1" v-if="list.length > 0">
      <i class="bi bi-info-circle-fill"></i>
      Calendar above displays leave requests for the current month and the two surrounding months
    </small>
  </div>
</template>

<script>
import setupCalendar from "../../mixins/setupCalendar";
import StaffLeaveList from "./list/StaffLeaveList.vue";
import EmptyState from "../elements/EmptyState.vue";
import {mapState} from "pinia";
import {useDepartmentLeaveStore} from "../../stores/departmentLeave";

export default {
  components: {StaffLeaveList, EmptyState},

  mixins: [setupCalendar],

  props: {
    data: {
      type: Object,
      required: true
    }
  },

  async mounted() {
    await useDepartmentLeaveStore().init(this.data.department_id);
    await this.setupData();
  },

  data() {
    return {
      loading: true,
    }
  },

  methods: {
    async setupData() {
      this.attributes = [
          ...this.attributes,
          ...this.departmentLeaves
      ];
      this.loading = false;
    }
  },

  computed: {
    ...mapState(useDepartmentLeaveStore, ['departmentLeaves', 'list'])
  }
}
</script>
