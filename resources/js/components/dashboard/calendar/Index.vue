<template>
  <SidebarFilter
      :params="params"
      :toggle="toggleFilter"
      :is-searchable="false"
      :is-status="false"
      @closeToggle="toggleFilter = false"
      @search="search"
      @reset="reset"
  />
  <div class="d-flex justify-content-between">
    <h4 class="mb-0">Calendar</h4>
    <Button :disabled="loading" type="button" label="Filter" icon="bi bi-filter" :badge="filterBadge"
            @click="toggleFilter = true"/>
  </div>
  <div class="border bg-white text-secondary d-inline-block p-2">
    <i class="bi bi-buildings-fill"></i>
    Department: {{getDeptName()}}
  </div>
  <div class="border bg-white text-secondary d-inline-block p-2">
    <i class="bi bi-clock-fill"></i> Leave Period: {{this.formatDateToMonth(this.params.month)}}
  </div>
  <div class="border bg-white">
    <div v-if="loading" class="d-flex justify-content-center align-items-center" style="height: 50vh;">
      <loader :large="false"/>
    </div>
    <main v-show="! loading">
      <VCalendar ref="calendar" :disable-page-swipe="true" :attributes="this.calendarData" expanded borderless/>
      <div class="d-flex align-items-center mt-3">
        <div class="border flex-fill"></div>
        <strong class="text-uppercase text-secondary mx-2" style="font-size: 10.5px;">staff on leave</strong>
        <div class="border flex-fill"></div>
      </div>
      <div class="overflow-y-auto" style="max-height: 35vh;">
        <StaffLeaveList v-for="i in list" :user="i" :key="i"/>
        <EmptyState v-if="list.length === 0" title="" subtitle="There are no leave request available"/>
      </div>
    </main>
  </div>

</template>

<script>
import SidebarFilter from "../../elements/SidebarFilter.vue";
import setupCalendar from "../../../mixins/setupCalendar";
import StaffLeaveList from "../list/StaffLeaveList.vue";
import EmptyState from "../../elements/EmptyState.vue";
import $api from "../../api";
import Button from 'primevue/button';
import Conversion from "../../../mixins/conversion";
import {useDepartmentStore} from "../../../stores/getDepartment";
import { mapActions } from "pinia";

export default {
  props: ['deptId'],

  components: {StaffLeaveList, EmptyState, SidebarFilter, Button},

  mixins: [setupCalendar, Conversion],

  async created() {
    await this.resetCalendar();
    await this.setupParams();
    await this.getApi();
  },

  data() {
    return {
      params: {},
      toggleFilter: false,
      loading: true,
      list: [],
      departmentLeaves: [],
      calendarData: [],
    }
  },

  methods: {
    async resetCalendar() {
      this.calendarData = [
        ...this.attributes,
      ];
    },

    async setupParams() {
      this.params = {
        department_id: this.deptId,
        month: this.formDataDate(new Date())
      };
    },

    getDeptName() {
      return useDepartmentStore().getDepartmentName(this.params.department_id);
    },

    async search() {
      this.loading = true;
      await this.resetCalendar();
      await this.$refs.calendar.move(this.params.month);
      this.toggleFilter = false;
      this.list = [];
      this.departmentLeaves = [];
      await this.getApi();
    },

    async reset() {
      this.loading = true;
      await this.resetCalendar();
      await this.setupParams();
      await this.$refs.calendar.move(this.params.month);
      this.toggleFilter = false;
      this.list = [];
      this.departmentLeaves = [];
      await this.getApi();
    },

    async getApi() {
      let params = this.params;

      await $api.get('/api/department-leave', {params}).then(response => {
        response.data.forEach((att) => {
          this.list.push({
            department_id: att.department_id,
            id: att.id,
            name: att.name,
            start_date: att.start_date,
            start_date_type: att.start_date_type,
            end_date: att.end_date,
            end_date_type: att.end_date_type,
            duration: att.duration,
            overall_status: att.overall_status,
            leave_balance_id: att.leave_balance_id,
            leave_type_id: att.leave_type_id,
            leave_name: att.leave_name,
            color: this.leaveTypeColor(att.leave_name),
          });

          this.departmentLeaves.push({
            highlight: {
              color: this.leaveTypeColor(att.leave_name),
            },
            dates: {
              start: new Date(att.start_date),
              end: new Date(att.end_date)
            },
            popover: {
              label: `${att.name} ${att.leave_name}`
            }
          });
        })

        this.calendarData = [
          ...this.attributes,
          ...this.departmentLeaves
        ];

        this.loading = false;
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

    ...mapActions(useDepartmentStore, ['getDepartmentName'])
  }
}
</script>