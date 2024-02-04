<template>
  <Toast/>
  <h4 v-if="!this.isEdit" class="mb-0">Apply leave</h4>
  <small v-if="!this.isEdit" class="text-secondary">Fill in the form below to apply for leave</small>
  <div class="mt-4">
    <h5 v-if="!this.isEdit" class="mb-2">Select Leave Type</h5>
    <div class="form-control py-3">
      <label class="mb-1">Leave Type</label>
      <span v-if="this.isEdit" class="text-warning mt-1 mb-0 ms-2">
        <i class="bi bi-exclamation-diamond-fill"></i> You are not allowed to edit leave type</span>
      <input v-if="this.isEdit && form.leave_type_name"
             class="form-control"
             type="text"
             disabled
             :value="form.leave_type_name + ' | Balance: ' + form.balance + ' day(s)'"/>
      <Dropdown
          v-else
          :model-value="form.leaveType"
          :options="leaveTypes"
          optionLabel="leave_request_name"
          placeholder="Please select leave type"
          append-to="self"
          class="w-100"
          :required="true"
          :disabled="form.start_date !== undefined"
          @update:model-value="setLeaveType($event)"/>
      <FormError :error="v$.form.leaveType.$error" error-text="Leave Type is required"/>
      <FormError :error="v$.form.balance.$error" error-text="Leave Balance have to be more than 0"/>
      <div v-if="form.leave_type_id === 9">
        <label class="mt-3 mb-1">Available Replacement Leave</label>
        <input
            v-if="this.isEdit && form.redeem_replacement_selected"
            class="form-control mb-2 text-capitalize"
            type="text"
            disabled
            :value="form.redeem_replacement_selected"/>
        <Dropdown
            v-else
            :model-value="form.replacement"
            :options="replacementLeaveTypes"
            optionLabel="replacement_options"
            option-value=""
            placeholder="Please select replacement leave"
            class="w-100"
            :required="true"
            @update:modelValue="form.replacement = $event"/>
        <FormError :error="v$.form.replacement.$error" error-text="Available Replacement Leave is required"/>
      </div>
      <div v-if="form.leave_type_id === 10">
        <label class="mt-3 mb-1">Compassionate Leave Type</label>
        <input
            v-if="this.isEdit"
            class="form-control text-capitalize"
            type="text"
            disabled
            :value="form.redeem_replacement_selected"/>
        <Dropdown
            v-else
            :model-value="form.compassionate"
            :options="compassionateOption"
            optionValue="value"
            optionLabel="label"
            placeholder="Please select compassionate leave type"
            class="w-100"
            :required="true"
            @update:modelValue="form.compassionate = $event"/>
        <FormError :error="v$.form.compassionate.$error" error-text="Compassionate Leave Type is required"/>
      </div>
    </div>
    <small v-if="!this.isEdit" class="text-secondary">Click on the dropdown button above to select your leave
      type</small>
    <h5 class="mt-4 mb-2" :class="{ 'text-disabled': disableDate }">Leave Duration</h5>
    <div class="form-control py-3">
      <div class="row">
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
    <FormApprovers
        date-exist=""
        :supervisor="form.first_approver"
        :hod="form.second_approver"
        :error="v$.form.first_approver.$error || v$.form.second_approver.$error"
        @update:supervisor="form.first_approver = $event"
        @update:hod="form.second_approver = $event"/>
    <FormSingleAttachment
        :approvers-exists="form.first_approver !== undefined || form.second_approver !== undefined"
        :error="v$.form.file.$error"
        @fileChange="form.file = $event"/>

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
  <button class="btn btn-success mt-4" @click="postLeaveRequestAPI">Submit</button>
  <Modal v-model="modal.show" :modal="modal" :is-close="modal.isClose" :has-confirm="modal.hasConfirm"/>
</template>


