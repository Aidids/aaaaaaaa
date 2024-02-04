<template>
  <div class="h-100 d-flex flex-column">
    <div class="d-flex align-items-center mt-2 gap-2">
      <h5>Family Information </h5>
      <button v-if="this.readMode" @click="edit" class="button">Edit</button>
    </div>
    <hr>

    <div class="h-100">
      <div v-if="! apiCall" class="h-100 d-flex align-items-center justify-content-center">
        <loader :large="false"></loader>
      </div>
      <div v-else>
        <LabelInputRow label="Marital Status*" class="mx-0">
          <template #input>
            <div class="d-flex align-items-center gap-2 mt-2">
              <RadioButton v-model="this.user.marital_status" value="single" :disabled="this.readMode"
                           :checked="this.user.marital_status === 'single'"/>
              <label for="marital_status1">Single</label>

              <RadioButton v-model="this.user.marital_status" value="married" :disabled="this.readMode"
                           :checked="this.user.marital_status === 'married'"/>
              <label for="marital_status2">Married</label>
            </div>
          </template>
          <template #validation>
            <small v-if="v$.user.marital_status.$error && !this.readMode" class="d-flex text-danger">*{{
                v$.user.marital_status.$errors[0].$message
              }}</small>
          </template>
        </LabelInputRow>

        <h5 class="my-3">Spouse Information*</h5>
        <hr>
        <div v-if="this.user.marital_status === 'married'">
          <FormSingleAttachment
              v-show="!this.readMode"
              :approvers-exists="true"
              :show-title="false"
              subtitle="Upload Marriage Certificate"
              @fileChange="this.user.file = $event"
              :attachment-path="null"
          />
          <small v-if="v$.user.marriage_cert.$error && !this.readMode" class="d-flex text-danger">*{{
              v$.user.marriage_cert.$errors[0].$message
            }}</small>
          <a v-if="user.marriage_cert"
             target="_blank"
             :href="this.attachment_url + `/personal-attachment/` + user.marriage_cert">Marriage
            Certificate</a>

          <LabelInputRow label="Spouse Working Status" class="my-3 mx-0">
            <template #input>
              <div class="d-flex align-items-center me-3 gap-2 mt-2">
                <RadioButton v-model="this.user.spouse_working" :disabled="this.readMode" value="yes"
                             :checked="this.user.spouse_working === 'yes'"/>
                <label for="marital1">Yes</label>
                <RadioButton v-model="this.user.spouse_working" :disabled="this.readMode" value="no"
                             :checked="this.user.spouse_working === 'no'"/>
                <label for="marital2">No</label>
              </div>
            </template>
            <template #validation>
              <small v-if="v$.user.spouse_working.$error && !this.readMode" class="d-flex text-danger">*{{
                  v$.user.spouse_working.$errors[0].$message
                }}</small>
            </template>
          </LabelInputRow>

          <LabelInputRow label="Spouse's Name" class="my-3 mx-0">
            <template #input>
              <input v-model="this.user.spouse_name" :disabled="this.readMode" type="text"
                     placeholder="Enter name" maxlength="150"
                     autocomplete="true" class="form-control"
                     :class="(v$.user.spouse_name.$error && !this.readMode) && `border border-danger`">
            </template>
            <template #validation>
              <small v-if="v$.user.spouse_name.$error && !this.readMode" class="d-flex text-danger">*{{
                  v$.user.spouse_name.$errors[0].$message
                }}</small>
            </template>
          </LabelInputRow>

          <LabelInputRow label="Spouse's I.C No." class="mx-0">
            <template #input>
              <InputMask v-model="this.user.spouse_ic_no" :disabled="this.readMode" type="text"
                         mask="999999-99-9999"
                         placeholder="Enter spouse IC no." autocomplete="false" class="form-control"
                         :class="(v$.user.spouse_ic_no.$error && !this.readMode) && `border border-danger`"/>
            </template>
            <template #validation>
              <small v-if="v$.user.spouse_ic_no.$error && !this.readMode" class="d-flex text-danger">*{{
                  v$.user.spouse_ic_no.$errors[0].$message
                }}</small>
            </template>
          </LabelInputRow>
        </div>

        <div v-else class="d-flex flex-column justify-content-center align-items-center">
          <i class="bi bi-people text-danger" style="font-size: 5rem;"/>
          <h6 class="text-danger">Spouse Information are Not Applicable</h6>
        </div>
        <div v-if="showChildren">
          <div class="d-flex align-items-center my-3">
            <h5>Children Information</h5>
            <button v-show="!this.readMode" class="btn btn-outline-success ms-2" @click="addChildren">Add</button>
          </div>
          <hr>
          <div v-for="(child, index) in this.user.children" class="d-xl-flex align-items-end gap-2">
            <div class="w-100 mb-3">
              <input type="hidden" v-model="child.id">
              <label class="form-label">Name</label>
              <input v-model="child.child_name" :disabled="this.readMode" type="text"
                     placeholder="Enter name" maxlength="150"
                     autocomplete="true" class="form-control"
                     :class="(hasChildNameError(index)) && `border border-danger`">
              <small
                  v-if="hasChildNameError(index)"
                  class="text-danger">
                *{{ childNameErrorMessage(index) }}
              </small>
            </div>
            <div class="w-100 mb-3">
              <label class="form-label">IC No.*</label>
              <InputMask v-model="child.child_ic" :disabled="this.readMode" type="text"
                         mask="999999-99-9999"
                         placeholder="Enter child's IC no."
                         autocomplete="true" class="form-control"
                         :class="(hasChildICError(index)) && `border border-danger`"/>
              <small
                  v-if="hasChildICError(index)"
                  class="text-danger">
                *{{ childICErrorMessage(index) }}
              </small>
            </div>
            <div v-if="!this.readMode" class="w-100 mb-3">
              <FormSingleAttachment
                  :approvers-exists="true"
                  :show-title="false"
                  subtitle="Birth Certificate"
                  @fileChange="child.file = $event"
              />
            </div>
            <div class="w-100 mb-3">
              <a v-if="user.children[index].birth_cert_path"
                 target="_blank"
                 :href="this.attachment_url + `/personal-attachment/` +
                                 user.children[index].birth_cert_path">Preview Attachment</a>
            </div>
            <div class="mb-3">
              <button v-if="!this.readMode" class="btn btn-outline-danger" @click="removeChildren(index)">
                Remove
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="!this.readMode" class="mt-3 mb-2 w-100 text-end">
      <button type="submit" class="btn btn-success" @click="save" :disabled="this.disableSubmit">Save</button>
      <button @click="cancel" class="button-outline ms-2">Cancel</button>
    </div>

    <Modal v-model="modal.show"
           :isClose="modal.isClose"
           :modal="modal"
           @complete="reload"
    />

  </div>
