<template>
  <vue-final-modal
      :click-to-close="false"
      v-slot="{ params, close }"
      v-bind="$attrs"
      classes="modal-container"
      content-class="modal-content">
    <div>
      <div class="modal-header">
        <h5 class="modal-title">Edit Leave Request</h5>
      </div>
      <div class="modal-body pe-3 pt-2" style="overflow: auto; height: 70vh; width: 90vw">
        <div class="form-control py-3">
          <label class="mb-2">Leave Type</label><span class="text-warning ms-2">
          <i class="bi bi-exclamation-diamond-fill"></i> You are not allowed to edit leave type</span>
          <input class="form-control mb-2"
                 type="text"
                 disabled
                 :value="form.leave_type_name + ([5, 10, 11, 12].includes(form.leave_type_id) ?  '' : ' | Balance: ' + form.balance + ' day(s)')"/>
          <div v-if="form.leave_type_id === 9">
            <label class="mb-2">Available Replacement Leave</label>
            <input
                class="form-control mb-2 text-capitalize"
                type="text"
                disabled
                :value="form.redeem_replacement_selected"/>
          </div>
          <div v-if="form.leave_type_id === 10">
            <label class="mb-2">Compassionate Leave Type</label>
            <input
                class="form-control mb-2 text-capitalize"
                type="text"
                disabled
                :value="form.compassionate_type"/>
          </div>
        </div>
        <h5 class="mt-4 mb-2" :class="{ 'text-disabled': disableDate }">
          Leave Duration
        </h5>
        <div class="form-control py-3">
          <div class="row mb-1">
            <div class="col-md-6">
              <label class="mb-1" :class="{ 'text-disabled': disableDate }">Start date</label>
              <Datepicker
                  :disabled="disableDate"
                  :model-value="form.start_date"
                  :enable-time-picker="false"
                  placeholder="Please select start date"
                  auto-apply
                  :state="null"
                  :format="dateFormat"
                  :clearable="false"
                  :min-date="setWorkDays"
                  :max-date="form.end_date"
                  @update:model-value="setStartDate($event)">
              </Datepicker>
              <FormError :error="v$.form.start_date.$error" error-text="Start date is required"/>
            </div>
            <div class="col-md-6 mb-3">
              <label class="mb-1">Start Date Period</label>
              <Dropdown
                  :disabled="disableDatePeriod"
                  :model-value="form.start_date_type"
                  class="w-100"
                  placeholder="Please select start date period"
                  :options="dateType"
                  option-value="value"
                  optionLabel="label"
                  @update:modelValue="form.start_date_type = $event"/>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <label class="mb-1" :class="{ 'text-disabled': !form.start_date }">End date</label>
              <Datepicker
                  :disabled="disableDate || form.leave_type_id === 10"
                  :model-value="form.end_date"
                  :enable-time-picker="false"
                  placeholder="Please select start date"
                  auto-apply
                  :state="null"
                  :clearable="false"
                  :format="dateFormat"
                  :min-date="form.start_date"
                  @update:model-value="setEndDate($event)">
              </Datepicker>
              <FormError :error="v$.form.end_date.$error" error-text="End date is required"/>
            </div>
            <div class="col-md-6">
              <label class="mb-1">End Date Period</label>
              <Dropdown
                  :disabled="disableDatePeriod || form.start_date === form.end_date"
                  :model-value="restrictDatePeriod"
                  class="w-100"
                  :options="dateType"
                  placeholder="Please select end date period"
                  option-value="value"
                  optionLabel="label"
                  @update:modelValue="form.end_date_type = $event"/>
            </div>
          </div>
        </div>
        <small>Date Period is only applicable for Annual & Emergency Leave</small>
        <FormSingleAttachment
            :approvers-exists="form.first_approver !== undefined || form.second_approver !== undefined"
            @fileChange="form.file = $event" :attachment="form.attachment[0]"/>
        <FormRemarks
            :approvers-exists="form.first_approver !== undefined || form.second_approver !== undefined"
            v-model="form.reason"/>
        <h5 class="mt-4 mb-2">Leave Preview</h5>
        <div class="form-control d-md-flex flex-row">
          <div class="col-md-7 pe-md-1 border-end">
            <VCalendar :attributes="attributes" class="w-100"/>
          </div>
          <div class="col-md-5 p-4">
            <h5>Leave Type</h5>
            <h3>
              {{ this.form.leave_type_name ?? "No leave are selected" }}
            </h3>
            <h5>Start Date</h5>
            <h3 class="text-capitalize">
              {{ displayDate(this.form.start_date, this.form.start_date_type) }}
            </h3>
            <h5>End Date</h5>
            <h3 class="text-capitalize">
              {{ displayDate(this.form.end_date, this.form.end_date_type) }}
            </h3>
            <h5>Duration</h5>
            <h3>{{ calculateDuration }} day(s)</h3>
            <FormError :error="v$.form.duration.$error" error-text="Insufficient balance to apply leave"/>
          </div>
        </div>
      </div>
      <div class="modal-footer mt-3">
        <button
            @click="cancel"
            type="button"
            class="btn btn-secondary me-2">
          Cancel
        </button>
        <button
            @click="confirm(this.form.id)"
            type="submit"
            class="btn btn-success">Save
        </button>
      </div>
    </div>
  </vue-final-modal>
