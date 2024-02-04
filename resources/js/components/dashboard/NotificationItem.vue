<template>
  <div class="notification-item" :class="unread ? 'unread' : ''">
    <div class="w-100">
      <div class="round-icon" :class="data.color">
        <i class="bi me-1"
           :class="{'bi-calendar-plus': data.color === 'yellow',
                        'bi-calendar': data.color === 'green',
                        'bi-clipboard': data.color === 'blue',
                }"
        ></i>
      </div>
      <div class="d-flex flex-column w-100">
        <small class="text-secondary">
          <strong>{{ data.requester_name ?? 'Your' }}</strong>
          <span v-html="description"></span>
        </small>
        <div class="d-flex align-items-center justify-content-between mt-1">
          <div class="d-flex align-items-center">
            <i class="bi bi-clock text-secondary me-2"></i>
            <p style="font-size: 0.8rem;">{{ data.time }}</p>
          </div>
          <a v-if="data.status === 'pending' || data.status === 'hr_processing'"
             :href="viewLink(data.model_name, data.status)" class="view">Review</a>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    data: {
      type: Object,
      default: 'green'
    },
    icon: {
      type: String,
      default: 'green'
    },
    unread: {
      type: Boolean,
      default: true
    }

  },

  computed: {
    description() {
      switch (this.data.status) {
        case 'pending':
          return ` has requested <span class="color-primary">` + this.formatModelName(this.data.model_name) + `</span> and assigned
                            <strong class="color-primary">you</strong> as an approver.`;
        case 'hr_processing':
          return ` <span class="color-primary">` + this.formatModelName(this.data.model_name) + `</span> has been approved and requires
                            <strong class="color-primary">you</strong> for approval.`;
        case 'canceled':
          return ` <span class="color-primary">` + this.formatModelName(this.data.model_name) + `</span> has been <span class="text-danger">canceled</span>.`;
        case 'approved':
          return ` <span class="color-primary">` + this.formatModelName(this.data.model_name) + `</span> has been <span class="color-primary">approved</span>.`;
        case 'rejected':
          return ` <span class="color-primary">` + this.formatModelName(this.data.model_name) + `</span> has been <span class="text-danger">rejected</span>.`;
        case 'hr_pending':
          return ` <span class="color-primary">` + this.formatModelName(this.data.model_name) + `</span> has been sent to <span class="color-primary">HR</span> for approval.`;
        case 'hr_approved':
          return ` <span class="color-primary">` + this.formatModelName(this.data.model_name) + `</span> has been <span class="color-primary">approved</span> by HR.`;
        case 'hr rejected':
          return ` <span class="color-primary">` + this.formatModelName(this.data.model_name) + `</span> has been <span class="text-danger">rejected</span> by HR.`;
        case 'expired':
          return ` <span class="color-primary">` + this.formatModelName(this.data.model_name) + `</span> has <span class="text-danger">expired.</span>`;
        default:
          return '';
      }
    },
  },

  methods: {
    formatModelName(model_name) {
      return model_name.replace(/_/g, ' ').toLowerCase();
    },

    viewLink(model_name, status) {
      const eForm = ['travel-authorization', 'travel-claim'];
      const redeemLeave = ['redeem-replacement-leave', 'redeem-offshore-leave']
      const model = model_name.replace(/_/g, '-').toLowerCase();

      if (status === 'hr_processing') {
        if (eForm.includes(model)) {
          return 'e-form/summary/' + model;
        }

        if (redeemLeave.includes(model)) {
          return 'redeem-leave/summary/' + model.replace('redeem-', '');
        }
        return model + `/` + parseInt(localStorage.getItem('user_id')) + `/summary`;
      }

      if (eForm.includes(model)) {
        return 'e-form/approvers/' + model;
      }

      if (redeemLeave.includes(model)) {
        return 'redeem-leave/approve/' + model.replace('redeem-', '');
      }
      return model + `/` + parseInt(localStorage.getItem('user_id')) + `/approve`;
    }

  }
}
</script>
