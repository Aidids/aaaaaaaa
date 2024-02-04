<template>
  <div v-if="approver_id !== 2" :class="wrapperClass">
    <label :class="{'text-disabled' : isDisabled}">Assigned Supervisor</label>
    <Dropdown
        :modelValue="modelValue"
        :options="supervisors"
        optionLabel="name"
        :option-value="supervisors.id"
        :disabled="isDisabled"
        :filter="true"
        placeholder="Search Supervisor"
        :showClear="true"
        :class="error && 'p-invalid'"
        class="w-100 mt-2"
        @update:modelValue="$emit('update:modelValue', $event)">
      <template #value="slotProps">
        <p v-if="slotProps.value">{{ slotProps.value.name }}</p>
        <span v-else>{{ slotProps.placeholder }}</span>
      </template>
      <template #option="slotProps">
        {{ slotProps.option.name }}
      </template>
    </Dropdown>
  </div>
</template>

<script>
import {mapState} from "pinia";
import {useApproverStore} from "../../stores/approvers";

export default {
  emits: ['update:modelValue'],

  created() {
    useApproverStore().init();
  },

  props: {
    modelValue: {
      type: Object,
      default: () => {
      },
    },
    isDisabled: {
      type: Boolean,
      default: false,
    },
    wrapperClass: {
      type: String,
      default: 'col-md-6'
    },
    error: {
      type: Boolean,
      default: false,
    },
  },

  computed: {
    ...mapState(useApproverStore, ['supervisors', 'headOfDepartments', 'approver_id']),
  }
};
</script>
