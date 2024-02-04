<template>
  <h5 class="text-secondary mb-2">{{ title }}</h5>
  <div :class="wrapperClass">
    <div class="form-group me-md-1 w-100">
      <label class="mb-2">Start date</label>
      <Datepicker
          :model-value="startDate"
          :enable-time-picker="false"
          placeholder="Please select start date"
          auto-apply :state="null"
          :max-date="endDate"
          :format="dateFormat"
          :clearable="false"
          @update:model-value="$emit('update:start-date', startDate = convertDate($event))"/>
      <FormError :error="errorStartDate" error-text="Start Date is required"/>
    </div>
    <div class="form-group ms-md-1 w-100">
      <label class="mb-2">End date</label>
      <Datepicker
          :model-value="endDate"
          :enable-time-picker="false" ref="startDate"
          placeholder="Please select end date"
          auto-apply :state="null"
          :clearable="false"
          :min-date="startDate"
          :format="dateFormat"
          @update:model-value="$emit('update:end-date', endDate = convertDate($event))"/>
      <FormError :error="this.errorEndDate" error-text="End Date is required"/>
    </div>
  </div>
  <small class="text-secondary">{{ description }}</small>
</template>

<script>
import FormError from "../elements/FormError.vue";

export default {
  components: {FormError},
  emits: ['update:start-date', 'update:end-date'],

  data() {
    return {
      dateFormat: "dd/MM/yyyy",
    }
  },

  props: {
    startDate: {
      type: String,
      default: null,
    },
    endDate: {
      type: String,
      default: () => {
      },
    },
    errorStartDate: {
      type: Boolean,
      default: false,
    },
    errorEndDate: {
      type: Boolean,
      default: false
    },
    wrapperClass: {
      type: String,
      default: 'col-md-6 d-md-flex flex-row form-control py-3'
    },
    title: {
      type: String,
      default: ''
    },
    description: {
      type: String,
      default: ''
    }
  },

  methods: {
    convertDate: function (date) {
      let dateTime = new Date(date);
      let year = dateTime.getFullYear();
      let month = (dateTime.getMonth() + 1).toString().padStart(2, '0');
      let day = dateTime.getDate().toString().padStart(2, '0');

      return `${year}-${month}-${day}`;
    },
  }
};
</script>
