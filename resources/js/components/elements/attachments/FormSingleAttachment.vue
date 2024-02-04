<template>
  <div v-if="showTitle">
    <h5 :class="{'text-disabled' : typeof approversExists === 'undefined'}" class="mt-4 mb-2">{{ title }}</h5>
    <div class="form-control py-3">
      <label :class="{'text-disabled' : typeof approversExists === 'undefined'}"
             class="form-label w-100">{{ subtitle }}</label>
      <input
          :disabled="typeof approversExists === 'undefined'"
          class="form-control"
          type="file"
          @change="onFileChange"
          ref="file">
      <PreviewAttachment v-if="attachment" :href="attachment.path"/>
      <FormError :error="error" error-text="Attachment is required"/>
    </div>
    <small
        :class="[typeof approversExists === 'undefined' ? 'text-disabled' : 'text-secondary']">{{
        description
      }}</small>
  </div>
  <div v-else>
    <label v-if="subtitle" :class="{'text-disabled' : typeof approversExists === 'undefined'}"
           class="form-label w-100">
      {{ subtitle }}</label>
    <input
        :disabled="typeof approversExists === 'undefined'"
        class="form-control"
        type="file"
        @change="onFileChange"
        ref="file">
  </div>
</template>

<script>
import FormError from "../FormError.vue";
import PreviewAttachment from "./PreviewAttachment.vue";


export default {
  components: {FormError, PreviewAttachment},
  emits: ['fileChange'],

  props: {
    title: {
      type: String,
      default: 'Attachments'
    },
    subtitle: {
      type: String,
      default: 'Upload attachment'
    },
    description: {
      type: String,
      default: 'Attachment is required as evidence for medical, hospitalisation, compassionate and out of office leave eligibility'
    },
    approversExists: {
      type: Boolean,
      default: false
    },
    attachment: {
      type: Object,
      default: null
    },
    attachmentPath: {
      type: String,
      default: ''
    },
    showTitle: {
      type: Boolean,
      default: true,
    },
    error: {
      type: Boolean,
      default: false
    }
  },

  methods: {
    onFileChange(event) {
      const file = this.$refs.file.files[0];
      const allowedExtensions = ['pdf', 'png', 'jpeg', 'jpg', 'heic', 'eml', 'msg'];
      const sizeMax = 20000000;

      // Check if the file extension is allowed
      const fileExtension = file.name.split('.').pop().toLowerCase();

      if (!allowedExtensions.includes(fileExtension)) {
        alert('Only PDF, PNG, and JPEG files are allowed');
        event.target.value = '';
      }

      if (file.size >= sizeMax) {
        alert('File size must be less than 20MB');
        event.target.value = '';
      }

      this.$emit('fileChange', this.$refs.file.files[0])
    },
  }
}
</script>
