<template>
  <div class="approver-preview pt-4">
    <div class="d-flex flex-column align-items-center mb-4">
      <TableProfile :user="approvers.user" :card="true" card-label="You"/>
    </div>
    <div class="d-flex align-items-center" v-for="(i, index) in approvers.approvers">
      <div class="arrow-up right">
        <span></span>
        <span></span>
        <span></span>
      </div>
      <div class="d-flex flex-column align-items-center">
        <TableProfile :user="i" :card="true" />
        <small class="mb-4 d-block">{{ setOrdinalNumber(index + 1) + ' approver' }}</small>
      </div>
    </div>
  </div>
</template>

<script>
import TableProfile from "./TableProfile.vue";
import Conversion from "../../mixins/conversion";
export default {
  components: {TableProfile},
  mixins: [Conversion],
  props: {
    wrapperClass: {
      type: String,
      default: 'my-3'
    },
    approvers: {
      type: Object,
      default: null,
    }
  },

}
</script>