<template>
  <div class="d-flex justify-content-between border-bottom py-2 my-2 mx-3">
    <span>
      <p class="fw-bold">{{this.user.name}}</p>
      <small :style="`color: ${this.user.color}`">{{this.user.leave_name}} {{this.user.duration}} day(s)</small>
    </span>
    <span class="d-flex flex-column align-items-end">
      <small class="text-muted">
        From:
        {{this.dMY(this.user.start_date)}}  <span class="text-capitalize">({{this.user.start_date_type}})</span>
      </small>
      <small class="text-muted">
        To:
        {{this.dMY(this.user.end_date)}}  <span class="text-capitalize">({{this.user.end_date_type}})</span>
      </small>
    </span>
  </div>
</template>

<script>
export default {
  props: {
    user: {
      type: Object,
      default: {}
    }
  },
  methods: {
    dMY: function(date) {
      if (date === null) {
        return 'No Date';
      }

      const inputDate = new Date(date);
      const day = inputDate.getDate();
      const month = inputDate.toLocaleString('default', { month: 'short' });
      const year = inputDate.getFullYear();

      return `${day} ${month} ${year}`;
    },
  }
}
</script>