<template>
  <h5 v-if="showTitle" class="mt-4 mb-2"
      :class="{'text-disabled' : typeof approversExists === 'undefined'}">{{ title }}</h5>
  <div :class="[showTitle ? 'form-control py-3' : 'mt-2']">
    <label :class="{'text-disabled' : typeof approversExists === 'undefined'}"
           class="form-label mb-1">{{ title }}</label>
    <Textarea
      class="form-control"
      :class="error && 'border border-danger'"
      v-model="inputValue"
      type="textarea"
      :maxlength="150"
      :disabled="typeof approversExists === 'undefined'"
      style="resize: none;"
      :placeholder="placeholder"
    />
  </div>
  <small v-if="error" class="text-danger d-block">
    Missing {{ title }}
  </small>
  <p v-else class="w-100" :class="[typeof approversExists === 'undefined' ? 'text-disabled' : 'text-secondary']">
    {{ description }}
  </p>
</template>

<script>
import Textarea from "primevue/textarea";

export default {
  components: {
    Textarea
  },
  props: {
    showTitle: {
      type: Boolean,
      default: true
    },
    title: {
      type: String,
      default: 'Add Remarks'
    },
    description: {
      type: String,
      default: 'Remark is optional'
    },
    placeholder: {
      type: String,
      default: ''
    },
    approversExists: {
      type: Boolean,
      default: false
    },
    modelValue: {
      type: String,
      default: ''
    },
    error: {
      type: Boolean,
      default: false
    }
  },

  computed: {
    inputValue: {
      get() {
        return this.modelValue
      },
      set(value) {
        this.$emit('update:modelValue', value)
      }
    }
  },
};
</script>
