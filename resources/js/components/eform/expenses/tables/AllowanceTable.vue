<template>
  <ProgressLoad v-if="load" :value="progress"/>
  <template v-else-if="allowances.length > 0">
    <h5>Allowances</h5>
    <TableMain class="mt-2">
      <thead>
      <tr>
        <th style="min-width:10rem;">Allowance Type</th>
        <th style="min-width:15rem;">Date</th>
        <th style="min-width:12rem;">Remark & Attachment</th>
        <th class="text-center">Amount</th>
      </tr>
      </thead>
      <tbody>
      <tr class="mb-0" v-for="allowance in allowances" :key="allowance.id">
        <td class="text-secondary">
          {{ allowance.allowance_type }}  {{ allowance.allowance_name ? '(' + allowance.allowance_name + ')': '' }}
        </td>
        <td>
          <p class="text-secondary">{{ humanReadableDate(allowance.start_date) }} - {{ humanReadableDate(allowance.end_date) }}</p>
          <small class="text-secondary">{{ rawDaysBetweenDate(allowance.start_date, allowance.end_date, 'full day', 'full day') }} days
            (RM{{ allowance.allowance_rate.toFixed(2) }} per day) </small>
        </td>
        <td class="text-secondary">
          <p>{{ allowance.remark ? allowance.remark : 'No remark added' }}</p>
          <a v-if="allowance.path" class="text-capitalize" target="_blank"
             :href="attachment_url + 'allowance/' + allowance.path">
            <i class="bi bi-paperclip" style="font-size: 1rem; transform: rotate(45deg);"/>Attachment</a>
        </td>
        <td class="text-center text-secondary">
          RM {{ allowance.amount.toFixed(2) }}
        </td>
      </tr>
      </tbody>
      <tfoot>
      <tr>
        <td></td>
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
import conversion from "../../../../mixins/conversion";

export default {
  components: {ProgressLoad, Pagination, TableProfile, TableMain},
  mixins: [conversion],
  props: ['travelId', 'total', 'attachment_url'],
  mounted() {
    this.page = 1
    this.total_page = null
    if (this.travelId) {
      this.allowanceIndexApi()
    }
  },
  data() {
    return {
      load: true,
      allowances: [],
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
    async allowanceIndexApi() {
      await $api.get('/api/travel-claim/' + this.travelId + '/allowance?page=' + this.page)
          .then(response => {
            this.total_page = response.data.meta.last_page;

            response.data.data.forEach((data) => this.allowances.push(data));

            if (this.page < this.total_page) {
              this.page++;
              setTimeout(() => {
                this.allowanceIndexApi()
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


