<template>
  <h5 class="mt-4 mb-2">{{ form.travel_purpose ? 'Corporate' : 'Project' }} Details</h5>

  <!--E-Form Type Selection-->
  <div class="form-control py-3">
    <div class="d-flex flex-column">
      <label class="w-100 mb-2">Travel Purpose</label>
      <div class="d-flex align-items-center">
        <small :class="form.travel_purpose ? 'text-secondary' : 'text-success fw-bold'">Project</small>
        <InputSwitch class="mx-2" v-model="form.travel_purpose"/>
        <small :class="form.travel_purpose ? 'text-success fw-bold' : 'text-secondary'">Corporate</small>
      </div>
    </div>

    <!--Project Information-->
    <div v-if="form.travel_purpose" class="col-md-6 form-group mt-2">
      <label class="my-2">Department</label>
      <InputText type="text" class="form-control mb-3" v-model="profile.department"
                 :disabled="true" placeholder="Enter project name"/>
    </div>

    <div v-else class="d-md-flex gap-2 mt-2 me-2">
      <div class="col-md-6 form-group mb-2">
        <label class="my-2">Project Name</label>
        <InputText type="text" class="form-control" v-model="form.project_name"
                   :class="v$.form.project_name.$error && 'p-invalid'"
                   placeholder="Enter project name"/>
        <small v-if="v$.form.project_name.$error" class="text-danger">
          Please select project name
        </small>
      </div>
      <div class="col-md-6 form-group mb-2">
        <label class="my-2">Project Location</label>
        <InputText type="text" class="form-control" v-model="form.project_location"
                   :class="v$.form.project_location.$error && 'p-invalid'"
                   placeholder="Enter project location"/>
        <small v-if="v$.form.project_location.$error" class="text-danger">
          Please enter project location
        </small>
      </div>
    </div>

    <div class="d-flex gap-1 my-2">
      <div class="col-md-6">
        <label class="mb-2 d-block">Main Office</label>
        <div class="d-inline-block" v-for="(location, index) in mainOfficeList">
          <RadioButton v-model="form.main_office" :inputId="'office' + index" name="office"
                       :value="location.value"/>
          <label :for="'office' + index" class="ms-1 me-3">{{ location.label }}</label>
        </div>
        <small v-if="v$.form.main_office.$error" class="text-danger d-block">
          Please select office location
        </small>
      </div>
      <div class="col-md-6">
        <label class="mb-2 d-block">Reimbursable</label>
        <div class="d-inline-block" v-for="(reimbursement, index) in ['No', 'Yes']">
          <RadioButton v-model="form.reimbursement" :inputId="'reimbursement' + index" name="reimbursement"
                       :value="index"/>
          <label :for="'reimbursement' + index" class="ms-1 me-3">{{ reimbursement }}</label>
        </div>
        <small v-if="v$.form.reimbursement.$error" class="text-danger d-block">
          Select reimbursement
        </small>
      </div>
    </div>
  </div>

  <h5 class="mt-4 mb-2">Travel Details</h5>
  <div v-if="form.location.length === 0" class="form-control p-3">
    <p class="py-2" :class="v$.form.location.$error && 'text-danger fw-bold'"
    >Click on <strong>Add Travel Details</strong> button below to add your travel details</p>
  </div>
  <div v-else v-for="(location, index) in form.location" :key="location.id">
    <FormList :index="index" @remove="removeLocation($event)">
      <div class="d-xl-flex">
        <div class="ms-1 mb-2 me-md-2 w-100">
          <label class="mb-2 d-block w-100">Start Date</label>
          <Datepicker
              :class="(v$.form.location.$dirty &&
            Object.keys(v$.form.location.$model[index].start_date).length === 0) &&
            'border border-danger'"
              :model-value="location.start_date"
              :enable-time-picker="false"
              placeholder="Select start date"
              auto-apply
              :state="null"
              :format="dateFormat"
              :clearable="false"
              @update:model-value="location.start_date = this.convertDate($event)"/>
          <small v-if="v$.form.location.$dirty &&
          Object.keys(v$.form.location.$model[index].start_date).length === 0"
                 class="text-danger d-block">
            Select start date
          </small>
        </div>
        <div class="ms-1 mb-2 me-md-2 w-100">
          <label class="mb-2 d-block w-100">End Date</label>
          <Datepicker
              :class="(v$.form.location.$dirty &&
            Object.keys(v$.form.location.$model[index].end_date).length === 0) &&
            'border border-danger'"
              :model-value="location.end_date"
              :enable-time-picker="false"
              placeholder="Select end date"
              auto-apply
              :state="null"
              :format="dateFormat"
              :min-date="(location.start_date).toString()"
              :clearable="false"
              @update:model-value="location.end_date = this.convertDate($event)"/>
          <small v-if="v$.form.location.$dirty &&
          Object.keys(v$.form.location.$model[index].end_date).length === 0"
                 class="text-danger d-block">
            Select end date
          </small>
        </div>
        <div class="ms-1 mb-2" style="min-width: 12rem;">
          <label class="d-block">Request Accomodation</label>
          <div class="d-inline-block" v-for="( accomodation, index) in ['No', 'Yes']">
            <RadioButton v-model=" location.accomodation" :inputId="' accomodation' + index"
                         name=" accomodation" :value=" index"
            />
            <label :for="' accomodation' + index" class="ms-1 me-3 mt-xl-3">{{ accomodation }}</label>
          </div>
          <small v-if="v$.form.location.$dirty &&
          v$.form.location.$model[index].accomodation === null"
                 class="text-danger d-block">
            Select accomodation
          </small>
        </div>
      </div>
      <div class="d-xl-flex mb-2">
        <div class="ms-1 mb-2 me-md-2 w-100">
          <label class="mb-2 d-block">Request flight ticket</label>
          <Dropdown
              class="w-100"
              :class="(v$.form.location.$dirty &&
              v$.form.location.$model[index].flight_type === '') &&
              'p-invalid'"
              :model-value="location.flight_type"
              placeholder="Please select flight type"
              :options="flightType"
              optionLabel="label"
              option-value="value"
              :required="true"
              @update:model-value="location.flight_type = $event"
          />
          <small v-if="v$.form.location.$dirty &&
          v$.form.location.$model[index].flight_type === ''"
                 class="text-danger d-block">
            Select flight type
          </small>
        </div>
        <div class="ms-1 mb-2 me-md-2 w-100">
          <label class="mb-2 d-block">Travelling From</label>
          <InputText class="form-control" v-model="location.from"
                     :class="(v$.form.location.$dirty &&
          Object.keys(v$.form.location.$model[index].from).length === 0) && 'border border-danger'"/>
          <small v-if="v$.form.location.$dirty &&
          Object.keys(v$.form.location.$model[index].from).length === 0"
                 class="text-danger d-block">
            Missing from location
          </small>
        </div>
        <div class="ms-1 mb-2 me-md-2 w-100">
          <label class="mb-2 d-block">Travelling To</label>
          <InputText type="text" class="form-control" v-model="location.to"
                     :class="(v$.form.location.$dirty &&
          Object.keys(v$.form.location.$model[index].to).length === 0) && 'border border-danger'"/>
          <small v-if="v$.form.location.$dirty &&
          Object.keys(v$.form.location.$model[index].to).length === 0"
                 class="text-danger d-block">
            Missing to location
          </small>
        </div>
      </div>
    </FormList>
  </div>
  <button @click="addLocation" class="btn btn-outline-success mt-2">Add Travel Details</button>

  <FormApprovers
      :date-exist="this.form_id === null ? 'exists' : undefined"
      :supervisor="form.approvers.first_approver"
      :hod="form.approvers.second_approver"
      @update:supervisor="form.approvers.first_approver = $event"
      @update:hod="form.approvers.second_approver = $event"
      :error="v$.form.approvers.first_approver.$error || v$.form.approvers.second_approver.$error"
      description="Note: If you have 2 approvers needed for travel authorization form, please assign the respective approvers"
  />

  <h5 class="mt-4 mb-2">Attachment</h5>
  <div class="form-control py-3">
    <FormMultipleAttachment
        :approvers-exists="true"
        @update:fileChange="fileChange($event)"
    />
    <small v-if="v$.form.files.$error" class="text-danger">
      Only maximum of 10 attachments are allowed. Please remove some of your attachments to continue
    </small>
    <div v-if="form.approvers && form.id">
      <AttachmentList :attachment_url="attachment_url" :attachments="form.approvers.attachments"
                      @delete="deleteAttachmentAPI($event)" title="Recent Attachments" :can-delete="true"/>
    </div>
  </div>

  <FormRemarks
      title="Travel Purpose"
      :approvers-exists="true"
      :error="v$.form.purpose.$error"
      description="Please provide the purpose of travel"
      v-model="form.purpose"/>

  <button class="btn btn-success mt-3" @click="submit">Submit</button>

  <Modal v-model="modal.show" :modal="modal" :is-close="modal.isClose" @complete="close"></Modal>

