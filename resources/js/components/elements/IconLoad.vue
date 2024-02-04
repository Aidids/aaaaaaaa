<template>
  <div v-if="load" :class="wrapperClass" class="icon-loading-box" disabled>
    <div class="lds-button ms-2">
      <div></div>
      <div></div>
      <div></div>
      <div></div>
    </div>
  </div>
  <div class="icon-box" v-else :class="wrapperClass" @click="this.toggleLoad">
    <slot>

    </slot>
  </div>
</template>

<script>
import {mapState} from "pinia";
import {useLoadButton} from "../../stores/loadButton";

export default {
  emits: ['click'],
  props: {
    wrapperClass: {
      type: String,
      default: ''
    },
  },
  methods: {
    toggleLoad() {
      this.$emit('click');
    }
  },
  computed: {
    ...mapState(useLoadButton, ['load'])
  }
}
</script>