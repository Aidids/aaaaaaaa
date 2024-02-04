<template>
  <div class="floating-button">
    <ButtonLoad v-if="!isEmpty" class="me-2" @click="$emit('add')">
      <i class="bi bi-plus-circle" style="font-size: 1rem; font-style: normal"/> {{addLabel}}
    </ButtonLoad>
    <ButtonLoad v-if="isEditing" wrapper-class="btn btn-secondary me-2" @click="$emit('cancel')">Cancel
    </ButtonLoad>
    <ButtonLoad v-else wrapper-class="btn btn-outline-secondary me-2 bg-white" @click="$emit('reset')">Reset
    </ButtonLoad>
    <ButtonLoad v-if="$parent.$parent.$data.activeIndex > 0" wrapper-class="btn btn-secondary me-2" @click="$parent.$parent.$data.activeIndex -= 1">Previous</ButtonLoad>
    <ButtonLoad v-if="isEmpty && $parent.$parent.$data.activeIndex < 3" wrapper-class="btn btn-success" @click="$parent.$parent.$data.activeIndex += 1">Skip</ButtonLoad>
    <ButtonLoad v-else wrapper-class="btn btn-success" @click="$emit('submit')">{{ submitLabel }}</ButtonLoad>
  </div>
</template>
<script>
import ButtonLoad from "../../../elements/ButtonLoad.vue"

export default {
  components: {ButtonLoad},
  props: {
    isEmpty: {
      type: Boolean,
      default: true,
    },
    isEditing: {
      type: Boolean,
      default: false,
    },
    addLabel: {
      type: String,
      default: ''
    },
    submitLabel: {
      type: String,
      default: 'Next'
    }
  },
  emits: ['add', 'cancel', 'reset', 'submit']
}
</script>
