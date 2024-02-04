<template>
  <div :class="wrapperClass">
    <label class="form-label" :class="{'text-disabled' : isDisabled}">{{ title }}</label>
    <Dropdown
        :modelValue="modelValue"
        :options="users"
        optionLabel="name"
        :option-value="users.id"
        :disabled="isDisabled"
        :filter="true"
        placeholder="Search staff"
        :showClear="false"
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
import {useDynamicUsersStore} from "../../stores/dynamicUsers";

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
    title: {
      type: String,
      default: 'Select staff',
    }
  },

  computed: {
    ...mapState(useDynamicUsersStore, ['users']),
  }
};
</script>
