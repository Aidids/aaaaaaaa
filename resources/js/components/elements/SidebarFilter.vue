<template>
  <Sidebar
      position="right"
      :visible="toggle"
      @update:visible="$emit('closeToggle')"
      :pt="{
        header: {
            style: 'justify-content: space-between; align-items: center;'
        }
      }"
  >
    <template #header>
      <h2 class="mb-0">Filter</h2>
    </template>
    <slot></slot>
    <div v-if="isSearchable">
      <label class="form-label">Search Employee</label>
      <div class="input-search my-2 my-xl-0">
        <i class="fa fa-search text-secondary"></i>
        <input
            :value="params.query"
            @input="params.query = $event.target.value"
            type="text"
            class="me-3 form-control"
            placeholder="Type here to search employee"
        />
      </div>
      <Divider/>
    </div>
    <div v-if="isDepartment">
      <label class="form-label">Select Department</label>
      <Dropdown class="w-100 rounded"
                :model-value="params.department_id"
                @update:model-value="params.department_id = $event"
                :options="departmentOption"
                optionValue="id"
                optionLabel="name"
                placeholder="Click here to select department"
      />
      <Divider/>
    </div>
    <div v-if="isMonth">
      <label class="form-label">Select Period</label>
      <Calendar :model-value="params.month"
                @update:model-value="params.month = this.formDataDate($event)"
                class="border-secondary w-100 rounded"
                view="month" dateFormat="mm/yy"
                placeholder="Click here to select month"
      />
      <Divider/>
    </div>
    <div v-if="isLeaveType">
      <label class="form-label">Select Leave Type</label>
      <Dropdown class="w-100 rounded"
                :model-value="params.status"
                @update:model-value="params.status = $event"
                :options="setStatusOptions()"
                optionLabel="label"
                optionValue="status"
                placeholder="Click here to select status"
      />
    </div>
    <div class="mt-4">
      <button :disabled="disabled" class="btn btn-success" @click="$emit('search')">Search</button>
      <Button :disabled="disabled" class="ms-1" label="Reset" link @click="$emit('reset')"/>
    </div>
  </Sidebar>
</template>
<script>
import Conversion from "../../mixins/conversion";
import Sidebar from 'primevue/sidebar';
import Divider from 'primevue/divider';
import Calendar from 'primevue/calendar';
import Button from 'primevue/button';
import {useDepartmentStore} from "../../stores/getDepartment";
import {mapState} from "pinia";
import filterOption from "../../mixins/filterOption";

export default {
  components: {Sidebar, Divider, Calendar, Button},

  mixins: [Conversion, filterOption],

  props: {
    toggle: {
      type: Boolean,
      default: false,
    },
    params: {
      type: Object,
      default: {}
    },
    isSearchable: {
      type: Boolean,
      default: true,
    },
    isMonth: {
      type: Boolean,
      default: true,
    },
    isDepartment: {
      type: Boolean,
      default: true,
    },
    statusOption: {
      type: String,
      default: ''
    },
    isLeaveType: {
      type: Boolean,
      default: false
    }
  },

  data() {
    return {
      disabled: true
    }
  },

  created() {
    useDepartmentStore().init();
  },

  methods: {
    setStatusOptions() {
      if (this.statusOption === 'fixed-approvers')
      {
        return this.fixedApproverStatus;
      }

      if (this.statusOption === 'hr-approval')
      {
        return this.hrApprovalStatus;
      }

      return this.standardStatus;
    }
  },

  computed: {
    ...mapState(useDepartmentStore, ['departmentOption']),
  },

  watch: {
    params: {
      deep: true,
      handler (n,o) {
        this.disabled = n !== o;
      }
    }
  }
}
</script>
