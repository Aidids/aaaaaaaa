<template>
  <ProgressLoad v-if="load" :value="progress"/>
  <template v-else-if="expenses.length > 0">
    <h5>Expenses</h5>
    <TableMain class="mt-2">
      <thead>
      <tr>
        <th class="text-center" style="min-width:8rem;">Account Code</th>
        <th style="min-width:12rem;">Description</th>
        <th style="min-width:12rem;">Remark & Attachment</th>
        <th class="text-center">Amount</th>
      </tr>
      </thead>
      <tbody>
      <tr class="mb-0" v-for="(expense, index) in expenses" :key="expense.id">
        <td class="text-center text-secondary">
          {{ expense.account_code ?? 'No account code' }}
        </td>
        <td class="text-secondary">
          <p>{{ expense.description }} {{ expense.description_name ? '(' + expense.description_name + ')' : ''}}</p>
        </td>
        <td class="text-secondary">
          <p>{{ expense.remark ? expense.remark : 'No remark added' }}</p>
          <a v-if="expense.path" class="text-capitalize" target="_blank"
             :href="attachment_url + 'expense/' + expense.path">
            <i class="bi bi-paperclip" style="font-size: 1rem; transform: rotate(45deg);"/>Attachment</a>
        </td>
        <td class="text-center text-secondary">
          RM {{ expense.amount.toFixed(2) }}
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
import EmptyState from "../../../elements/EmptyState.vue";

export default {
  components: {EmptyState, ProgressLoad, Pagination, TableProfile, TableMain},
  props: ['travelId', 'total', 'attachment_url'],
  data() {
    return {
      load: true,
      expenses: [],
      page: 1,
      total_page: null,
    }
  },
  created() {
    this.page = 1;
    this.travelId && this.expensesIndexApi();
  },

  computed: {
    progress() {
      let progress = (this.page / this.total_page * 100).toFixed(2);
      return parseInt(progress);
    },
  },

  methods: {
    async expensesIndexApi() {
      await $api.get('/api/travel-claim/' + this.travelId + '/expense?page=' + this.page)
          .then(response => {
            this.total_page = response.data.meta.last_page;

            response.data.data.forEach((data) => this.expenses.push(data))

            if (this.page < this.total_page) {
              this.page++;
              setTimeout(() => {
                this.expensesIndexApi()
              }, 3000)
            } else {
              this.load = false
            }
          }).catch(e => {
            this.load = false;
            console.log(e)
          });
    },
  }
}
</script>



