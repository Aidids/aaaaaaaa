<template>
  <div :class="wrapperClass">
    <label :class="{'text-disabled' : isDisabled}">Head of Department</label>
    <Dropdown
        :model-value="modelValue"
        :options="headOfDepartments"
        optionLabel="name"
        :option-value="headOfDepartments.id"
        :disabled="isDisabled"
        :filter="true"
        placeholder="Search Head Of Department"
        :showClear="true"
        :class="error && 'p-invalid'"
        class="w-100 mt-2"
        @update:modelValue="$emit('update:modelValue', $event)">
      <template #value="slotProps">
        <p v-if="slotProps.value">{{ slotProps.value.name }}</p>
        <span v-else>{{ slotProps.placeholder }}</span>
      </template>
      <template #option="slotProps">{{ slotProps.option.name }}</template>
    </Dropdown>
  </div>
</template>

<script>
import {mapState} from "pinia";
import {useApproverStore} from "../../stores/approvers";

export default {
  emits: ['update:modelValue'],

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
    ...mapState(useApproverStore, ['headOfDepartments']),
  }
};
</script>
