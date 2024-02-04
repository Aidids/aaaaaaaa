<template>
  <div class="w-100 h-100 d-flex flex-column">
    <div class="d-xl-flex align-items-center justify-content-between mt-2">
      <span>
        <h5 class="d-inline-block me-2">Job Profile</h5>
        <button v-if="this.disabled && isAdmin" @click="edit" class="button">Edit</button>
      </span>
      <span v-if="this.disabled && isAdmin">
        <a class="button me-2" target="_blank"
           :href="'/api/administration/excel-user-profile/' + this.jobProfile.id">
          Download Excel
        </a>
        <button @click="print" class="button">Print</button>
      </span>
    </div>
    <hr>

    <LabelInputRow label="Full Name" class="mx-0">
      <template #input>
        <input v-model="this.jobProfile.name" disabled="disabled" type="text" placeholder="Enter first name"
               autocomplete="true" class="form-control">
      </template>
    </LabelInputRow>
    <LabelInputRow v-show="isAdmin" label="Ingress ID" class="mt-2 mx-0">
      <template #input>
        <input v-model="this.jobProfile.ingress_id" type="text" placeholder="Enter Ingress ID" :disabled="this.disabled"
               autocomplete="true" class="form-control" maxlength="100">
      </template>
    </LabelInputRow>
    <LabelInputRow v-show="isAdmin" label="Staff ID" class="mt-2 mx-0">
      <template #input>
        <input v-model="this.jobProfile.staff_id" type="text" placeholder="Enter Staff ID" :disabled="this.disabled"
               autocomplete="true" class="form-control" maxlength="100">
      </template>

    </LabelInputRow>
    <LabelInputRow label="Company Email" class="mt-2 mx-0">
      <template #input>
        <input v-model="this.jobProfile.email" disabled="disabled" type="email" placeholder="Enter email"
               autocomplete="false" class="form-control">
      </template>

    </LabelInputRow>
    <LabelInputRow label="Joining Date" class="mt-2 mx-0">
      <template #input>
        <input v-model="this.jobProfile.joining_date" :disabled="this.disabled" placeholder="Not added yet"
               type="date" class="form-control">
      </template>

    </LabelInputRow>
    <LabelInputRow label="Gender" class="my-3 mx-0">
      <template #input>
        <div class="d-flex align-items-center me-3 gap-2 mt-2 d-print-none">
          <RadioButton v-model="this.jobProfile.gender" value="m" :checked="this.jobProfile.gender === 'm'"
                       :disabled="this.disabled"/>
          <label for="gender1">Male</label>
          <RadioButton v-model="this.jobProfile.gender" value="f" :checked="this.jobProfile.gender === 'f'"
                       class="ms-3" :disabled="this.disabled"/>
          <label for="gender2">Female</label>
        </div>
        <label class="d-none d-print-block">
          {{ (this.jobProfile.gender === 'm') ? 'Male' : 'Female' }}
        </label>
      </template>
    </LabelInputRow>

    <PdfView/>

    <div v-if="!this.disabled" class="w-100 text-end ">
      <button @click="save" type="submit" class="button ms-auto">Save</button>
      <button @click="cancel" class="button-outline ms-2">Cancel</button>
    </div>
  </div>
  <Modal v-model="modal.show" :modal="modal" @complete="this.modal.show = false"/>
</template>

<script>
import $api from '../../api.js';
import LabelInputRow from "../../elements/LabelInputRow.vue";
import Modal from "../../elements/Modal.vue";
import PdfView from "../PdfView.vue";

export default {
  components: {Modal, LabelInputRow, PdfView},
  props: {
    jobProfile: Object,
  },
  name: 'ProfileInfo',
  data() {
    return {
      disabled: true,
      modal: {
        show: false,
        loader: false,
        message: "",
      },
      isAdmin: false,
    }
  },

  created() {
    (parseInt(localStorage.getItem('isAdmin')) === 1) ? this.isAdmin = true : this.isAdmin = false;
  },

  methods: {
    print() {
      window.print()
    },
    edit() {
      this.disabled = false;
    },
    cancel() {
      this.disabled = true;
    },
    async save() {
      await $api.post('/api/profile-settings/' + this.jobProfile.id, this.jobProfile)
          .then(response => {
                this.disabled = true;
                this.modal = {
                  show: true,
                  loader: false,
                  message: response.data.message,
                };
              }
          ).catch(error => {
            this.disabled = true;
            this.modal = {
              show: true,
              loader: false,
              message: error.data.message,
            };
          });
    },
  }
}
</script>
