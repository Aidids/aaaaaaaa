<template>
  <vue-final-modal :click-to-close="true" v-slot="{ params, close }" v-bind="$attrs"
                   classes="modal-container" content-class="modal-content">
    <div style="max-height: 90vh; overflow-y: auto; min-width: 50vw;">
      <div class="modal-header">
        <h5 class="text-uppercase me-5 mb-0">{{ title }}</h5>
        <i class="bi bi-x-circle close-icon" @click="close"/>
      </div>
      <div class="modal-body">
        <hr>
        <AttachmentList :attachment_url="attachment_url" :attachments="attachments"
                        title="Click to Preview Attachment"/>
      </div>
    </div>


  </vue-final-modal>
</template>

<script>

import AttachmentList from "./AttachmentList.vue";

export default {
  components: {AttachmentList},
  inheritAttrs: false,
  props: {
    title: {
      type: String,
      default: 'Redemption Leave Attachment'
    },
    attachments: {
      type: Array,
      default: []
    },
    attachment_url: {
      type: String,
      default: ''
    },
  }
}
</script>
