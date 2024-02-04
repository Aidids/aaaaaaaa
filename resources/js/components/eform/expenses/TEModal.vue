<template>
  <vue-final-modal :click-to-close="true" v-slot="{ params, close }" v-bind="$attrs" classes="modal-container"
                   content-class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title">Travel Claim Request</h5>
      <i class="bi bi-x-circle close-icon" @click="close"/>
    </div>
    <hr>
    <div class="modal-body px-2 pb-2" style="overflow: auto; height: 70vh; width: 90vw;">
      <TEShow v-if="form_id" :is-modal-view="true" :form_id="form_id"/>
    </div>
    <div class="modal-footer justify-content-start ms-1 mt-3">
      <button class="btn btn-success m-1" @click="$emit('action', 'approved')">Approve</button>
      <button class="btn btn-danger m-1" @click="$emit('action', 'rejected')">Reject</button>
    </div>
  </vue-final-modal>
</template>

<script>
import TEShow from "./TEShow.vue";

export default {
  components: {
    TEShow,
  },
  props: ['form_id'],
  emits: ['action'],
}
</script>