</template>

<script>
import FormRemarks from "../../form/FormRemarks.vue";
import FormApprovers from "../../form/FormApprovers.vue";
import calculateWorkDay from "../../../mixins/calculateWorkDay";
import travelOption from "../../../mixins/travelOption";
import RadioButton from "primevue/radiobutton";
import InputText from "primevue/inputtext";
import InputSwitch from "primevue/inputswitch";
import $api from "../../api";
import FormMultipleAttachment from "../../elements/attachments/FormMultipleAttachment.vue";
import Modal from "../../elements/Modal.vue";
import {useVuelidate} from '@vuelidate/core'
import {required, helpers} from '@vuelidate/validators'
import {mapState} from "pinia";
import {useProfileStore} from "../../../stores/getProfile";
import AttachmentList from "../../elements/attachments/AttachmentList.vue";
import FormList from "../../elements/FormList.vue";

export default {
  components: {
    FormList,
    AttachmentList,
    Modal, FormMultipleAttachment, FormApprovers, FormRemarks,
    RadioButton, InputText, InputSwitch
  },

  props: {
    form_id: {
      type: Number,
      default: null,
    }
  },

  mixins: [calculateWorkDay, travelOption],

  created() {
    if (this.form_id) {
      this.showTravelAPI(this.form_id)
    }
  },

  setup() {
    return {v$: useVuelidate()}
  },

  data() {
    return {
      form: {
        travel_purpose: false,
        main_office: '',
        project_name: '',
        project_location: '',
        location: [],
        reimbursement: null,
        approvers: {
          first_approver: undefined,
          second_approver: undefined,
        },
        files: [],
        purpose: ''
      },
      dateFormat: 'dd/MM/yyyy',
      modal: {
        show: false,
        loader: false,
        isClose: true,
        message: ''
      },
      locations: [],
      attachment_url: localStorage.getItem('currentUrl') + '/e-form/travel-authorization/',
    }
  },

  validations() {
    return {
      form: {
        main_office: {required},
        project_name: {
          required: function () {
            return this.form.travel_purpose || this.form.project_name;
          },
        },
        project_location: {
          required: function () {
            return this.form.travel_purpose || this.form.project_location;
          },
        },
        reimbursement: {required},
        location: {
          required: function () {
            return this.form.location.length !== 0
          },
          $each: helpers.forEach(
              {
                start_date: {required},
                end_date: {required},
                accomodation: {required},
                flight_type: {required},
                from: {required},
                to: {required},
              }
          )
        },
        approvers: {
          first_approver: {
            required: function () {
              return this.form.approvers.first_approver || this.form.approvers.second_approver
            }
          },
          second_approver: {
            required: function () {
              return this.form.approvers.first_approver || this.form.approvers.second_approver
            }
          },
        },
        purpose: {required},
        files: {
          required: function () {
            return this.form.files.length <= 10;
          },
        }
      }
    }
  },

  methods: {
    addLocation() {
      this.v$.$reset();

      this.form.location.push({
        start_date: {},
        end_date: {},
        accomodation: null,
        flight_type: '',
        from: '',
        to: ''
      });
    },

    removeLocation(index) {
      this.form.location.splice(index, 1);
    },

    fileChange(files) {
      this.v$.$reset();
      this.form.files = files;
    },

    close() {
      window.location.href =
          '/e-form/' + localStorage.getItem('user_id') + '/travel/travel-authorization';
    },

    async showTravelAPI(id) {
      await $api.get('/api/travel-authorization/' + id)
          .then(response => {
            this.form = response.data.data;
            this.form.files = [];
          });
    },

    async submit() {
      const validated = await this.v$.$validate()

      if (!validated) {
        return this.modal = {
          show: true,
          loader: false,
          isClose: true,
          message: 'Please fill up all fields before submitting',
        };
      }

      this.modal = {
        show: true,
        loader: true,
      };
      let url = this.form_id ? '/api/travel-authorization/' + this.form_id + '/edit' : '/api/travel-authorization/apply';

      let formData = new FormData();
      (!this.form_id) && formData.append('user_id', this.profile.id);
      formData.append('travel_purpose', this.form.travel_purpose);
      formData.append('department_id', this.profile.department_id);
      !this.form.travel_purpose && formData.append('project_name', this.form.project_name);
      !this.form.travel_purpose && formData.append('project_location', this.form.project_location);
      formData.append('main_office', this.form.main_office);
      formData.append('reimbursement', this.form.reimbursement);
      formData.append('purpose', this.form.purpose);

      if (this.form.approvers.first_approver) {
        formData.append('first_approver_id', this.form.approvers.first_approver.id);
        formData.append('first_approver_status', 'pending');
      }

      if (this.form.approvers.second_approver) {
        formData.append('second_approver_id', this.form.approvers.second_approver.id);
        formData.append('second_approver_status', 'pending');
      }

      for (let i = 0; i < this.form.location.length; i++) {
        this.locations.push(this.form.location[i]);
      }

      formData.append('location', JSON.stringify(this.locations));

      if (this.form.files) {
        // Loop over the files array and append each file individually
        for (let i = 0; i < this.form.files.length; i++) {
          formData.append('files[]', this.form.files[i]);
        }
      }

      await $api
          .post(
              url, formData
          )
          .then((response) => {
            setTimeout(() => {
              this.modal = {
                show: true,
                loader: false,
                isClose: false,
                message: response.data.message,
              };
            }, 500);
          })
          .catch((error) => {
            this.modal = {
              show: true,
              loader: false,
              isClose: true,
              message: error.response.data.message,
            };
          });
    },

    async deleteAttachmentAPI(attachmentId) {
      const params = {
        attachment_id: attachmentId,
      }

      await $api.delete('/api/travel-authorization/' + this.form.id + '/deleteAttachment', {params})
          .then(response => {
            let index = this.form.approvers.attachments.findIndex(obj => obj.id === attachmentId);
            this.form.approvers.attachments.splice(index, 1);
            this.modal = {
              show: true,
              loader: false,
              isClose: true,
              message: response.data.message
            }
          })
          .catch(error => {
            this.modal = {
              show: true,
              loader: false,
              isClose: true,
              message: error.response.data.message
            }
          });
    },
  },


  computed: {
    ...mapState(useProfileStore, ['profile'])
  }
}
</script>