<script>
import $api from "../api";
import Modal from "../elements/Modal.vue";
import FormRemarks from "../form/FormRemarks.vue";
import FormApprovers from "../form/FormApprovers.vue";
import FormSingleAttachment from "../elements/attachments/FormSingleAttachment.vue";
import FormError from "../elements/FormError.vue";
import Toast from "primevue/toast";
import dateType from "../../mixins/dateType";
import compassionateOption from "../../mixins/compassionateOption";
import calculateWorkDay from "../../mixins/calculateWorkDay";
import {useModalStore} from "../../stores/modal";
import {useVuelidate} from "@vuelidate/core";
import {mapState} from "pinia";

export default {
  components: {FormError, FormSingleAttachment, FormApprovers, FormRemarks, Modal, Toast},
  mixins: [dateType, calculateWorkDay, compassionateOption],
  props: {
    isEdit: {
      type: Boolean,
      default: false
    }
  },
  setup() {
    return {v$: useVuelidate()}
  },
  created() {
    useModalStore().init()
    this.getAllLeaveBalance();
  },
  data() {
    return {
      form: {
        start_date_type: 'full day',
        end_date_type: 'full day'
      },
      leaveTypes: [],
      dateFormat: "dd/MM/yyyy",
      replacementLeaveTypes: [],
    };
  },
  validations() {
    return {
      form: {
        leaveType: {
          required: function () {
            return this.form.leaveType && !this.isEdit
          }
        },
        balance: {
          required: function () {
            return (this.form.balance > 0 && !this.isEdit || [5, 10, 11, 12].includes(this.form.leave_type_id)) || !this.form.leaveType
          }
        },
        duration: {
          required: function () {
            return this.form.duration && !this.insufficientBalance(this.form.duration, this.form.leave_type_id === 9 ? this.form.replacement?.balance_qty ?? 0 : this.form.balance, this.form.leave_type_id)
          }
        },
        replacement: {
          required: function () {
            return this.form.leave_type_id !== 9 || this.form.replacement || this.isEdit
          }
        },
        compassionate: {
          required: function () {
            return this.form.leave_type_id !== 10 || this.form.compassionate || this.isEdit
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
        },
        first_approver: {
          required: function () {
            return this.form.first_approver || this.form.second_approver
          }
        },
        second_approver: {
          required: function () {
            return this.form.first_approver || this.form.second_approver
          }
        },
        file: {
          required: function() {
            return this.form.file || !([2,3,10,11, 12].includes(this.form.leave_type_id))
          }
        }
      }
    }
  },
  methods: {
    setLeaveType(leaveType) {
      this.form.leaveType = leaveType
      this.form.leave_type_id = leaveType.leave_type_id
      this.form.leave_balance_id = leaveType.id
      this.form.balance = leaveType.balance
      this.form.leave_type_name = leaveType.name
      if (this.form.leave_type_id === 9) {
        this.getReplacementBalance();
      }
    },

    setStartDate(e) {
      this.form.start_date = this.convertDate(e);
      if (this.form.leave_type_id === 10) {
        let startDate = new Date(this.form.start_date);
        let alteredDate = new Date(
            startDate.setDate(
                startDate.getDate() + this.compassionateWorkDay(startDate)
            )
        );
        this.form.end_date = this.convertDate(alteredDate);
        this.setCalendarRange(this.form.start_date, this.form.end_date, true);
      } else if (this.form.end_date) {
        this.setCalendarRange(this.form.start_date, this.form.end_date, true);
      }
    },

    setEndDate(e) {
      this.form.end_date = this.convertDate(e);
      this.setCalendarRange(this.form.start_date, this.form.end_date, true);
    },

    async getAllLeaveBalance() {
      await $api
          .get("/api/leave-request/" + parseInt(localStorage.getItem("user_id")))
          .then((response) => {
            this.leaveTypes = response.data.data;

            let unpaidLeaveType = this.leaveTypes.findIndex(
                (obj) => obj.leave_type_id === 4
            );

            let balanceExist = false;

            this.leaveTypes.forEach((obj) => {
              if ((obj.leave_type_id === 1 && obj.balance > 0)
                  || (obj.leave_type_id === 8 && obj.balance > 0)
              ) {
                balanceExist = true;
              }
            });

            balanceExist && this.leaveTypes.splice(unpaidLeaveType, 1);
          });
    },

    async getReplacementBalance() {
      await $api
          .get(
              "/api/redeem-replacement-leave/completed"
          )
          .then((response) => {
            this.replacementLeaveTypes = response.data.data;
          });
    },

    async postLeaveRequestAPI() {

      const validated = await this.v$.$validate()
      if (!validated) {
        let message = 'Please fill up all fields before submitting'
        if (this.v$.form.duration.$error) {
          message = 'Insufficient balance to apply leave'
        }
        return useModalStore().show(message)
      }

      useModalStore().load()
      let formData = new FormData();
      formData.append("leave_balance_id", this.form.leave_balance_id)
      formData.append("start_date", this.convertDate(this.form.start_date))
      formData.append("start_date_type", this.form.start_date_type)
      formData.append("end_date", this.convertDate(this.form.end_date))
      formData.append("end_date_type", this.form.end_date_type)

      formData.append("duration", this.form.duration)

      if (this.form.id) {
        formData.append("id", this.form.id)
      }

      if (this.form.compassionate) {
        formData.append("compassionate_type", this.form.compassionate)
      }

      if (this.form.replacement) {
        formData.append("replacement_leave_id", this.form.replacement.id)
      }

      if (this.form.first_approver) {
        formData.append("first_approver_id", this.form.first_approver.id)
        formData.append("first_approver_status", "pending")
      }

      if (this.form.second_approver) {
        formData.append("second_approver_id", this.form.second_approver.id)
        formData.append("second_approver_status", "pending")
      }

      if (this.form.reason) {
        formData.append("reason", this.form.reason)
      }

      if (this.form.file) {
        formData.append("file", this.form.file);
      }
      let url = this.form.leave_type_id === 10 ? 'compassionate-leave' : 'leave-request'

      await $api
          .post(
              "/api/" + url + "/" + parseInt(localStorage.getItem("user_id")),
              formData
          )
          .then((response) => {
            this.$toast.add({
              severity: 'success',
              summary: 'Success',
              detail: 'Application submitted successfully',
              life: 3000
            })
            setTimeout(() => {
              useModalStore().finishLoad()
              window.location.href = "/leave-request/" + localStorage.getItem("user_id");
            }, 1000)
          })
          .catch((error) => {
            useModalStore().show(error.response.data.message)
          })
    },
  },
  computed: {
    ...mapState(useModalStore, ['modal']),
    disableDatePeriod() {
      if (this.form.balance === undefined || this.form.balance <= 0) {
        return true;
      }

      if (this.form.leave_type_id === 9 && this.form.replacement) {
        return false
      }

      const validLeaveTypes = [1, 5, 8, 11];
      return !validLeaveTypes.includes(this.form.leave_type_id)
    },
    disableDate() {
      if (
          this.form.leave_type_id === 9 &&
          this.form.replacement === undefined
      ) {
        return true;
      }
      if (this.form.leave_type_id === 10) {
        return this.form.compassionate === undefined;

      }
      return this.form.balance === undefined ||
          this.form.balance <= 0;
    },
    setWorkDays() {
      const today = new Date();
      let backDatedDate = new Date(today);

      /*Annual Leave*/
      if (this.form.leave_type_id === 1) {
        backDatedDate.setDate(
            backDatedDate.getDate() + this.annualLeaveWorkDay(backDatedDate)
        );
        return backDatedDate;
      }

      /*Hospitalisation Leave*/
      if (this.form.leave_type_id === 3) {
        backDatedDate.setDate(backDatedDate.getDate() - 45);
        return backDatedDate;
      }

      if([2, 5, 9, 11, 12].includes(this.form.leave_type_id)) {
        backDatedDate.setDate(backDatedDate.getDate() - 31);
        return backDatedDate;
      }

      backDatedDate.setDate(backDatedDate.getDate() - 7);

      return backDatedDate;
    },
    restrictDatePeriod() {
      if (this.form.start_date === this.form.end_date) {
        return (this.form.end_date_type = this.form.start_date_type);
      }

      return this.form.end_date_type;
    },
    calculateDuration() {
      if (this.form.leave_type_id === 10) {
        this.form.duration = 3;
      }
      if ([7,11, 12].includes(this.form.leave_type_id)) {
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
  },
};
</script>


