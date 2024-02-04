<template>
  <vue-final-modal v-slot="{ params, close }" v-bind="$attrs" classes="modal-container" content-class="modal-content">
    <div style="min-width: 28rem;">
      <div class="modal-header">
        <h5 class="modal-title">Add Approvers</h5>
        <button @click="close" type="button" class="btn-close"></button>
      </div>
      <div class="modal-body">
        <div class="mt-4 form-inline">
          <label class="">Employee List</label>
          <Dropdown v-model="approvers.selectedUser" :options="users" optionLabel="name" :filter="true"
                    placeholder="Search Employee" :showClear="true" class="w-100 mt-2">
            <template #value="slotProps">
              <p v-if="slotProps.value">{{ slotProps.value.name }}</p>
              <span v-else>{{ slotProps.placeholder }}</span>
            </template>
            <template #option="slotProps">
              {{ slotProps.option.name }}
            </template>
          </Dropdown>
        </div>
        <div class="form-group mt-4">
          <label class="">Approvers Level</label>
          <Dropdown v-model="approvers.selectedApproverLevel" :options="options" optionLabel="text" class="w-100 mt-2"
                    placeholder="Select Approver Level" :showClear="true" required>
          </Dropdown>
        </div>
        <p class="mt-4" style="color: red">*Required to fill both field</p>
      </div>
      <div class="modal-footer">
        <button @click="$emit('cancel', close)" type="button" class="btn btn-secondary me-2">Cancel</button>
        <button v-if="disabled" type="submit" class="btn text-center btn-secondary">Save</button>
        <button v-else @click="$emit('confirm', close)" type="submit" class="btn text-center btn-success">Save</button>
      </div>
    </div>
  </vue-final-modal>
</template>

<script>

import CustomInput from '../elements/CustomInput.vue';
import $api from "../api.js";


export default {
  props: ['approvers', 'user_id'],
  data() {
    return {
      users: [],
      query: '',
      isActive: false,
      disabled: true,
      options: [
        {text: 'First Level', value: '1'},
        {text: 'Second Level', value: '2'},
      ]
    }
  },
  methods: {
    async getAllApproverApi() {
      await $api.get('/api/approver/all').then(response => {
        this.users = response.data.data;
      });
    },

    toggle: function () {
      this.isActive = !this.isActive;
    },
  },
  created() {
    this.getAllApproverApi();
  },
  components: {
    CustomInput,
  },
  name: 'ApproversModal',
  inheritAttrs: false,
  watch: {
    approvers: {
      deep: true,
      handler(n, o) {
        this.disabled =
            n.selectedUser == null ||
            n.selectedApproverLevel == null;

      }
    }
  }

}

</script>
