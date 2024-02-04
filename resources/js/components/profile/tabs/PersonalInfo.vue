<template>
  <div class="h-100 d-flex flex-column">
    <div class="d-flex align-items-center mt-2 gap-2">
      <h5>Personal Information </h5>
      <button v-if="this.readMode" @click="edit" class="button">Edit</button>
    </div>
    <hr>

    <div class="h-100">
      <div v-if="! apiCall" class="h-100 d-flex align-items-center justify-content-center">
        <loader :large="false"></loader>
      </div>
      <div v-else>
        <DoubleFormRow label1="I.C No." :required1="true" label2="Passport No.">
          <template #input1>
            <InputMask v-model="this.user.ic_no" :disabled="this.readMode" type="text" mask="999999-99-9999"
                       placeholder="Enter IC No." autocomplete="false" class="form-control"
                       :class="(v$.user.ic_no.$error && !this.readMode) && 'border border-danger'"/>
          </template>
          <template #validation1>
            <small v-if="v$.user.ic_no.$error && !this.readMode" class="d-flex text-danger">*{{
                v$.user.ic_no.$errors[0].$message
              }}</small>
          </template>
          <template #input2>
            <input v-model="this.user.passport_no" :disabled="this.readMode" type="text" maxlength="100"
                   placeholder="Enter passport no" autocomplete="false" class="form-control"/>
          </template>
        </DoubleFormRow>

        <DoubleFormRow label1="Date of Birth" label2="Place of Birth" :required1="true" :required2="true">
          <template #input1>
            <input v-model="this.user.date_of_birth" :disabled="this.readMode" placeholder="Not added yet"
                   type="date" class="form-control"
                   :class="(v$.user.date_of_birth.$error && !this.readMode) && 'border border-danger'">
          </template>
          <template #validation1>
            <small v-if="v$.user.date_of_birth.$error && !this.readMode"
                   class="d-flex text-danger">*{{ v$.user.date_of_birth.$errors[0].$message }}</small>
          </template>
          <template #input2>
            <input v-model="this.user.place_of_birth" :disabled="this.readMode" type="text" maxlength="100"
                   placeholder="Enter place of birth" autocomplete="false" class="form-control"
                   :class="(v$.user.place_of_birth.$error && !this.readMode) && 'border border-danger'"/>
          </template>
          <template #validation2>
            <small v-if="v$.user.place_of_birth.$error && !this.readMode"
                   class="text-danger">*{{ v$.user.place_of_birth.$errors[0].$message }}</small>
          </template>
        </DoubleFormRow>

        <DoubleFormRow label1="Race" label2="Religion" :required1="true" :required2="true">
          <template #input1>
            <input v-model="this.user.race" :disabled="this.readMode" type="text" maxlength="100"
                   placeholder="Enter race" autocomplete="false" class="form-control"
                   :class="(v$.user.race.$error && !this.readMode) && 'border border-danger'"/>
          </template>
          <template #validation1>
            <small v-if="v$.user.race.$error && !this.readMode"
                   class="d-flex text-danger">*{{ v$.user.race.$errors[0].$message }}</small>
          </template>
          <template #input2>
            <input v-model="this.user.religion" :disabled="this.readMode" type="text" maxlength="100"
                   placeholder="Enter religion" autocomplete="false" class="form-control"
                   :class="(v$.user.religion.$error && !this.readMode) && 'border border-danger'"/>
          </template>
          <template #validation2>
            <small v-if="v$.user.religion.$error && !this.readMode" class="d-flex text-danger">*{{
                v$.user.religion.$errors[0].$message
              }}</small>
          </template>
        </DoubleFormRow>

        <DoubleFormRow label1="Nationality" label2="Contact No." :required1="true" :required2="true">
          <template #input1>
            <input v-model="this.user.nationality" :disabled="this.readMode" type="text" maxlength="100"
                   placeholder="Enter nationality" autocomplete="false" class="form-control"
                   :class="(v$.user.nationality.$error && !this.readMode) && 'border border-danger'"/>
          </template>
          <template #validation1>
            <small v-if="v$.user.nationality.$error && !this.readMode"
                   class="d-flex text-danger">*{{ v$.user.nationality.$errors[0].$message }}</small>
          </template>
          <template #input2>
            <InputMask v-model="this.user.phone_no" :disabled="this.readMode" type="text"
                       mask="999-9999999?99"
                       placeholder="Enter contact no." autocomplete="false" class="form-control"
                       :class="(v$.user.phone_no.$error && !this.readMode) && 'border border-danger'"/>
          </template>
          <template #validation2>
            <small v-if="v$.user.phone_no.$error && !this.readMode" class="d-flex text-danger">*{{
                v$.user.phone_no.$errors[0].$message
              }}</small>
          </template>
        </DoubleFormRow>

        <h5 class="my-3">Payroll Information</h5>

        <hr>

        <DoubleFormRow label1="E.P.F No." label2="Income Tax No.">
          <template #input1>
            <input v-model="this.user.epf_no" :disabled="this.readMode" placeholder="Enter E.P.F No."
                   type="text" class="form-control" maxlength="100">
          </template>
          <template #input2>
            <input v-model="this.user.income_tax_no" :disabled="this.readMode"
                   placeholder="Enter Income Tax No." maxlength="100"
                   type="text" class="form-control">
          </template>
        </DoubleFormRow>

        <DoubleFormRow label1="Bank Name" label2="Bank account type" :required1="true" :required2="true">
          <template #input1>
            <Dropdown
                v-model="this.user.bank_name"
                :class="(v$.user.bank_name.$error && !this.readMode) && 'p-invalid'"
                :disabled="this.readMode"
                :options="bank_list"
                append-to="self"
                placeholder="Search bank"
                :showClear="true"
                class="w-100"
            />
          </template>
          <template #validation1>
            <small v-if="v$.user.bank_name.$error && !this.readMode"
                   class="d-flex text-danger">*{{ v$.user.bank_name.$errors[0].$message }}</small>
          </template>
          <template #input2>
            <Dropdown
                class="w-100"
                :class="(v$.user.bank_acc_type.$error && !this.readMode) && 'p-invalid'"
                v-model="this.user.bank_acc_type"
                placeholder="Select bank account type"
                :options="acc_type"
                optionLabel="label"
                option-value="value"
                :disabled="this.readMode"
            />
          </template>
          <template #validation2>
            <small v-if="v$.user.bank_acc_type.$error && !this.readMode"
                   class="d-flex text-danger">*{{ v$.user.bank_acc_type.$errors[0].$message }}</small>
          </template>
        </DoubleFormRow>

        <DoubleFormRow label1="Bank Account No." label2="Socso No." :required1="true">
          <template #input1>
            <input v-model="this.user.bank_acc_no" :disabled="this.readMode" maxlength="100"
                   placeholder="Enter Bank Account No" type="text" class="form-control"
                   :class="(v$.user.bank_acc_no.$error && !this.readMode) && 'border border-danger'"
            >
          </template>
          <template #validation1>
            <small v-if="v$.user.bank_acc_no.$error && !this.readMode"
                   class="d-flex text-danger">*{{ v$.user.bank_acc_no.$errors[0].$message }}</small>
          </template>
          <template #input2>
            <input v-model="this.user.socso_no" :disabled="this.readMode" placeholder="Enter Socso No"
                   type="text" class="form-control" maxlength="100">
          </template>
        </DoubleFormRow>

        <div class="d-flex flex-row align-items-center my-3 gap-2">
          <h5>Education Information</h5>
          <button v-show="!this.readMode" class="btn btn-outline-success" @click="addEducation">Add</button>
        </div>
        <hr>
        <div v-if="this.readMode && (this.user.educations === undefined || this.user.educations.length === 0)"
             class="h-100 w-100 d-flex flex-column align-items-center mb-3">
          <i class="bi bi-book-half text-danger" style="font-size: 5rem"></i>
          <h6 class="text-danger">No Education Information Added Yet</h6>
        </div>


        <div v-else v-for="(education, index) in this.user.educations" class="d-xl-flex">
          <div class="container col-xl-8 mb-3 p-0">
            <div class="row align-items-center">
              <label class="col-xl-2">Qualification*</label>
              <div class="col">
                <div class="row px-2">
                  <input v-model="education.qualification" :disabled="this.readMode"
                         placeholder="Enter qualification title"
                         type="text" class="form-control" maxlength="200"
                         :class="(hasQualificationError(index) && !this.readMode) && 'border border-danger'">
                </div>
                <div class="row align-items-center">
                  <small
                      v-if="hasQualificationError(index)"
                      class="text-danger">
                    *{{ qualificationErrorMessage(index) }}
                  </small>
                </div>
              </div>
            </div>
          </div>
          <div class="container col-xl-3 mb-3 px-0 px-xl-3">
            <div class="row align-items-center">
              <label class="col-xl-5">Year Passed*</label>
              <div class="col">
                <div class="row px-2">
                  <InputMask v-model="education.year_passed" :disabled="this.readMode" mask="9999"
                             placeholder="yyyy"
                             type="text" class="form-control"
                             :class="(hasYearPassedError(index) && !this.readMode) && 'border border-danger'"/>
                </div>
                <small
                    v-if="hasYearPassedError(index)"
                    class="text-danger">
                  *{{ yearPassedErrorMessage(index) }}
                </small>
              </div>
            </div>
          </div>
          <div class="container mb-3 p-0">
            <button v-if="!this.readMode"
                    class="btn btn-outline-danger mt-2 mt-xl-0 ms-xl-2"
                    @click="removeEducation(index)">
              Remove
            </button>
          </div>
        </div>
      </div>
      <small v-if="v$.user.educations.$error && this.user.educations.length < 1 && !this.readMode"
             class="d-flex text-danger">*{{
          v$.user.educations.$errors[0].$message
        }}</small>
    </div>

    <div v-if="!this.readMode" class="mb-2 w-100 text-end">
      <button type="submit" class="btn btn-success" @click="save" :disabled="this.disableSubmit">Save</button>
      <button @click="cancel" class="button-outline ms-2">Cancel</button>
    </div>
  </div>


  <Modal v-model="modal.show" :modal="modal" @complete="reloadPage"/>
  <Modal v-model="errorModal.show" :modal="errorModal" @complete="close"/>
