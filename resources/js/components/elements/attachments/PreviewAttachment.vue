<template>
  <a v-if="this.href.endsWith('.eml') || this.href.endsWith('.msg')"  :href="this.href" class="text-decoration-none mb-1"
     :class="[this.isButton ? 'btn btn-outline-secondary' : '']"
     download
  >
    <i class="bi bi-envelope me-1"></i>
    <span :class="[this.isButton ? '' : 'text-decoration-underline']">
      {{ (this.name === 'Preview Attachment') ? 'Email Uploaded' : this.name}}
    </span>
  </a>
  <a v-else :href="this.href" target="_blank"
     :class="[this.isButton ? 'btn btn-outline-secondary m-1' : '']">
    {{this.name}}
  </a>
</template>
<script>
import $api from "../../api";

export default {
  props: {
    href: {
      type: String,
      default: ''
    },
    isButton: {
      type: Boolean,
      default: false,
    },
    name: {
      type: String,
      default: 'Preview Attachment'
    }
  },

  methods: {
    async downloadEmail() {
      // Use Axios to make the API request
      $api.get('/api/administration/excel-leave-summary', {
        responseType: 'blob'
      })
          .then(response => {
            // Create a blob from the response data
            const blob = new Blob([response.data], { type: response.headers['content-type'] });

            // Create a link element and trigger a download
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = 'excel-leave-summary.xlsx';
            link.click();
          })
          .catch(error => {
            console.error('Error downloading email:', error);
            // Handle error as needed
          });
    },
  }
}
</script>