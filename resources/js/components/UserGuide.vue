<template>
  <h4 class="mb-3 me-2">DESB APP User Guide</h4>
  <iframe
      :src="'/storage/user_guide.pdf'"
      height="90%"
      width="100%"
  />
</template>
<script>
import Button from 'primevue/button';

export default {
  components: {Button},

  props: {
    src: {
      type: String,
      default: ''
    }
  }
}
</script>