</template>

<script>
import $api from '../../api.js';
import LabelInputRow from "../../elements/LabelInputRow.vue";
import Modal from "../../elements/Modal.vue";
import FormSingleAttachment from "../../elements/attachments/FormSingleAttachment.vue";
import InputMask from "primevue/inputmask";
import DoubleFormRow from "../../elements/DoubleFormRow.vue";
import banks from "../../../mixins/banks";
import {useVuelidate} from "@vuelidate/core";
import {required, helpers} from '@vuelidate/validators';
import {mapState} from "pinia";
import {usePersonalInfo} from "../../../stores/getPersonalInfo";


export default {
  setup() {
    return {v$: useVuelidate()}
  },
  components: {DoubleFormRow, FormSingleAttachment, Modal, LabelInputRow, InputMask},

  props: ['user_id'],

  name: 'ProfileInfo',

  mixins: [banks],

  data() {
    return {
      readMode: true,
      disableSubmit: false,
      modal: {
        show: false,
        loader: false,
        message: "",
      },
      errorModal: {
        show: false,
        loader: false,
        message: "",
      },
      attachment_url: localStorage.getItem('currentUrl'),
    }
  },

  validations() {
    return {
      user: {
        ic_no: {required: helpers.withMessage('IC No. is required', required)},
        date_of_birth: {required: helpers.withMessage('Date of birth is required', required)},
        place_of_birth: {required: helpers.withMessage('Place of birth is required', required)},
        race: {required: helpers.withMessage('Race is required', required)},
        religion: {required: helpers.withMessage('Religion is required', required)},
        nationality: {required: helpers.withMessage('Nationality is required', required)},
        phone_no: {required: helpers.withMessage('Contact no. is required', required)},
        bank_name: {required: helpers.withMessage('Bank name is required', required)},
        bank_acc_type: {required: helpers.withMessage('Bank account type is required', required)},
        bank_acc_no: {required: helpers.withMessage('Bank account number is required', required)},
        educations: {
          required: helpers.withMessage('Education section is required', required),
          $each: helpers.forEach(
              {
                qualification: {required: helpers.withMessage('Qualification is required', required)},
                year_passed: {required: helpers.withMessage('Year passed is required', required)}
              }
          )
        }
      }
    }
  },

  methods: {
    edit() {
      this.readMode = false;
      this.v$.$reset();
    },

    cancel() {
      this.readMode = true;
    },

    removeEducation(index) {
      this.user.educations.splice(index, 1);
    },

    addEducation() {
      this.v$.$reset();
      if (this.user.educations === undefined) {
        this.user.educations = [];
      }
      this.user.educations.push({
        qualification: '',
        year_passed: undefined
      })
    },

    hasQualificationError(index) {
      return (
          this.v$.user.educations.$dirty && !this.readMode &&
          this.v$.user.educations.$each.$response.$errors[index].qualification[0] !== undefined
      );
    },
    qualificationErrorMessage(index) {
      return this.v$.user.educations.$each.$response.$errors[index].qualification[0].$message;
    },
    hasYearPassedError(index) {
      return (
          this.v$.user.educations.$dirty && !this.readMode &&
          this.v$.user.educations.$each.$response.$errors[index].year_passed[0] !== undefined
      );
    },
    yearPassedErrorMessage(index) {
      return this.v$.user.educations.$each.$response.$errors[index].year_passed[0].$message;
    },

    reloadPage(close) {
      close();
    },

    close() {
      this.errorModal.show = false;
    },

    async save() {
      const isFormCorrect = await this.v$.$validate()

      if (isFormCorrect) {
        let formData = new FormData();
        (this.user.id) && formData.append('personal_information_id', this.user.id);

        (this.user.date_of_birth) && formData.append('date_of_birth', this.user.date_of_birth);
        (this.user.place_of_birth) && formData.append('place_of_birth', this.user.place_of_birth);
        (this.user.phone_no) && formData.append('phone_no', this.user.phone_no);

        (this.user.race) && formData.append('race', this.user.race);
        (this.user.nationality) && formData.append('nationality', this.user.nationality);
        (this.user.religion) && formData.append('religion', this.user.religion);

        (this.user.ic_no) && formData.append('ic_no', this.user.ic_no);
        (this.user.passport_no) && formData.append('passport_no', this.user.passport_no);

        (this.user.epf_no) && formData.append('epf_no', this.user.epf_no);
        (this.user.income_tax_no) && formData.append('income_tax_no', this.user.income_tax_no);
        (this.user.bank_name) && formData.append('bank_name', this.user.bank_name);
        (this.user.bank_acc_no) && formData.append('bank_acc_no', this.user.bank_acc_no);
        formData.append('bank_acc_type', this.user.bank_acc_type);
        (this.user.socso_no) && formData.append('socso_no', this.user.socso_no);

        formData.append('educations', JSON.stringify(this.user.educations));

        await $api.post('/api/profile-settings/' + parseInt(this.user_id) + '/personal-information', formData)
            .then(response => {
                  this.readMode = true;
                  this.modal = {
                    show: true,
                    loader: false,
                    message: response.data.message,
                  };
                  usePersonalInfo().getPersonalInformationApi(this.user_id);
                }
            ).catch(error => {
              this.readMode = true;
              this.errorModal = {
                show: true,
                loader: false,
                message: error.message,
              };
            });
      } else {
        this.errorModal = {
          show: true,
          loader: false,
          message: 'Please fill in all required fields'
        }
      }
    },
  },

  computed: {
    ...mapState(usePersonalInfo, ['user', 'apiCall'])
  }
}
</script>

