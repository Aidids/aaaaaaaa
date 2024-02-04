<template>
  <vue-final-modal :click-to-close="false" v-slot="{ params, close }" v-bind="$attrs" classes="modal-container"
                   content-class="modal-msg">
    <div v-if="modal.loader">
      <loader :large="false"/>
      <p>Please do not close or refresh the browser</p>
    </div>
    <div v-else-if="modal.message" class="text-center">
      <p class="mb-3" v-html="modal.message"></p>
      <div class="d-flex justify-content-center gap-2">
        <button v-if="isClose" @click="close" type="button" class="btn btn-secondary">Close</button>
        <button v-else @click="$emit('complete', close)" type="button" class="btn btn-secondary">Close</button>
        <button v-if="hasConfirm" @click="$emit('confirm', close)" type="button" class="btn btn-success">Confirm
        </button>
      </div>
    </div>
  </vue-final-modal>
</template>

<script>
export default {
  props: {
    modal: {
      type: Object,
      default: {}
    },
    isClose: {
      type: Boolean,
      default: false,
    },
    hasConfirm: {
      type: Boolean,
      default: false,
    }
  },
  inheritAttrs: false,
  emits: ['confirm', 'complete'],
}
</script>
