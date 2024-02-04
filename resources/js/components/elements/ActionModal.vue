<template>
  <vue-final-modal
      :click-to-close="true"
      v-slot="{ params, close }"
      v-bind="$attrs"
      classes="modal-container"
      content-class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title">{{ action === 'approved' ? 'Approving...' : 'Rejecting...' }}</h5>
      <i class="bi bi-x-circle close-icon" @click="close"/>
    </div>
    <hr class="mb-0">
    <div class="modal-body mt-3" style="overflow: auto; min-width: 40vw">
      <slot></slot>
      <div class="form-group">
        <label class="form-label">Remark</label>
        <textarea v-model="remark" cols="30" rows="3" class="form-control" style="resize: none;" :maxlength="150"
                  placeholder="You can leave additional remark regarding your status choices (optional)">
                        </textarea>
      </div>
      <div class="my-3 text-end">
        <button class="btn btn-outline-success m-1" @click="onConfirm">Confirm</button>
        <button class="btn btn-outline-secondary m-1" @click="onCancel(close)">Cancel</button>
      </div>
    </div>
  </vue-final-modal>
</template>

<script>

export default {
  props: {
    action: {
      type: String,
      default: 'approved'
    }
  },
  data() {
    return {
      remark: ''
    }
  },
  emits: ['confirm'],
  methods: {
    onConfirm() {
      this.$emit('confirm', this.remark);
      this.remark = '';
    },
    onCancel(close) {
      this.remark = '';
      close();
    }
  }
}
</script>