</template>

<script>
import dateType from "../../mixins/dateType";
import calculateWorkDay from "../../mixins/calculateWorkDay";
import FormRemarks from "../form/FormRemarks.vue";
import FormApprovers from "../form/FormApprovers.vue";
import FormSingleAttachment from "../elements/attachments/FormSingleAttachment.vue";
import FormError from "../elements/FormError.vue";
import Datepicker from "@vuepic/vue-datepicker";
import {useVuelidate} from "@vuelidate/core";
import {useModalStore} from "../../stores/modal";
import {mapState} from "pinia";

export default {
  components: {Datepicker, FormError, FormSingleAttachment, FormApprovers, FormRemarks},
  emits: ["confirm", "cancel"],
  props: ["form"],
  mixins: [calculateWorkDay, dateType],
  setup() {
    return {v$: useVuelidate()}
  },
  data() {
    return {
      dateFormat: "dd/MM/yyyy",
    }
  },
  validations() {
    return {
      form: {
        duration: {
          required: function () {
            return this.form.duration && !this.insufficientBalance(this.form.duration, this.form.leave_type_id === 9 ? this.form.replacement?.balance_qty ?? 0 : this.form.balance, this.form.leave_type_id)
          }
        },
        start_date: {
          required: function () {
            return this.form.start_date || this.disableDate
          }
        },
        end_date: {
          required: function () {
            return this.form.end_date || this.disableDate
          }
        }
      }
    }
  },
  methods: {
    setStartDate(e) {
      this.form.start_date = this.convertDate(e)
      if (this.form.leave_type_id === 10) {
        let startDate = new Date(this.form.start_date)
        let alteredDate = new Date(
            startDate.setDate(
                startDate.getDate() + this.compassionateWorkDay(startDate)
            )
        );
        this.form.end_date = this.convertDate(alteredDate)
        this.setCalendarRange(this.form.start_date, this.form.end_date)
      } else if (this.form.end_date) {
        this.setCalendarRange(this.form.start_date, this.form.end_date)
      }

    },

    setEndDate(e) {
      this.form.end_date = this.convertDate(e)
      this.setCalendarRange(this.form.start_date, this.form.end_date)
    },

    cancel(close) {
      close
      location.reload()
    },

    async confirm(id) {
      const validated = await this.v$.$validate()
      if (!validated) {
        let message = 'Please fill up all fields before submitting'
        if (this.v$.form.duration.$error) {
          message = 'Insufficient balance to apply leave'
        }
        return useModalStore().show(message)
      }

      this.$emit('confirm', id)
    }
  },

  watch: {
    leaveRequestModal: {
      deep: true,
      handler(n, o) {
        this.setCalendarRange(this.leaveRequestModal.start_date, this.leaveRequestModal.end_date);
      },
    },
  },

  computed: {
    ...mapState(useModalStore, ['modal']),
    restrictDatePeriod() {
      if (this.form.start_date === this.form.end_date) {
        return (this.form.end_date_type = this.form.start_date_type);
      }

      return this.form.end_date_type;
    },
    setWorkDays() {
      const today = new Date()
      let backDatedDate = new Date(today)

      /*Annual Leave*/
      if (this.form.leave_type_id === 1) {
        backDatedDate.setDate(backDatedDate.getDate() + this.annualLeaveWorkDay(backDatedDate))
        return backDatedDate
      }
      /*Hospitalisation Leave*/
      if (this.form.leave_type_id === 3) {
        backDatedDate.setDate(backDatedDate.getDate() - 45)
        return backDatedDate
      }

      if([2, 5, 9, 11, 12].includes(this.form.leave_type_id)) {
        backDatedDate.setDate(backDatedDate.getDate() - 31);
        return backDatedDate;
      }

      backDatedDate.setDate(backDatedDate.getDate() - 7)

      return backDatedDate
    },
    disableDatePeriod() {
      if ([1, 5, 8, 9, 11].includes(this.form.leave_type_id)) {
        return false;
      }

      return [2, 3, 4, 7, 10, 12].includes(this.form.leave_type_id);
    },
    disableDate() {
      if (
          this.form.leave_type_id === 9 &&
          this.form.replacement === undefined
      ) {
        return true
      }
      if (this.form.leave_type_id === 10) {
        return this.form.compassionate_type === undefined

      }
      return this.form.balance === undefined ||
          this.form.balance <= 0
    },
    calculateDuration() {
      if (this.form.leave_type_id === 10) {
        this.form.duration = 3;
      }

      if ([7, 11, 12].includes(this.form.leave_type_id)) {
        this.form.duration = this.calculateEveryDay(
            this.form.start_date,
            this.form.end_date,
            this.form.start_date_type,
            this.form.end_date_type
        );
      }
      else {
        this.form.duration = this.calculateWorkDays(
            this.form.start_date,
            this.form.end_date,
            this.form.start_date_type,
            this.form.end_date_type
        );
      }

      return this.form.duration;
    },
  }
};
</script>
