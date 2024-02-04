<template>
  <template v-if="loading"/>
  <template v-else>
    <div v-if="!this.isModalView" class="w-100 d-md-flex justify-content-between align-items-center mb-2 gap-2">
      <h4 class="mb-0">Travel Expenses Details</h4>
      <span>
        <button v-if="user_id === request.requester.id && (request.status === 'pending' || request.status === 'rejected')" class="btn btn-success d-print-none me-2" @click="edit">
          <i class="bi bi-pencil-square"></i>
          Edit
        </button>
        <button class="btn btn-success d-print-none me-2" @click="printPage">
          <i class="bi bi-printer-fill"></i>
          Print
        </button>
        <ButtonLoad wrapper-class="btn btn-success d-print-none" @click="downloadAttachment">
          <i class="bi bi-cloud-arrow-down-fill"></i>
          Download Attachments
        </ButtonLoad>
      </span>
    </div>
    <hr v-if="!this.isModalView" class="mt-1">
    <h5 class="mb-2">Claim Overview</h5>
    <div class="d-lg-flex border border-rounded bg-white mb-4">
      <TEHeader class="col-lg-4" :request="request"/>
      <div class="te-card-border flex-fill p-3">
        <BarChart class="mt-2" :breakdown-data="breakdownData"/>
      </div>
    </div>
    <h5 class="mb-3">Claim Details</h5>
    <AllowanceTable :travel-id="request.id" :total="request.total_allowance" :attachment_url="attachment_url"/>
    <TransportTable :travel-id="request.id" :total="request.total_transport" :attachment_url="attachment_url"/>
    <ExpenseTable :travel-id="request.id" :total="request.total_expense" :attachment_url="attachment_url"/>
    <div class="d-flex justify-content-end me-1">
      <h4 class="bg-white border rounded-2 p-3">Total: <strong class="text-success">RM {{
          (request.total_allowance + request.total_transport + request.total_expense).toFixed(2)
        }}</strong></h4>
    </div>
    <h5 class="mb-2">Approvers</h5>
    <CommentBox :request="request"/>

  </template>

</template>

<script>
import statusOption from "../../../mixins/statusOption";
import CommentBox from "../../elements/CommentBox2.vue";
import ExpenseTable from "./tables/ExpenseTable.vue";
import AllowanceTable from "./tables/AllowanceTable.vue";
import TransportTable from "./tables/TransportTable.vue";
import TEHeader from "./card/TEHeader.vue";
import $api from "../../api";
import BarChart from "./card/BarChart.vue";
import travelOption from "../../../mixins/travelOption";
import ButtonLoad from "../../../components/elements/ButtonLoad.vue";
import {useLoadButton} from "@/stores/loadButton";

export default {
  components: {
    BarChart,
    TEHeader, TransportTable, AllowanceTable, ExpenseTable, CommentBox,
    ButtonLoad
  },
  props: {
    isModalView: {
      type: Boolean,
      default: false
    },
    form_id: {
      type: Number,
      default: null,
    },
    editable: {
      type: Boolean,
      default: false
    }
  },
  mixins: [statusOption, travelOption],
  data() {
    return {
      request: {},
      attachment_url: localStorage.getItem('currentUrl') + '/travel-claim-attachment/' + this.form_id + '/',
      user_id: parseInt(localStorage.getItem('user_id')),
      loading: true,
      breakdownData: {
        total_allowance: 0,
        total_transport: 0,
        total_expense: 0,
        allowance: [],
        expenses: [],
        transport: []
      }
    }
  },
  mounted() {
    if (this.form_id) {
      this.showTravelClaimApi(this.form_id)
    }
  },
  methods: {
    printPage() {
      window.print();
    },

    edit() {
      window.location.href = '/travel-claim/' + this.form_id;
    },

      async downloadAttachment() {
          useLoadButton().start();

          await $api.get('/api/travel-claim/' + this.form_id + '/download', {responseType: 'blob'})
              .then(response => {
                  const url = window.URL.createObjectURL(new Blob([response.data]));
                  const link = document.createElement('a');
                  link.href = url;
                  link.setAttribute('download', 'claim_attachments.zip');
                  document.body.appendChild(link);
                  setTimeout(()=>{
                      useLoadButton().finish();
                      link.click();
                  },1000)
              })
              .catch(res => {
                  useLoadButton().finish();
                  console.log(res.response.data.message)
              })
      },

    async showTravelClaimApi(id) {
      await $api.get('/api/travel-claim/' + id + '/show')
          .then(response => {
            this.request = response.data.travelClaim;
            this.breakdownData = {
              total_expense: this.request.total_expense,
              total_allowance: this.request.total_allowance,
              total_transport: this.request.total_transport,
              allowance: this.request.allowances,
              expenses: this.request.expenses,
              transport: this.request.transports
            }

            this.loading = false
          })
          .catch(res => {
            this.loading = false
            console.log(res.response.data.message)
          })
    },
  },
}
</script>
