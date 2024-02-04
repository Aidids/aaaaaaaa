<script>
export default {
  props : {
    boolState: {
      type: Boolean,
      default: false,
    },
    label: {
      type: String,
      default: ''
    },
    iconStyle: {
        type: String,
        default: "font-size: 1rem"
    }
  }
}
</script>

<template>
  <div v-if="boolState" class="p-1 badge border border-secondary-subtle border-2">
    <i class="bi bi-check-circle-fill fst-normal text-success" :style="iconStyle"><span class="ms-1">{{ label }}</span></i>
  </div>
  <div v-else class="p-1 badge border border-secondary-subtle border-2">
    <i class="bi bi-x-circle-fill fst-normal text-danger" :style="iconStyle"><span class="ms-1">{{ label }}</span></i>
  </div>
</template>

