<template>
  <div v-if="isViewMore">
    <div class="d-md-flex gap-2">
      <div class="border border-1 p-3 rounded-3 bg-white p-0 col-md-6 mb-2 mb-md-0">
        <TAHeader :request="request" :is-view-more="true" :is-approve-view="isApproveView"
                  @viewAttachment="$emit('viewAttachment', $event)"/>
      </div>
      <div class="border border-1 rounded-3 p-0 col-md-6 bg-success-subtle">
        <TAContent :overall-travel="getOverallTravelDetail"/>
        <div v-for="location in request.location"
             class="m-2 bg-white p-2 border-2 rounded border-secondary-subtle">
          <TADetails :detail="location"/>
        </div>
      </div>
    </div>
  </div>
  <div v-else class="card p-0 me-md-3 border-0 align-top" style="width: 25vw; min-width: 380px;">
    <TAHeader :request="request" :is-approve-view="isApproveView"
              @viewAttachment="$emit('viewAttachment', $event)"/>

    <div class="card-body p-0">
      <TAContent :overall-travel="getOverallTravelDetail"/>
      <Accordion>
        <AccordionTab header="View Details">
          <div v-for="detail in request.location"
               class="mb-2 bg-white p-2 border-2 rounded border-secondary-subtle">
            <TADetails :detail="detail"/>
          </div>
          <hr>
          <div class="mb-2 bg-white p-2 border-2 rounded border-secondary-subtle">
            <h6 class="text-secondary mb-0">Purpose</h6>
            <p class="text-justify">{{ request.purpose }}</p>
          </div>
        </AccordionTab>
      </Accordion>
    </div>

    <div v-if="isApproveView" class="m-2 text-end">
      <button class="btn btn-outline-secondary m-1" @click="$emit('viewMore')">Review</button>
    </div>
    <div v-else class="m-2 text-end">
      <button class="btn btn-outline-secondary m-1" @click="preview(request.id)">View</button>
      <button v-show="request.approvers.overall_status === 'pending'" class="btn btn-outline-success m-1"
              @click="edit(request.id)">Edit
      </button>
      <button v-show="request.approvers.overall_status === 'pending'" class="btn btn-outline-danger m-1"
              @click="$emit('cancel')">Cancel
      </button>
    </div>
  </div>
</template>


<script>
import Accordion from "primevue/accordion";
import AccordionTab from "primevue/accordiontab";
import TAHeader from "./TAHeader.vue";
import TADetails from "./TADetails.vue";
import calculateWorkDay from "../../../../mixins/calculateWorkDay";
import TAContent from "./TAContent.vue";

export default {
  components: {TAContent, TADetails, TAHeader, Accordion, AccordionTab},
  mixins: [calculateWorkDay],
  emits: ['viewMore', 'viewAttachment', 'cancel'],
  props: {
    request: {
      type: Object,
      default: {}
    },
    isApproveView: {
      type: Boolean,
      default: false,
    },
    isViewMore: {
      type: Boolean,
      default: false,
    }
  },
  methods: {
    preview(id) {
      window.open(
          '/travel-authorization/' + id + '/show',
          '_blank'
      );
    },
    edit(id) {
      window.location.href =
          '/travel-authorization/' + id;
    }
  },
  computed: {
    getOverallTravelDetail() {
      let overallDetail = {
        from: '',
        to: '',
        start_date: '',
        end_date: '',
        duration: 0,
      };

      if (this.request.location) {
        if (this.request.location.length === 1) {
          overallDetail = this.request.location[0];
          overallDetail.duration = this.calculateDays(overallDetail.start_date, overallDetail.end_date);
        } else if (this.request.location.length > 1) {
          overallDetail.from = this.request.location[0].from;
          overallDetail.start_date = this.request.location[0].start_date;
          overallDetail.to = this.request.location[this.request.location.length - 1].to === overallDetail.from ? this.request.location[this.request.location.length - 1].from : this.request.location[this.request.location.length - 1].to;
          overallDetail.end_date = this.request.location[this.request.location.length - 1].end_date;
          overallDetail.duration = this.calculateDays(overallDetail.start_date, overallDetail.end_date);
        }
      }
      return overallDetail
    }
  }
}
</script>


