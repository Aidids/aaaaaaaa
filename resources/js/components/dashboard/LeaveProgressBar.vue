<template>
    <span v-if="! excluded">
        <div class="d-flex justify-content-between mb-1">
            <p>{{title}}</p>
            <p>
                <small class="fw-light">Total: </small>
                <small class="color-primary fw-bold">{{total}} days</small>
            </p>
        </div>
        <div v-if="this.value > 0.00" class="progress mb-2">
            <div class="progress-bar bg-success" role="progressbar" :style="{width: progress}">{{value}} {{dayUnit}} remaining</div>
        </div>
        <div v-else class="progress mb-2">
            <div class="progress-bar bg-secondary w-100" role="progressbar">No leaves remaining</div>
        </div>
    </span>
</template>
<script>
export default {
    props: {
        excluded: {
            type: Boolean,
            default: false,
        },
        title: {
            type: String,
            default: 'Title'
        },
        total: {
            type: Number,
            default: 0
        },
        value: {
            type: Number,
            default: 0
        },
    },
    computed: {
      progress() {
          const percentage = (this.value / this.total * 100).toFixed(2);
          if (percentage <= 20.00) {
              return `35%`;
          }
          return `${percentage}%`;
      },

      dayUnit() {
          if (this.value <= 1) {
              return 'day';
          }
          return 'days';
      }
    }

}
</script>
