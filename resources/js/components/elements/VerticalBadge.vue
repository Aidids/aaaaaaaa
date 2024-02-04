<template>
  <div class="vertical-badge d-flex flex-column align-items-center my-1">
    <div class="shadow d-print-none mb-2">
      <i :class="icon" style="font-size: 2rem;"></i>
    </div>
    <strong class="text-secondary text-center" v-html="value"></strong>
    <small class="text-secondary text-center">{{title}}</small>
  </div>
</template>

<script>
export default {
  props: ['title', 'value', 'icon']
}
</script>
