<template>
  <vue-final-modal :click-to-close="false" v-slot="{params, close}" v-bind="$attrs" classes="modal-container"
                   content-class="modal-content">
    <div style="max-height: 90vh; overflow-y: auto; min-width: 80vw;">
      <div class="modal-header">
        <h5 class="modal-title">{{ title }}</h5>
        <i class="bi bi-x-circle close-icon" @click="close"/>
      </div>
      <div class="modal-body">
        <hr>
        <FormMultipleAttachment
            ref="form"
            :approvers-exists="true"
            @update:fileChange="files = $event"/>
        <AttachmentList :attachment_url="attachment_url" :attachments="attachments"
                        @delete="$emit('delete', $event)" title="Recent Attachments" :can-delete="true"/>

      </div>
      <div class="modal-footer mt-3">
        <button @click="onCancel(close)" type="button" class="btn btn-secondary me-2">Cancel</button>
        <button v-if="files.length > 0" @click="onConfirm" type="submit"
                class="btn text-center btn-success">Upload
        </button>
      </div>
    </div>
  </vue-final-modal>

  <Modal v-model="modal.show" :modal="modal" :is-close="modal.isClose"></Modal>

</template>

<script>
import FormMultipleAttachment from "./FormMultipleAttachment.vue";
import {useVuelidate} from "@vuelidate/core";
import Modal from "../Modal.vue";
import AttachmentList from "./AttachmentList.vue";

export default {
  setup() {
    return {v$: useVuelidate()}
  },
  components: {AttachmentList, Modal, FormMultipleAttachment},
  emits: ['confirm', 'delete'],
  inheritAttrs: false,
  props: {
    attachments: {
      type: Array,
      default: [],
    },
    attachment_url: {
      type: String,
      default: '',
    },
    title: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      files: [],
      modal: {
        show: false,
        loader: false,
        isClose: true,
        message: ''
      }
    }
  },
  validations() {
    return {
      files: {
        required: function () {
          return this.files.length <= 10;
        }
      }
    }
  },
  methods: {
    async onConfirm() {
      const validated = await this.v$.$validate()

      if (!validated) {
        return this.modal = {
          show: true,
          loader: false,
          isClose: true,
          message: 'Only maximum of 10 attachments are allowed. Please remove some of your attachments to continue'
        }
      }

      this.$refs.form.$refs.file.files = [];
      this.$refs.form.count = 0;
      this.$emit('confirm', this.files);
      this.files = [];
    },

    onCancel(close) {
      this.$refs.form.$refs.file.files = [];
      this.$refs.form.count = 0;
      this.files = [];
      close();
    }
  }
}
</script>
