<template>
  <h5 class="mt-4 mb-2">Claim Period</h5>
  <div class="form-control py-3">
    <label class="mb-2">Select claim period</label>
    <Calendar v-model="form.submission_month" view="month" dateFormat="d MM yy"
              :disabled="!!(form.submission_month)"
              placeholder="Click here to choose claim period" class="d-flex"
    />
    <small v-if="v$.form.submission_month.$error" class="text-danger">*Submission month are
      required</small>
  </div>
  <small class="text-secondary">Note: The submission month are applicable to allowance, mileage, and expenses</small>

  <h5 class="mt-4 mb-1">Assign Approvers</h5>
  <div class="d-flex align-items-center mb-3">
    <small :class="[!checked ? 'color-primary' : 'text-secondary']">Default Approvers</small>
    <InputSwitch class="mx-2" v-model="checked" @update:model-value="this.form.custom_approver = checked"/>
    <small :class="[checked ? 'color-primary' : 'text-secondary']">Custom Approvers</small>
  </div>

  <FormApprovers
      v-if="checked"
      title=""
      date-exist=""
      :supervisor="form.first_id"
      :hod="form.second_id"
      @update:supervisor="form.first_id = $event"
      @update:hod="form.second_id = $event"
      :error="v$.form.first_id.$error || v$.form.second_id.$error"
      description="Note: By selecting custom approvers, you acknowledge that you are a part of project team and require different approvers outside of your department."/>

  <PreviewFixedApprovers
      v-else
      :approvers="fixed_approvers"
      style="margin-bottom: 60px;"
  />

  <div class="floating-button">
    <ButtonLoad v-if="$parent.$props.form_id" wrapper-class="btn btn-secondary" @click="$emit('cancel')">Cancel</ButtonLoad>
    <ButtonLoad v-else :disabled="!form.submission_month" wrapper-class="btn btn-secondary" @click="$emit('reset')">Reset</ButtonLoad>
    <ButtonLoad wrapper-class="btn btn-success ms-2" @click="next">Next</ButtonLoad>
  </div>
</template>

<script>
import ButtonLoad from "../../../elements/ButtonLoad.vue";
import FormApprovers from "../../../form/FormApprovers.vue";
import PreviewFixedApprovers from "../../../elements/PreviewFixedApprovers.vue";
import Calendar from "primevue/calendar";
import InputSwitch from 'primevue/inputswitch';
import {useVuelidate} from "@vuelidate/core";
import {useModalStore} from "../../../../stores/modal";
import {useLoadButton} from "../../../../stores/loadButton";
import {required} from "@vuelidate/validators";
import $api from "../../../api";
import conversion from "../../../../mixins/conversion";
import FloatingButton from "./FloatingButton.vue";

export default {
  components: {FloatingButton, ButtonLoad, Calendar, FormApprovers, InputSwitch, PreviewFixedApprovers},
  mixins: [conversion],

  props: ['travelClaim'],
  emits: ['cancel', 'reset'],
  setup() {
    return {v$: useVuelidate()}
  },

  data() {
    return {
      checked: false,
      form: {},
      fixed_approvers: {}
    };
  },

  mounted() {
    this.getApproversApi();
    this.form = this.travelClaim;
    if (this.form.custom_approver) {
      this.checked = true;
    }
  },

  validations() {
    return {
      form: {
        submission_month: {required},
        first_id: {
          required: function () {
            if (!this.checked) {
              return true;
            }

            return this.form.first_id || this.form.second_id;
          }
        },
        second_id: {
          required: function () {
            if (!this.checked) {
              return true;
            }

            return this.form.first_id || this.form.second_id;
          }
        }
      }
    }
  },

  methods: {

    async reset() {
      useLoadButton().start();
      let params = {
          'id': this.travelClaim.id,
      }
      await $api.delete('/api/travel-claim/reset', {params})
          .then(() => {
            setTimeout(() => {
                useLoadButton().finish();
              this.$toast.add({severity: 'success', summary: 'Success', detail: 'Claim info updated', life: 3000});
            }, 1000);
          })
          .catch((res) => {
            useLoadButton().finish();
            useModalStore().show(res.response.data.message)
          })
    },

    async next() {
      const validated = await this.v$.$validate()

      if (!validated) {
        return useModalStore().show('Please fill up all fields before submitting');
      }

      this.$parent.lockDate(this.form.submission_month);
      useLoadButton().start();


      let formData = new FormData();
      formData.append('id', this.travelClaim.id);
      formData.append('submission_month', this.formDataDate(this.form.submission_month));

      if (this.checked) {
        formData.append('custom_approver', 1);
        this.form.first_id && formData.append('first_id', this.form.first_id.id);
        this.form.second_id && formData.append('second_id', this.form.second_id.id);
      } else {
        formData.append('custom_approver', 0);
      }

      await $api.post('/api/travel-claim', formData)
          .then(() => {
            setTimeout(() => {
              useLoadButton().finish();
              this.$toast.add({severity: 'success', summary: 'Success', detail: 'Claim info updated', life: 3000});
              this.$parent.$data.activeIndex += 1
            }, 1000);
          })
          .catch((res) => {
            useLoadButton().finish();
            useModalStore().show(res.response.data.message)
          })
    },

    async getApproversApi() {
      let params = {}

      params.user_id = parseInt(localStorage.getItem('user_id'));

      await $api.get('/api/administration/fixed-approvers', {params})
          .then((res) => {
            this.fixed_approvers = res.data.data;
          })
          .catch((res) => {
            console.log(res.response.data.message)
          })
    },
  },
}
</script>


