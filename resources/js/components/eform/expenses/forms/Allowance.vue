<template>
  <div class="d-flex justify-content-between align-items-center mt-5">
    <h5 class="mb-0">Allowance Form</h5>
    <small class="text-secondary">Submission Month:
      <span class="color-primary">{{ this.humanReadableDate(this.date) }}</span>
    </small>
  </div>
  <hr>
  <ProgressLoad v-if="load" :value="progress"/>
  <template v-else>
    <EmptyState
        v-if="allowance.length === 0"
        title="Allowance claim not available"
        subtitle="Click on the button below to add your first allowance claim"
        :show-add-button="true"
        add-label="Allowance" @add="addAllowanceApi"/>
    <div v-else class="d-flex flex-column-reverse flex-lg-row" style="padding-bottom: 60px;">
      <div class="align-self-start shadow rounded mt-2 me-3" style="position: sticky; top: -20px;">
        <TableMain table="table-main sticky mb-0" style="max-height: 75vh;">
          <thead>
          <tr>
            <th class="text-start" style="width: 25px;"></th>
            <th class="text-start" style="width: 260px;">Date</th>
            <th class="text-end pe-3" style="min-width: 100px;">Total</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(allowance, index) in allowance" :key="index">
            <td class="text-center text-secondary">
              <strong>{{ index + 1 }}</strong>
            </td>
            <td class="text-start">
              <div>
                <p>{{ humanReadableDate(allowance.start_date) }} - {{ humanReadableDate(allowance.end_date) }}</p>
                <small class="color-primary">{{ getTotalDay(index) }} day(s)</small> {{ allowance.allowance_type }}
                {{ allowance.allowance_name ? '(' + allowance.allowance_name + ')' : '' }}
                {{ allowance.meal_total_hours ? '(' + allowance.meal_total_hours + ' hrs)' : '' }}
              </div>
            </td>
            <td class="text-end pe-3">RM{{ getAmount(index) }}</td>
          </tr>
          </tbody>
        </TableMain>
        <p class="border-top text-end p-3">
          Total Amount <strong class="color-primary ms-2">RM {{ getTotalAmount }}</strong>
        </p>
      </div>
      <div class="flex-fill">
        <FormList ref="formList" v-for="(allowance, index) in allowance" :key="allowance.id" :index="index"
                  @remove="removeAllowanceApi($event)">
          <div class="d-md-flex gap-3">
            <div class="col d-flex flex-column">
              <label class="form-label mb-1">Allowance Type</label>
              <Dropdown
                  :class="eachError(index, 'allowance_type') && 'p-invalid'"
                  class="w-100"
                  append-to="self"
                  :model-value="allowance.allowance_type"
                  placeholder="Please select allowance type"
                  :options="allowance_type"
                  optionLabel="label"
                  option-value="value"
                  :required="true"
                  @update:model-value="setAllowanceDropdown(allowance, $event)"/>
              <FormError :error="eachError(index, 'allowance_type')" error-text="Allowance Type is required"/>
              <label class="mt-2 mb-1">Start Date</label>
              <Datepicker
                  :class="eachError(index, 'start_date') && 'border rounded-1 border-danger'"
                  :prevent-min-max-navigation="true"
                  :start-date="this.halfMonthBackDated(this.date)"
                  :min-date="this.halfMonthBackDated(this.date)"
                  :max-date="this.halfMonthForwardDated(this.date)"
                  :model-value="allowance.start_date"
                  :enable-time-picker="false"
                  placeholder="dd/MM/yyyy"
                  auto-apply
                  :state="null"
                  :clearable="false"
                  format="dd/MM/yyyy"
                  @update:model-value="allowance.allowance_type === 'Meal Allowance' ? allowance.end_date = allowance.start_date = formDataDate($event) : allowance.start_date = formDataDate($event) "/>
              <FormError :error="eachError(index, 'start_date')" error-text="Start Date is required"/>
              <label class="form-label mt-2 mb-1">End Date</label>
              <Datepicker
                  :class="eachError(index, 'end_date') && 'border rounded-1 border-danger'"
                  :prevent-min-max-navigation="true"
                  :start-date="this.halfMonthBackDated(this.date)"
                  :model-value="allowance.end_date"
                  :enable-time-picker="false"
                  placeholder="dd/MM/yyyy"
                  auto-apply
                  :state="null"
                  :clearable="false"
                  format="dd/MM/yyyy"
                  :min-date="allowance.start_date"
                  :max-date="this.halfMonthForwardDated(this.date)"
                  :disabled="allowance.allowance_type === 'Meal Allowance'"
                  @update:model-value="allowance.end_date = formDataDate($event)"/>
              <FormError :error="eachError(index, 'end_date')" error-text="End Date is required"/>
              <label v-if="allowance.allowance_type === 'Meal Allowance'" class="form-label mt-2 mb-1">Total
                Hours</label>
              <Dropdown
                  :class="eachError(index, 'meal_total_hours') && 'p-invalid'"
                  v-if="allowance.allowance_type === 'Meal Allowance'"
                  class="w-100"
                  append-to="self"
                  :model-value="allowance.meal_total_hours"
                  placeholder="Please select total hours"
                  :options="total_hours"
                  optionLabel="label"
                  option-value="value"
                  @update:model-value="setMealAllowanceHour(allowance, $event)"/>
              <FormError :error="eachError(index, 'meal_total_hours')" error-text="Total Hours is required"/>
              <div v-if="allowance.allowance_type === 'Others'">
                <label class="form-label mt-2 mb-1">Allowance Name</label>
                <InputText :class="eachError(index, 'allowance_name') && 'p-invalid'"
                           class="form-control" v-model="allowance.allowance_name"
                           placeholder="Please specify the allowance type"/>
                <FormError :error="eachError(index, 'allowance_name')" error-text="Allowance Name is required"/>
                <label class="form-label mt-2 mb-1">Allowance Rate</label>
                <InputNumber :class="eachError(index, 'allowance_rate') && 'p-invalid'" class="d-flex"
                             v-model="allowance.allowance_rate"
                             mode="currency" currency="MYR" :min="0" :max="2000" placeholder="Allowance rate"/>
                <FormError :error="eachError(index, 'allowance_rate')" error-text="Allowance Rate is required"/>
              </div>
              <FormRemarks
                  description=""
                  title="Remark"
                  placeholder="Remark is optional"
                  v-model="allowance.remark"
                  :show-title="false"
              />
            </div>
            <div class="col d-flex flex-column">
              <ApiAttachment
                  :travel-id="this.travelId"
                  :id="allowance.id"
                  api="allowance"
                  :path="allowance.path"
                  :error="eachError(index, 'path')"
                  @updatePath="(event) => allowance.path = event"
              />
            </div>
          </div>
        </FormList>
      </div>
    </div>
    <FloatingButton @add="addAllowanceApi" add-label="Allowance" :is-editing="$parent.$props.form_id !== undefined"
                    :is-empty="allowance.length === 0" @cancel="$emit('cancel')" @reset="$emit('reset')"
                    @submit="next"/>

  </template>
