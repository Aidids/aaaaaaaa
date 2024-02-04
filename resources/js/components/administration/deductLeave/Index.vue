<template>
  <Form/>
  <h4>History</h4>
  <div class="w-100">
    <TableMain>
      <thead>
      <tr>
        <th style="min-width: 8rem;">Staff</th>
        <th class="text-center">Quantity</th>
        <th style="min-width: 12rem;">HR PIC</th>
        <th style="min-width: 12rem;">Remarks</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="data in deductionData" :key="data.id">
        <td>
          <TableProfile :user="data.user" :navigate="true"/>
        </td>
        <td class="text-center">{{ data.duration }} day(s)</td>
        <td>
          <div class="d-flex flex-column align-items-start">
            <p class="mb-0">PIC: <span class="fw-bold">{{ data.hrIncharge.name }}</span></p>
            <p class="mb-0">Created at: <span class="fw-bold">{{ this.displayDate(data.created_at) }}</span></p>
          </div>
        </td>
        <td class="text-start">
          {{ data.remark }}
        </td>
      </tr>
      </tbody>
    </TableMain>
  </div>
  <Pagination
      :perPage="10"
      :totalPages="totalPages"
      :currentPage="currentPage"
      @pagechanged="onPageChange"
  />
</template>
<script>
import Form from "./Form.vue";
import TableMain from "../../elements/TableMain.vue";
import TableProfile from "../../elements/TableProfile.vue";
import Pagination from "../../elements/Pagination.vue"
import $api from "../../api";
import calculateWorkDay from "../../../mixins/calculateWorkDay";

export default {
  components: {Form, Pagination, TableMain, TableProfile},

  mixins: [calculateWorkDay],

  data() {
    return {
      deductionData: [],
      user_id: parseInt(localStorage.getItem('user_id')),
      currentPage: 1,
      totalPages: 1,
    }
  },

  created() {
    this.getDeductLeaveHistory();
  },

  methods: {
    onPageChange(page) {
      this.currentPage = page;
      this.deductLeavePagination();
    },

    async getDeductLeaveHistory() {
      await $api.get('/api/administration/deduct-leave/')
          .then(response => {
            this.deductionData = response.data.data;
            this.totalPages = response.data.meta.last_page;
          });
    },

    async deductLeavePagination() {
      await $api.get('/api/administration/deduct-leave?page=' + this.currentPage)
          .then(response => {
            this.deductionData = response.data.data;
            this.totalPages = response.data.meta.last_page;
          });
    },
  }
}
</script>
