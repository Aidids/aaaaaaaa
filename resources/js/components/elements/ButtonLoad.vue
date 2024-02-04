<template>
  <button v-if="load" :class="wrapperClass" disabled>
    <div class="d-flex align-items-center" style="">
      <small>Loading...</small>
      <div class="lds-button ms-2">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
      </div>
    </div>
  </button>
  <button :disabled="disabled" v-else :class="wrapperClass" @click="this.toggleLoad">
    <slot>
    </slot>
  </button>
</template>

<script>
import {mapState} from "pinia";
import {useLoadButton} from "../../stores/loadButton";

export default {
  emits: ['click'],
  props: {
    wrapperClass: {
      type: String,
      default: 'btn btn-outline-success'
    },
    disabled: {
      type: Boolean,
      default: false
    }
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
