<template>
  <div :class="wrapperClass">
    <label class="form-label">{{ title }}</label>
    <Dropdown
        :modelValue="modelValue"
        :options="adminLeaveType"
        optionLabel="name"
        :option-value="adminLeaveType.id"
        :disabled="isDisabled"
        :filter="true"
        placeholder="Search leave type"
        :showClear="true"
        class="w-100"
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
import {useLeaveTypeStore} from "../../stores/leaveType";

export default {
  data() {
    return {
      selectedLeaveType: {},
    }
  },

  created() {
    useLeaveTypeStore().init();
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
    title: {
      type: String,
      default: 'Select leave type',
    }
  },

  computed: {
    ...mapState(useLeaveTypeStore, ['adminLeaveType']),
  }
};
</script>