</template>

<script>
import FormRemarks from "../../../form/FormRemarks.vue"
import TableMain from "../../../elements/TableMain.vue"
import travelOption from "../../../../mixins/travelOption";
import EmptyState from "../../../elements/EmptyState.vue";
import {useVuelidate} from "@vuelidate/core";
import {helpers, required} from "@vuelidate/validators";
import ApiAttachment from "../../../elements/attachments/ApiAttachment.vue";
import ProgressLoad from "../../../elements/ProgressLoad.vue";
import $api from "../../../api";
import {useModalStore} from "../../../../stores/modal";
import {useLoadButton} from "../../../../stores/loadButton";
import InputNumber from "primevue/inputnumber";
import InputText from 'primevue/inputtext';
import Conversion from "../../../../mixins/conversion";
import FormList from "../../../elements/FormList.vue";
import FloatingButton from "./FloatingButton.vue";
import filterOption from "../../../../mixins/filterOption";
import FormError from "../../../elements/FormError.vue";
import {mapState} from "pinia";
import {useProfileStore} from "../../../../stores/getProfile";
import calculateWorkDay from "../../../../mixins/calculateWorkDay";

export default {
  components: {
    FormError,
    FloatingButton,
    FormList,
    EmptyState,
    FormRemarks,
    TableMain,
    ApiAttachment,
    ProgressLoad,
    InputNumber,
    InputText,
  },
  mixins: [travelOption, Conversion, filterOption, calculateWorkDay],
  props: ['travelId', 'date'],
  emits: ['reset', 'cancel'],
  created() {
    this.page = 1;
    this.total_page = null;
    this.allowanceIndexApi();
    this.filterOption();
  },

  setup() {
    return {v$: useVuelidate()}
  },
  data() {
    return {
      allowance: [],
      page: 1,
      total_page: null,
      load: true,
      allowance_total: 0,
      allowance_type: []
    }
  },
  validations() {
    return {
      allowance: {
        $each: helpers.forEach(
            {
              start_date: {required},
              end_date: {required},
              allowance_type: {required},
              allowance_name: {
                isValid: function (value, m) {
                  return value || m.allowance_type !== 'Others';
                }
              },
              allowance_rate: {required},
              meal_total_hours: {
                isValid: function (value, m) {
                  return m.meal_total_hours > 0 || m.allowance_type !== 'Meal Allowance'
                }
              },
              path: {
                isValid: function (value, m) {
                  return value || m.allowance_type !== 'Meal Allowance'
                }
              }
            }
        )
      }
    }
  },
  computed: {
    getTotalAmount() {
      this.allowance_total = 0;

      for (let i = 0; i < this.allowance.length; i++) {
        this.allowance_total += this.allowance[i].amount
      }

      if (this.allowance_total === 0) {
        return this.allowance_total.toFixed(2);
      }

      return this.allowance_total.toFixed(2);
    },
    progress() {
      let progress = (this.page / this.total_page * 100).toFixed(2);
      return parseInt(progress);
    },
    ...mapState(useProfileStore, ['profile'])
  },
  methods: {
    filterOption() {

      this.allowance_type = this.allowanceType.map((x) => x)
      let joining_date = useProfileStore().profile.joining_date
      let index = 0;
      if (this.calculateYearToDate(joining_date) > 3) {
        index = this.allowance_type.findIndex((obj) => obj.value === 'Offshore (Category 2)')
      } else {
        index = this.allowance_type.findIndex((obj) => obj.value === 'Offshore (Category 1)')
      }

      this.allowance_type.splice(index, 1)
    },

    setAllowanceDropdown(allowance, allowanceType) {
      allowance.allowance_type = allowanceType
      allowance.allowance_name = null
      allowance.allowance_rate = 0
      allowance.amount = 0
      allowance.end_date = null
      allowance.meal_total_hours = 0
      allowance.start_date = null
      allowance.total_day = 0

      if (allowanceType !== 'Others') {
        allowance.allowance_name = ''
        allowance.allowance_rate = this.allowanceType.find(type => type.value === allowanceType).allowance
      } else {
        allowance.allowance_name = null
        allowance.allowance_rate = null
      }
    },

    setMealAllowanceHour(allowance, total_hours) {
      allowance.meal_total_hours = total_hours
      allowance.allowance_rate = this.allowanceType.find(type => type.value === 'Meal Allowance').allowance
    },

    getTotalDay(index) {
      this.allowance[index].total_day = this.rawDaysBetweenDate(this.allowance[index].start_date, this.allowance[index].end_date)
      return this.allowance[index].total_day
    },

    getAmount(index) {
      this.allowance[index].amount = this.allowance[index].allowance_rate * (this.allowance[index].allowance_type !== 'Meal Allowance' ? this.allowance[index].total_day : (this.allowance[index].meal_total_hours / 4))
      return this.allowance[index].amount.toFixed(2)
    },

    eachError(index, key, value = '') {
      let checkValue = false;

      (value !== '') ? checkValue = true
          : checkValue = this.allowance[index][key] !== value

      return (
          this.v$.allowance.$dirty && checkValue &&
          this.v$.allowance.$each.$response.$errors[index][key][0] !== undefined
      );
    },

    async allowanceIndexApi() {
      await $api.get('/api/travel-claim/' + this.travelId + '/allowance?page=' + this.page)
          .then(response => {
            this.total_page = response.data.meta.last_page;

            response.data.data.forEach((data) => this.allowance.push(data));

            if (this.page < this.total_page) {
              this.page++;
              setTimeout(() => {
                this.allowanceIndexApi()
              }, 1000)
            } else {
              this.load = false;
            }
          })
          .catch(e => {
            this.load = false;
            console.log(e);
          })
      ;
    },

    async addAllowanceApi() {
      this.v$.$reset();
      useLoadButton().start();

      let formData = new FormData();
      this.allowance.forEach((data) => formData.append('allowance[]', JSON.stringify(data)));

      await $api.post('/api/travel-claim/' + this.travelId + '/allowance-add', formData)
          .then(response => {
            setTimeout(() => {
              useLoadButton().finish();
              this.$toast.add({severity: 'success', summary: 'Success', detail: 'Allowance added', life: 3000});
              this.allowance.push(response.data.data);
            }, 1000)
          })
          .catch(e => {
            useLoadButton().finish();
            console.log(e);
          })
    },

    async removeAllowanceApi(index) {
      useLoadButton().start();
      await $api.delete('/api/travel-claim/' + this.travelId + '/allowance-delete?id=' + this.allowance[index].id)
          .then(response => {
            setTimeout(() => {
              this.allowance.splice(index, 1);
              useLoadButton().finish();
              this.$toast.add({severity: 'success', summary: 'Success', detail: response.data.message, life: 3000});
            }, 500)
          })
          .catch(e => {
            useLoadButton().finish();
            console.log(e);
          })
    },

    async next() {
      const validated = await this.v$.$validate()
      if (!validated) {
        return useModalStore().show('Please fill up all fields before submitting')
      }
      useLoadButton().start();

      let formData = new FormData();
      this.allowance.forEach((data) => formData.append('allowance[]', JSON.stringify(data)));

      await $api.post('/api/travel-claim/' + this.travelId + '/allowance', formData)
          .then(response => {
            setTimeout(() => {
              useLoadButton().finish();
              this.$toast.add({severity: 'success', summary: 'Success', detail: response.data.message, life: 3000});
              this.$parent.$data.activeIndex += 1;
            }, 1000);
          })
          .catch(err => {
            useLoadButton().finish();
            useModalStore().show(err.response.data.message)
          });

    },
  },
}
</script>
