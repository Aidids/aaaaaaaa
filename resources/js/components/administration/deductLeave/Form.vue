<template>
  <div class="col-md-6 px-0">
    <h4 class="mb-3">Deduct Leave Balance</h4>
    <div class="bg-white shadow p-3 mb-4">
      <div class="d-flex flex-column" style="width: 10rem;">
        <label class="form-label">Select All Staff</label>
                <div class="d-flex align-items-center">
                  <p :class="[form.deduct_all ? 'text-disabled' : 'color-primary fw-bold']">No</p>
                  <InputSwitch v-model="form.deduct_all" class="mx-2"/>
                  <p :class="[form.deduct_all ? 'color-primary fw-bold' : 'text-disabled']">Yes</p>
                </div>
      </div>
      <Users wrapper-class="mt-3" v-model="form.user" :is-disabled="form.deduct_all"/>
      <FormError :error="v$.form.user.$error" error-text="Staff are required"/>
      <FormDate
          wrapper-class="d-md-flex flex-row my-3"
          :start-date="form.start_date"
          :end-date="form.end_date"
          :error-start-date="v$.form.start_date.$error"
          :error-end-date="v$.form.end_date.$error"
          @update:start-date="(v) => form.start_date = v"
          @update:end-date="(v) => form.end_date = v"
      />
      <label class="form-label">Remark</label>
      <textarea v-model="form.remark" class="form-control"></textarea>
      <div class="d-md-flex justify-content-between align-items-end">
        <div class="d-flex flex-column">
          <label class="form-label mt-3">Deduct Balance</label>
          <Dropdown
              class="w-full"
              v-model="form.duration"
              :options="balanceOption"
              optionLabel="value"
              placeholder="Select Value"/>
          <FormError :error="v$.form.duration.$error" error-text="Duration is required"/>
        </div>
        <button class="btn btn-success h-25" @click="openModal">Update</button>
      </div>
    </div>
  </div>
  <Modal v-model="modal.show" :modal="modal" :is-close="modal.isClose" :has-confirm="modal.hasConfirm"/>
  <ConfirmationModal v-model="showConfirmationModal" :form="this.form"/>
</template>

<script>
import Users from "../../dropdown/Users.vue";
import InputSwitch from "primevue/inputswitch";
import ConfirmationModal from "./ConfirmationModal.vue";
import balanceOption from "../../../mixins/balanceOption";
import FormDate from "../../form/FormDate.vue";
import {useVuelidate} from "@vuelidate/core";
import FormError from "../../elements/FormError.vue";
import {useModalStore} from "../../../stores/modal";
import {mapState} from "pinia";
import Modal from "../../elements/Modal.vue";

export default {
  components: {Modal, FormError, Users, ConfirmationModal, FormDate, InputSwitch},

  mixins: [balanceOption],
  setup() {
    return {v$: useVuelidate()}
  },
  data() {
    return {
      form: {
        deduct_all: false,
      },
      showConfirmationModal: false,
    }
  },

  validations() {
    return {
      form: {
        user: {
          required: function () {
            return this.form.user || this.form.deduct_all
          }
        },
        start_date: {
          required: function () {
            return this.form.start_date
          }
        },
        end_date: {
          required: function () {
            return this.form.end_date
          }
        },
        duration: {
          required: function () {
            return this.form.duration
          }
        }
      }
    }
  },

  methods: {
    async openModal() {
      const validated = await this.v$.$validate()

      if (!validated) {
        return useModalStore().show('Please fill up all fields before submitting');
      }
      this.showConfirmationModal = true;
    },

    // to be used inside confirmation modal child component
    // DO NOT REMOVE
    resetForm() {
      this.v$.$reset()
      this.form = {
        deduct_all: false,
      };
    },

    callApi() {
      this.resetForm();
      this.$parent.getDeductLeaveHistory();
    }
  },

  computed: {
    ...mapState(useModalStore, ['modal']),
  }
}
</script>
