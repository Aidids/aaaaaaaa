<template>
  <div class="d-flex flex-column" style="min-width: 450px;">
    <div class="p-3">
      <h5 v-if="!fullBorder" class="text-secondary">{{ request.requester ? request.requester.name : '' }}</h5>
      <small class="text-secondary d-block">Created at: {{ humanReadableDate(request.created_at) }}</small>
      <span class="badge text-capitalize" :class="getStatusOption(request.status)">
        {{ displayApproverBadge(request) }}
      </span>
      <span v-if="request.custom_approver"
            class="badge bg-info text-capitalize ms-1">Custom Approvers</span>
    </div>

    <div :class="[fullBorder ? 'd-flex' : 'd-md-flex']" class="justify-content-around my-auto pb-2">
      <VerticalBadge
          title="Allowance"
          icon="bi bi-cash-coin"
          :value="`RM ${request.total_allowance.toFixed(2)}`"
      />
      <VerticalBadge
          title="Transport"
          icon="bi bi-car-front-fill"
          :value="`RM ${request.total_transport.toFixed(2)}`"
      />
      <VerticalBadge
          title="Expenses"
          icon="bi bi-credit-card-fill"
          :value="`RM ${request.total_expense.toFixed(2)}`"
      />
    </div>
    <div :class="[fullBorder ? 'd-flex' : 'd-md-flex']" class="justify-content-around border-top px-3">
      <div class="text-center my-1">
        <strong class="text-secondary">{{ humanReadableDate(request.submission_month) }}</strong>
        <small class="text-secondary d-block">Period</small>
      </div>
      <div class="border-start"></div>
      <div class="text-center my-1">
        <strong class="color-primary">RM
          {{ (request.total_allowance + request.total_transport + request.total_expense).toFixed(2) }}</strong>
        <small class="text-secondary d-block">Total</small>
      </div>
    </div>
  </div>
</template>

<script>
import statusOption from "../../../../mixins/statusOption";
import conversion from "../../../../mixins/conversion";
import VerticalBadge from "../../../elements/VerticalBadge.vue";

export default {
  components: {VerticalBadge},
  props: {
    request: {
      type: Object,
      default: {}
    },
    fullBorder: {
      type: Boolean,
      default: false,
    }
  },
  mixins: [statusOption, conversion],
  data() {
    return {
      validExpenseTypes: ['Parking', 'Toll', 'Fuel', 'Public Transport']
    }
  }
}
</script>
