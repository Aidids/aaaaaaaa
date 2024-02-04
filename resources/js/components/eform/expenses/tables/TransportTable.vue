<template>
  <ProgressLoad v-if="load" :value="progress"/>
  <template v-else-if="transports.length > 0">
    <h5>Transport</h5>
    <TableMain class="mt-2">
      <thead>
      <tr>
        <th style="min-width:12rem;">Description</th>
        <th style="min-width:15rem;">Remark & Attachment</th>
        <th class="text-center">Amount</th>
      </tr>
      </thead>
      <tbody>
      <tr class="mb-0" v-for="transport in transports" :key="transport.id">
        <td>
          <div v-if="transport.transport_type === 'Mileage'">
            <p class="text-secondary">{{ transport.start_name ?? transport.start_location }} - {{ transport.end_name ?? transport.end_location }} ({{ transport.total_distance }} KM)</p>
            <small class="text-secondary">{{ humanReadableDate(transport.date) }}</small>
          </div>
          <p v-else>{{ transport.transport_type }}</p>
        </td>
        <td class="text-secondary">
          <p>{{ transport.remark ? transport.remark : 'No remark added' }}</p>
          <a v-if="transport.path" class="text-capitalize" target="_blank"
             :href="attachment_url + 'transport/' + transport.path">
            <i class="bi bi-paperclip" style="font-size: 1rem; transform: rotate(45deg);"/>Attachment</a>
        </td>
        <td class="text-center text-secondary">
          RM {{ transport.amount.toFixed(2) }}
        </td>
      </tr>
      </tbody>
      <tfoot>
      <tr>
        <td></td>
        <td class="text-end" style="font-size: 0.9rem; font-weight: bold">Total Amount</td>
        <td class="text-center">RM {{ total.toFixed(2) }}</td>
      </tr>
      </tfoot>
    </TableMain>
  </template>
</template>

<script>
import TableMain from "../../../elements/TableMain.vue";
import TableProfile from "../../../elements/TableProfile.vue";
import Pagination from "../../../elements/Pagination.vue";
import $api from "../../../api";
import ProgressLoad from "../../../elements/ProgressLoad.vue";
import EmptyState from "../../../elements/EmptyState.vue";
import conversion from "../../../../mixins/conversion";

export default {
  components: {EmptyState, ProgressLoad, Pagination, TableProfile, TableMain},
  props: ['travelId', 'total', 'attachment_url'],
  mixins: [conversion],
  mounted() {
    this.page = 1
    this.total_page = null
    if (this.travelId) {
      this.transportIndexApi()
    }
  },
  data() {
    return {
      load: true,
      transports: [],
      page: 1,
      total_page: null
    }
  },
  computed: {
    progress() {
      let progress = (this.page / this.total_page * 100).toFixed(2);
      return parseInt(progress);
    },
  },
  methods: {
    async transportIndexApi() {
      await $api.get('/api/travel-claim/' + this.travelId + '/transport?page=' + this.page)
          .then(response => {
            this.total_page = response.data.meta.last_page;

            response.data.data.forEach((data) => this.transports.push(data));

            if (this.page < this.total_page) {
              this.page++;
              setTimeout(() => {
                this.transportIndexApi()
              }, 1000)
            } else {
              this.load = false;
            }
          }).catch(e => {
            this.load = false;
            console.log(e)
          });
    },
  }
}
</script>

