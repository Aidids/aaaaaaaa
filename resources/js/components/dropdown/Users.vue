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
        :showClear="true"
        class="w-100"
        @update:modelValue="$emit('update:modelValue', $event)"
    >
      <template #value="slotProps">
        <div v-if="slotProps.value">
          <div>{{ slotProps.value.name }}</div>
        </div>
        <span v-else>
            {{ slotProps.placeholder }}
          </span>
      </template>
      <template #option="slotProps">
        {{ slotProps.option.name }}
      </template>
    </Dropdown>
  </div>
</template>

<script>
import {mapState} from "pinia";
import {useUserStore} from "../../stores/users";

export default {
  created() {
    useUserStore().init();
  },

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
    ...mapState(useUserStore, ['users']),
  }
};
</script>
