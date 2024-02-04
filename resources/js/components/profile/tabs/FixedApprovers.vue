<template>
  <div class="mt-2">
    <h5 class="d-inline-block me-2">Fixed Approvers</h5>
    <button v-if="this.disabled && isAdmin" @click="edit" class="button">Edit</button>
    <hr>
  </div>
  <div class="text-center my-5" v-if="approvers.length === 0">
    <i class="bi bi-person-fill-x text-secondary" style="font-size: 5rem;"></i>
    <h5 class="fw-normal">There are no approvers assigned to this profile.</h5>
  </div>
  <div v-else class="d-xl-flex flex-row">
    <div v-show="! this.disabled" class="col-xl-4 pe-xl-2">
      <h5 class="mb-3">Assign Approvers</h5>
      <div class="card" v-for="(approver, index) in approvers">
        <div class="w-100">
          <label class="form-label">Select {{ setOrdinalNumber(index + 1) }} approver</label>
          <Dropdown
              :modelValue="approver.user"
              :options="userOptions"
              optionLabel="name"
              :filter="true"
              placeholder="Search staff"
              :showClear="false"
              class="w-100"
              @update:modelValue="setApproverDropdown(index, $event)">
            <template #value="slotProps">
              <p v-if="slotProps.value">{{ slotProps.value.name }}</p>
              <span v-else>{{ slotProps.placeholder }}</span>
            </template>
            <template #option="slotProps">
              {{ slotProps.option.name }}
            </template>
          </Dropdown>
        </div>
        <div class="mt-2">
          <button v-if="index !== 0" @click="removeApprover(index)" class="btn btn-outline-danger">
            Remove
          </button>
        </div>
      </div>
      <button @click="addApprover" class="btn btn-outline-success mb-3">Add Approver</button>
    </div>
    <div :class="[this.disabled ? 'col-xl-12' : 'col-xl-8 ps-md-2']">
      <h5 v-show="! this.disabled" class="mb-3">Preview</h5>
      <div class="d-flex flex-column align-items-center" :class="[this.disabled ? 'py-5' : 'card']">
        <div v-for="(approver, index) in approvers.slice().reverse()" :key="index"
             class="d-flex flex-column align-items-center">
          <span class="d-inline-block border rounded-5 shadow p-3">
            <TableProfile :user="approver.user"/>
          </span>
          <div class="arrow-up">
            <span></span>
            <span></span>
            <span></span>
            <strong class="arrow-title color-primary">{{ setOrdinalNumber(approvers.length - index) }} approver</strong>
          </div>
        </div>

        <span class="d-inline-block border rounded-5 shadow p-3">
          <TableProfile :user="user"/>
        </span>
      </div>
    </div>

  </div>

  <div>
    <button v-show="!this.disabled" @click="save" class="btn btn-success">Save</button>
  </div>

  <Modal
      v-model="modal.show"
      :modal="modal"
      :is-close="true"
  />
</template>

<script>
import Conversion from "../../../mixins/conversion";
import TableProfile from "../../elements/TableProfile.vue";
import Modal from "../../elements/Modal.vue";
import {useDynamicUsersStore} from "../../../stores/dynamicUsers";
import $api from "../../api";
import {helpers, required} from "@vuelidate/validators";
import {useVuelidate} from "@vuelidate/core";
import {mapState} from "pinia";


export default {
  components: {TableProfile, Modal},
  mixins: [Conversion],
  props: ['user'],

  setup() {
    return {v$: useVuelidate()}
  },

  mounted() {
    this.userOptions = this.users.map((x) => x)
    this.getApproversApi();
  },

  data() {
    return {
      approvers: [],
      disabled: true,
      isAdmin: parseInt(localStorage.getItem('isAdmin')) === 1,
      userOptions: [],
      modal: {
        loader: false,
        show: false,
        message: ''
      }
    }
  },

  validations() {
    return {
      approvers: {
        required: function () {
          return this.approvers.length !== 0
        },
        $each: helpers.forEach(
            {
              user: {required},
            }
        )
      }
    }
  },
  computed: {
    ...mapState(useDynamicUsersStore, ['users']),

    getSelectedOptions() {
      let selectedOption = []
      this.approvers.forEach((data) => {
        data.user && selectedOption.push(data.user.id)
      })
      return selectedOption
    }
  },
  methods: {
    edit() {
      if (this.approvers.length === 0) {
        this.approvers.push({
          user: null
        });
      }
      this.disabled = false;
    },

    addApprover() {
      this.approvers.push({
        user: null
      });
    },

    filterUser() {
      this.userOptions = this.users.filter((option) => !this.getSelectedOptions.includes(option.id))
    },

    setApproverDropdown(index, event) {
      this.approvers[index].user = event
      this.filterUser()
    },

    removeApprover(index) {
      this.approvers.splice(index, 1);
      this.filterUser()
    },

    async getApproversApi() {
      let params = {}

      params.user_id = this.user.id;

      await $api.get('/api/administration/fixed-approvers', {params})
          .then((res) => {
            res.data.data.approvers.forEach((data) => {
              this.approvers.push({
                user: data
              })
            });
            this.filterUser()
          })
          .catch((res) => {
            console.log(res.response.data.message)
          })
    },

    async save() {
      const validated = await this.v$.$validate()

      if (!validated) {
        return alert('Please select approver from the dropdown box')
      }

      let approvers = [];
      this.approvers.forEach((obj) => {
        approvers.push(obj.user.id);
      })

      let formData = new FormData();
      formData.append('user_id', this.user.id);
      formData.append('approvers', approvers);

      await $api.post('/api/administration/fixed-approvers', formData)
          .then((response) => {
            this.modal = {
              show: true,
              loader: true,
            }
            setTimeout(() => {
              this.disabled = true;
              this.modal = {
                show: true,
                loader: false,
                message: response.data.message,
              };
            }, 500);
          })
          .catch(() => {
            this.modal = {
              show: true,
              loader: false,
              message: 'Opps something went wrong, please try again',
            };
          });
    }
  }
}
</script>
