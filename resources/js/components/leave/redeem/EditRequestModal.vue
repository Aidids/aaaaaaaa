<template>
  <vue-final-modal :click-to-close="false" v-slot="{ params, close }" v-bind="$attrs" classes="modal-container"
                   content-class="modal-content">
    <div>
      <div class="modal-header">
        <h5 class="modal-title">Edit Replacement Leave</h5>
      </div>
      <div class="modal-body" style="overflow: auto; width:80vw">
          <br>
          <label>Working Day Information</label>
          <FormDate description="Select which working day you would like to apply for replacement leave"
                    :start-date="redeemReplacementLeave.start_date" :end-date="redeemReplacementLeave.end_date"
                    @update:start-date="(v) => redeemReplacementLeave.start_date = v"
                    @update:end-date="(v) => redeemReplacementLeave.end_date = v"/>
          <FormRemarks :approvers-exists="true" title="Edit Remark" v-model="redeemReplacementLeave.remark"/>
      </div>
      <div class="modal-footer">
        <button @click="close" type="button" class="btn btn-secondary me-2">Cancel</button>
        <button @click="$emit('confirm', this.redeemReplacementLeave.id)" type="submit"
                class="btn text-center btn-success">Save</button>
      </div>
    </div>
  </vue-final-modal>
</template>

<script>
import FormDate from "../../form/FormDate.vue";
import FormRemarks from "../../form/FormRemarks.vue"
import {mapState} from "pinia";
import {useModalStore} from "../../../stores/modal";

export default {
  components: {FormDate, FormRemarks},
  emits: ['confirm'],
  props: ['redeemReplacementLeave'],
  computed: {
    ...mapState(useModalStore, ['modal'])
  }
}

</script>
