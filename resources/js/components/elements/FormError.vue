<template>
  <small v-if="this.error" class="text-danger mt-2">
    <i class="bi bi-exclamation-triangle"></i>
    {{ this.errorText }}
  </small>
</template>
<script>
export default {
  props: ['error', 'errorText']
}
</script>