</template>

<script>
import $api from '../../api.js';
import LabelInputRow from "../../elements/LabelInputRow.vue";
import Modal from "../../elements/Modal.vue";
import FormSingleAttachment from "../../elements/attachments/FormSingleAttachment.vue";
import InputMask from "primevue/inputmask";
import {useVuelidate} from "@vuelidate/core";
import {required, helpers, requiredIf} from "@vuelidate/validators";
import {mapState} from "pinia";
import {usePersonalInfo} from "../../../stores/getPersonalInfo";

export default {
  setup() {
    return {v$: useVuelidate()}
  },
  components: {FormSingleAttachment, Modal, LabelInputRow, InputMask},
  props: ['user_id'],
  name: 'ProfileInfo',

  data() {
    return {
      personalLoad: true,
      readMode: true,
      disableSubmit: false,
      modal: {
        show: false,
        loader: false,
        message: "",
        isClose: false,
      },
      attachment_url: localStorage.getItem('currentUrl'),

    }
  },

  validations() {
    return {
      user: {
        marital_status: {required: helpers.withMessage("Marital status are required", required)},
        marriage_cert: {
          required: helpers.withMessage('Marriage certificate is required', function () {
            return (this.user.marital_status === 'married' && (this.user.file || this.user.marriage_cert)) ||
                this.user.marital_status !== 'married'
          })
        },
        spouse_working: {
          required: helpers.withMessage("Spouse's working status is required",
              function () {
                return (this.user.marital_status === 'married' && this.user.spouse_working) ||
                    this.user.marital_status !== 'married'
              })
        },
        spouse_name: {
          required: helpers.withMessage("Spouse's name is required",
              function () {
                return (this.user.marital_status === 'married' && this.user.spouse_name) ||
                    this.user.marital_status !== 'married'
              })
        },
        spouse_ic_no: {
          required: helpers.withMessage("Spouse's IC is required",
              function () {
                return (this.user.marital_status === 'married' && this.user.spouse_ic_no) ||
                    this.user.marital_status !== 'married'
              })
        },
        children: {
          $each: helpers.forEach(
              {
                child_name: {
                  required: helpers.withMessage("Child's name is required", required)
                },
                child_ic: {
                  required: helpers.withMessage("Child's IC No. is required", required),
                },
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

    removeChildren(index) {
      this.user.children.splice(index, 1);
    },

    addChildren() {
      if (this.user.children === undefined) {
        this.user.children = [];
      }
      this.user.children.push({
        id: null,
        child_name: '',
        child_ic: '',
        file: null,
        birth_cert_path: undefined,
        child_birth_file: undefined
      })
    },
    hasChildNameError(index) {
      return (
          this.v$.user.children.$dirty && !this.readMode &&
          this.v$.user.children.$each.$response.$errors[index].child_name[0] !== undefined
      );
    },
    childNameErrorMessage(index) {
      return this.v$.user.children.$each.$response.$errors[index].child_name[0].$message;
    },
    hasChildICError(index) {
      return (
          this.v$.user.children.$dirty && !this.readMode &&
          this.v$.user.children.$each.$response.$errors[index].child_ic[0] !== undefined
      );
    },
    childICErrorMessage(index) {
      return this.v$.user.children.$each.$response.$errors[index].child_ic[0].$message;
    },

    reload(close) {
      usePersonalInfo().getPersonalInformationApi(this.user_id);
      close();
    },

    async save() {
      const isFormCorrect = await this.v$.$validate();
      let formData = new FormData();

      if (!isFormCorrect) {
        return this.modal = {
          show: true,
          loader: false,
          message: 'Please fill in all required fields',
          isClose: true,
        }
      }

      (this.user.id) && formData.append('personal_information_id', this.user.id);

      formData.append('marital_status', this.user.marital_status === 'married' ? 1 : 0);

      (this.user.file) && formData.append('marriage_cert', this.user.file);

      if (this.user.marital_status === 'married') {
        (this.user.spouse_name) && formData.append('spouse_name', this.user.spouse_name);
        (this.user.spouse_ic_no) && formData.append('spouse_ic_no', this.user.spouse_ic_no);
        (this.user.spouse_working) && formData.append('spouse_work', this.user.spouse_working === 'yes' ? 1 : 0);

        formData.append('childrens', JSON.stringify(this.user.children));
        for (let i = 0; i < this.user.children.length; i++) {
          (this.user.children[i].file) ?
              formData.append('birth_certs[' + i + ']', this.user.children[i].file) :
              formData.append('birth_certs[' + i + ']', 0);
        }
      }

      await $api.post('/api/profile-settings/' + this.user_id + '/family-detail', formData)
          .then(response => {
            let failResponse = response.data.status === 403;
            this.readMode = ! failResponse;
            this.modal = {
              show: true,
              loader: false,
              message: response.data.message,
              isClose: failResponse
            };

            (! failResponse) && usePersonalInfo().getPersonalInformationApi(this.user_id);
          }
          ).catch(error => {
            this.modal = {
              show: true,
              loader: false,
              message: error.message,
              isClose: true,
            };
          });
    },
  },
  computed: {
    showChildren() {
      if (this.user.marital_status === 'married' && (!this.user.children || this.user.children.length === 0)) {
        return !this.readMode;
      }
      return this.user.marital_status === 'married';
    },
    ...mapState(usePersonalInfo, ['user', 'apiCall'])
  },
}
</script>

