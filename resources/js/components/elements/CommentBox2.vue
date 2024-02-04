<template>
  <Timeline :value="request.approvers" align="alternate" class="customized-timeline">
    <template #marker="slotProps">
      <p class="text-center my-2" :class="this.getStatusTextColor(assignStatus(slotProps.index))"
         style="width: 7rem;">{{this.setOrdinalNumber(slotProps.index + 1)}} approver</p>
    </template>
    <template #connector="slotProps">
      <div class="p-timeline-event-connector"
           :class="this.getStatusOption(assignStatus(slotProps.index))">
      </div>
    </template>
    <template #content="slotProps">
      <Card class="mt-5">
        <template #title>
          {{ slotProps.item.name }}
        </template>
        <template #subtitle>
          <p>{{ slotProps.item.department }}</p>
          <span class="badge text-capitalize mt-2"
            :class="this.getStatusOption(assignStatus(slotProps.index))">
            {{assignStatus(slotProps.index)}}
          </span>
        </template>
        <template #content>
          <p>{{request.approvers_remark[slotProps.index] ?? 'No remarks added'}}</p>
        </template>
      </Card>
    </template>
  </Timeline>
</template>

<script>
import Timeline from 'primevue/timeline';
import Card from 'primevue/card';
import Conversion from "../../mixins/conversion";
import StatusOption from "../../mixins/statusOption";


export default {
  components: {Timeline, Card},

  mixins: [Conversion, StatusOption],

  props: {
    request: {
      type: Object,
      default: {}
    },
  },

  methods: {
    assignStatus:function (index) {
      let approvers = this.request.approver_array;
      let current_approver;

      if (this.request.current_approver) {
        current_approver = this.request.current_approver.id;
      } else {
        current_approver = 0;
      }

      if (index < approvers.indexOf(current_approver))
      {
        return 'approved';
      }

      if (index === approvers.indexOf(current_approver)) {
        return this.request.status;
      }
    }
  },
}
</script>
