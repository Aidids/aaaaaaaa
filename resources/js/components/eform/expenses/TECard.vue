<template>
  <div class="d-lg-flex border border-rounded bg-white shadow rounded mb-2">
    <TEHeader class="col-lg-4" :request="request"/>
    <div class="te-card-border flex-fill p-3">
      <BarChart :breakdown-data="request.breakdownData"/>
      <div v-if="isApproveView" class="text-end">
        <button class="btn btn-success m-1 mt-4" @click="$emit('review')">Review</button>
      </div>
      <div v-else class="text-end mt-4">
        <button class="btn btn-outline-secondary m-1" @click="preview(request.id)">View</button>
        <button v-if="request.status === 'pending' || request.status === 'rejected'"
                class="btn btn-outline-success m-1"
                @click="edit(request.id)">Edit</button>
        <button v-if="request.status === 'pending' || request.status === 'processing'"
                class="btn btn-outline-danger m-1" @click="$emit('cancel')">Cancel</button>
      </div>
    </div>
  </div>
</template>

<script>
import TEHeader from "./card/TEHeader.vue";
import BarChart from "./card/BarChart.vue";
import conversion from "../../../mixins/conversion";

export default {
  components: {BarChart, TEHeader},
  mixins: [conversion],
  props: {
    isApproveView: {
      type: Boolean,
      default: false
    },
    request: {
      type: Object,
      default: {}
    },
  },
  emits: ['review', 'cancel'],
  methods: {
    preview(id) {
      window.open(
          '/travel-claim/' + id + '/show',
          '_blank'
      );
    },
    edit(id) {
      window.location.href = '/travel-claim/' + id;
    },
  }
}
</script>
