<template>
  <div class="mt-2 form-control p-3 pt-2">
    <div class="d-flex justify-content-between align-items-center">
      <h5>{{ index + 1 }}</h5>
      <IconLoad @click="$emit('remove', index)">
        <i class="bi bi-trash text-danger"></i>
      </IconLoad>
    </div>
    <hr class="mt-1 mb-3">
    <slot></slot>
  </div>

</template>

<script>
import IconLoad from "./IconLoad.vue";

export default {
  components: {IconLoad},
  props: ['index'],
  emits: ['remove'],
}
</script>
