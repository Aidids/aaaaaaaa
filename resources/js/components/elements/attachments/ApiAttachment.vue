<template>
  <label class="mb-1">Attachment</label>
  <FileUpload
      v-if="!src"
      mode="basic"
      ref="file"
      name="file"
      accept=".msg, application/pdf, message/rfc822"
      :maxFileSize="1000000"
      :with-credentials="true"
      @change="onUpload"
  />
  <div class="h-100" v-else>
      <!-- if EML/MSG is displayed here, download will be prompted -->
    <iframe
        v-if="src && src.endsWith('.pdf')"
        :src="'/travel-claim-attachment/' + this.travelId + '/' + this.api + '/' + src"
        height="90%"
        width="100%"
    />
    <a :href="'/travel-claim-attachment/' + this.travelId + '/' + this.api + '/' + src" class="d-block text-decoration-none mb-1" download v-else>
      <i class="bi bi-envelope me-1"></i>
      Email File Uploaded
    </a>
    <small @click="onDelete" class="text-danger pointer">Remove attachment</small>
  </div>
  <FormError :error="this.error" error-text="Attachment is required"/>
</template>

<script>
import $api from "../../api";
import {useModalStore} from "../../../stores/modal";
import FormError from "../FormError.vue";

export default {
  components: {FormError},
  props: ['id', 'travelId', 'api', 'path', 'error'],

  emits: ['updatePath'],

  created() {
    this.src = this.path;
  },

  data() {
    return {
      src: null,
    }
  },

  methods: {
    async onUpload() {
      let formData = new FormData();
      formData.append('file', this.$refs.file.files[0])

      useModalStore().load();

      await $api.post('/api/' + this.api + '-attachment/' + this.id, formData)
          .then((response) => {
            this.src = response.data.data.path;
            this.$emit('updatePath', response.data.data.path);
            useModalStore().show(response.data.message);
          })
          .catch((response) => {
            useModalStore().show(response.response.data.message);
            this.$refs.file.files = null;
          })
    },

    async onDelete() {
      await $api.delete('/api/' + this.api + '-attachment/' + this.id)
          .then((response) => {
            this.src = null;
            this.$emit('updatePath', null);
            useModalStore().show(response.data.message);
          })
          .catch((response) => {
            this.src = null;
            this.$emit('updatePath', null);
            useModalStore().show(response.response.data.message);
          })
    }
  },
};
</script>
