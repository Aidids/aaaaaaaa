<template>
  <div class="d-flex justify-content-between align-items-center mt-5">
    <h5 class="mb-0">Transport Form</h5>
    <small class="text-secondary">Submission Month:
      <span class="color-primary">{{ this.humanReadableDate(this.date) }}</span>
    </small>
  </div>
  <hr>
  <ProgressLoad v-if="load" :value="progress"/>
  <template v-else>
    <EmptyState v-if="transports.length === 0"
                title="Transport claim not available"
                subtitle="Click on the button below to add your first transport claim"
                :show-add-button="true"
                add-label="Transport" @add="addTransportApi"></EmptyState>
    <div v-else class="d-flex flex-column-reverse flex-lg-row" style="padding-bottom: 60px;">
      <div class="align-self-start shadow rounded mt-2 me-3" style="position: sticky; top: -20px;">
        <TableMain table="table-main sticky mb-0" style="max-height: 75vh;">
          <thead>
          <tr>
            <th class="text-start" style="width: 25px;"></th>
            <th class="text-start" style="min-width: 270px;">Details</th>
            <th class="text-end pe-3" style="min-width: 100px;">Total</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(transport, index) in transports" :key="index">
            <td class="text-center">{{ index + 1 }}</td>
            <td class="text-start">
              <div v-if="transport.transport_type === 'Mileage'">
                <p>From: <strong>{{ transport.start_name ?? transport.start_location ?? 'Not selected' }}</strong></p>
                <p>To: <strong>{{ transport.end_name ?? transport.end_location ?? 'Not selected' }}</strong></p>
                <small v-if="transport.date">{{ displayDate(transport.date) }}</small>
              </div>
              <strong v-else>{{ transport.transport_type }}</strong>

            </td>
            <td class="text-end pe-3">{{ 'RM ' + (transport.amount ?? 0).toFixed(2) }}</td>
          </tr>
          </tbody>
        </TableMain>
        <p class="border-top text-end p-3">
          Total Amount <strong class="color-primary ms-2">RM {{ getTotal.toFixed(2) }}</strong>
        </p>
      </div>
      <div class="flex-fill">
        <FormList v-for="(transport, index) in transports" :key="transport.id" :index="index" :data="transport"
                  @remove="removeTransportApi($event)">
          <div class="d-md-flex gap-3">
            <div class="col d-flex flex-column w-lg-50">
              <label class="mb-1">Claim Type</label>
              <Dropdown
                  :class="eachError(index, 'transport_type') && 'p-invalid'"
                  :model-value="transport.transport_type"
                  placeholder="Select claim type"
                  :options="options"
                  optionLabel="label"
                  option-value="value"
                  :required="true"
                  @update:model-value="setTransportDropdown($event, transport)">
                <template #value="slotProps">
                  <p v-if="slotProps.value">{{ slotProps.value }}</p>
                  <span v-else>{{ slotProps.placeholder }}</span>
                </template>
                <template #option="slotProps">
                  {{ slotProps.option.label }}
                </template>
              </Dropdown>
              <FormError :error="eachError(index, 'transport_type')" error-text="Claim Type is required"/>
              <div v-if="transport.transport_type && transport.transport_type !== 'Mileage'"
                   class="col d-flex flex-column">
                <label class="mt-2 mb-1">Amount</label>
                <InputNumber
                    :class="eachError(index, 'amount') && 'p-invalid'"
                    v-model="transport.amount" mode="currency" currency="MYR" :min="0"
                    :max="9999999"
                    placeholder="Enter amount"/>
                <FormError :error="eachError(index, 'amount')" error-text="Amount is required"/>
              </div>
              <div v-else-if="transport.transport_type" class="col d-flex flex-column">
                <label class="mt-2 mb-1">Date</label>
                <Datepicker
                    :class="eachError(index, 'date') && 'border rounded-1 border-danger'"
                    :prevent-min-max-navigation="true"
                    :start-date="this.halfMonthBackDated(this.date)"
                    :min-date="this.halfMonthBackDated(this.date)"
                    :max-date="this.halfMonthForwardDated(this.date)"
                    :model-value="transport.date"
                    :enable-time-picker="false"
                    placeholder="dd/MM/yyyy"
                    auto-apply
                    :state="null"
                    :clearable="false"
                    format="dd/MM/yyyy"
                    @update:model-value="transport.date = convertDate($event)"/>
                <FormError :error="eachError(index, 'date')" error-text="Date is required"/>
                <label class="mt-2 mb-1">Start Location</label>
                <Dropdown
                    :class="eachError(index, 'start_location') && 'p-invalid'"
                    :model-value="transport.start_location"
                    placeholder="Select start location"
                    :options="travelFrom"
                    optionLabel="label"
                    option-value="value"
                    :required="true"
                    @update:model-value="setFromDropdown($event, transport)"/>
                <FormError :error="eachError(index, 'start_location')"
                           error-text="Allowance Type is required"/>
                <InputText :class="eachError(index, 'start_name') && 'p-invalid'"
                           v-show="transport.start_location && transport.start_location === 'Others'" type="text"
                           class="form-control mt-2" v-model="transport.start_name"
                           placeholder="Enter start location"/>
                <FormError :error="eachError(index, 'start_name')" error-text="Start location is required"/>
                <label class="mt-2 mb-1">Destination</label>
                <Dropdown
                    :class="eachError(index, 'end_location') && 'p-invalid'"
                    :model-value="transport.end_location"
                    placeholder="Select destination"
                    :options="transport.start_location === `KL OFFICE(VSQ)` ? this.travelToKL : this.travelToKSB"
                    optionLabel="label"
                    option-value="value"
                    :required="true"
                    :disabled="transport.start_location && transport.start_location === 'Others'"
                    @update:model-value="setToDropdown($event, transport)"/>
                <FormError :error="eachError(index, 'end_location')" error-text="Destination is required"/>
                <InputText
                    :class="eachError(index, 'end_name') && 'p-invalid'"
                    v-show="transport.end_location && transport.end_location === 'Others' || transport.start_location && transport.start_location === 'Others'"
                    type="text" class="form-control mt-2" v-model="transport.end_name"
                    placeholder="Enter destination"/>
                <FormError :error="eachError(index, 'end_name')" error-text="Destination is required"/>
                <label class="mt-2 mb-1">Distance (km)</label>
                <InputNumber :class="eachError(index, 'total_distance') && 'p-invalid'"
                             v-model="transport.total_distance" placeholder="Enter the distance" :min="0"
                             :max="9999999"
                             :disabled="(transport.end_location && transport.end_location !== 'Others') && (transport.start_location && transport.start_location !== 'Others')"
                             @update:model-value="this.getAmount(transport)"
                />
                <FormError :error="eachError(index, 'total_distance')" error-text="Distance is required"/>
              </div>
              <FormRemarks
                  description=""
                  placeholder="Remark is optional"
                  title="Remark"
                  v-model="transport.remark"
                  :show-title="false"/>
            </div>
            <div class="col d-flex flex-column">
              <ApiAttachment
                  :travel-id="travelId"
                  :id="transport.id"
                  api="transport"
                  :path="transport.path"
                  :error="eachError(index, 'path')"
                  @updatePath="(event) => transport.path = event"/>
            </div>
          </div>
        </FormList>
      </div>
    </div>
    <FloatingButton @add="addTransportApi" add-label="Transport" :is-editing="$parent.$props.form_id !== undefined"
                    :is-empty="transports.length === 0" @cancel="$emit('cancel')" @reset="$emit('reset')"
                    @submit="next"/>
  </template>
</template>


<script>
import FormRemarks from "../../../form/FormRemarks.vue"
import TableMain from "../../../elements/TableMain.vue"
import travelOption from "../../../../mixins/travelOption";
import calculateWorkDay from "../../../../mixins/calculateWorkDay";
import conversion from "../../../../mixins/conversion";
import EmptyState from "../../../elements/EmptyState.vue";
import {useVuelidate} from "@vuelidate/core";
import {helpers, required} from "@vuelidate/validators";
import ApiAttachment from "../../../elements/attachments/ApiAttachment.vue";
import $api from "../../../api";
import ProgressLoad from "../../../elements/ProgressLoad.vue";
import {useModalStore} from "../../../../stores/modal";
import {useLoadButton} from "../../../../stores/loadButton";
import InputNumber from "primevue/inputnumber";
import InputText from "primevue/inputtext";
import FormList from "../../../elements/FormList.vue";
import FloatingButton from "./FloatingButton.vue";
import FormError from "../../../elements/FormError.vue";

export default {
  components: {
    FormError,
    FloatingButton,
    FormList,
    ProgressLoad,
    ApiAttachment,
    EmptyState,
    FormRemarks,
    TableMain,
    InputNumber,
    InputText,
  },
  props: ['travelId', 'date'],
  mixins: [travelOption, calculateWorkDay, conversion],
  emits: ['cancel', 'reset'],
  setup() {
    return {v$: useVuelidate()}
  },
  data() {
    return {
      load: true,
      transports: [],
      transport_total: 0,
      options: [],
      mileage_rate: 0.6,
      page: 1,
      total_page: null,
    }
  },
  created() {
    this.page = 1;
    this.total_page = null;
    this.options = this.transportOption.map((x) => x)
    this.transportIndexApi();
  },
  validations() {
    return {
      transports: {
        $each: helpers.forEach(
            {
              transport_type: {required},
              amount: {
                isValid: function (value, m) {
                  return value > 0
                }
              },
              start_location: {
                isValid: function (value, m) {
                  return value || m.transport_type !== 'Mileage'
                }
              },
              start_name: {
                isValid: function (value, m) {
                  return value || m.start_location !== 'Others' || m.transport_type !== 'Mileage'
                }
              },
              end_location: {
                isValid: function (value, m) {
                  return value || m.transport_type !== 'Mileage'
                }
              },
              end_name: {
                isValid: function (value, m) {
                  return value || m.end_location !== 'Others' || m.transport_type !== 'Mileage'
                }
              },
              date: {
                isValid: function (value, m) {
                  return value || m.transport_type !== 'Mileage'
                }
              },
              total_distance: {
                isValid: function (value, m) {
                  return value || m.transport_type !== 'Mileage'
                }
              },
              path: {
                isValid: function (value, m) {
                  return value || (m.start_location !== 'Others' && m.end_location !== 'Others')
                      && (m.transport_type !== 'Parking' && m.transport_type !== 'Toll'
                          && m.transport_type !== 'Public Transport' && m.transport_type !== 'Fuel')
                }
              }
            }
        )
      }
    }
  },
  computed: {
    getTotal() {
      this.transport_total = 0
      for (let i = 0; i < this.transports.length; i++) {
        this.transport_total += this.transports[i].amount
      }
      return this.transport_total
    },
    progress() {
      let progress = (this.page / this.total_page * 100).toFixed(2);
      return parseInt(progress);
    },
    getSelectedOptions() {
      let selectedOption = []
      this.transports.forEach((data) => {
        if (data.transport_type !== 'Mileage') {
          selectedOption.push(data.transport_type)
        }
      })
      return selectedOption
    }
  },

  methods: {
    setOption() {
      this.options = this.transportOption.filter((option) => !this.getSelectedOptions.includes(option.value))
    },

    setTransportDropdown(transportValue, transport) {
      transport.start_location = null
      transport.start_name = null
      transport.end_name = null
      transport.end_location = null
      transport.total_distance = null
      transport.amount = null
      transport.transport_type = transportValue
      this.setOption()
    },

    setFromDropdown(travelFromValue, transport) {
      transport.start_location = travelFromValue
      transport.start_name = null
      transport.end_name = null
      transport.end_location = null
      transport.total_distance = null
      transport.amount = null
      if (travelFromValue !== 'Others') {
        transport.start_name = travelFromValue
      } else {
        transport.end_location = 'Others'
      }
    },

    setToDropdown(toValue, transport) {
      transport.end_location = toValue
      if (toValue !== 'Others') {
        transport.end_name = toValue
        let dest = transport.start_location === 'KL OFFICE(VSQ)' ? this.travelToKL : this.travelToKSB
        transport.total_distance = dest.find(location => location.label === toValue).distance
        this.getAmount(transport)
      }
    },

    reset() {
      useModalStore().confirm('Resetting will clear all information. Do you want to proceed?')
    },

    getAmount(transport) {
      transport.amount = transport.total_distance * this.mileage_rate
    },

    eachError(index, key, value = '') {
      let checkValue = false;

      (value !== '') ? checkValue = true
          : checkValue = this.transports[index][key] !== value

      return (
          this.v$.transports.$dirty && checkValue &&
          this.v$.transports.$each.$response.$errors[index][key][0] !== undefined
      );
    },

    async transportIndexApi() {
      await $api.get('/api/travel-claim/' + this.travelId + '/transport?page=' + this.page)
          .then(response => {
            this.total_page = response.data.meta.last_page;

            response.data.data.forEach((data) => {
              if (data.start_location === undefined) {
                data.start_location = null
              }
              this.transports.push(data)
              this.setOption()
            });

            if (this.page < this.total_page) {
              this.page++;
              setTimeout(() => {
                this.transportIndexApi()
              }, 1000)
            } else {
              this.load = false;
            }
          }).catch(e => {
            this.load = false;
            console.log(e);
          });
    },

    async addTransportApi() {
      this.v$.$reset();
      useLoadButton().start();

      let formData = new FormData();
      this.transports.forEach((data) => formData.append('transport[]', JSON.stringify(data)));

      await $api.post('/api/travel-claim/' + this.travelId + '/transport-add', formData)
          .then(response => {
            setTimeout(() => {
              useLoadButton().finish();
              this.$toast.add({severity: 'success', summary: 'Success', detail: 'Transport added', life: 3000});
              this.transports.push(response.data.data);
            }, 1000)
          }).catch(e => {
            useLoadButton().finish()
            useModalStore().show(e.response.data.message)
          })
    },

    async removeTransportApi(index) {
      useLoadButton().start();

      await $api.delete('/api/travel-claim/' + this.travelId + '/transport-delete?id=' + this.transports[index].id)
          .then(response => {
            setTimeout(() => {
              this.setOption()
              this.transports.splice(index, 1);
              useLoadButton().finish();
              this.$toast.add({severity: 'success', summary: 'Success', detail: response.data.message, life: 3000});
            }, 1000)
          }).catch(e => {
            useLoadButton().finish()
            useModalStore().show(e.response.data.message)
          })
    },

    async next() {
      const validated = await this.v$.$validate()

      if (!validated) {
        return useModalStore().show('Please fill up all fields before submitting')
      }
      useLoadButton().start();

      let formData = new FormData();
      this.transports.forEach((data) => formData.append('transport[]', JSON.stringify(data)));

      await $api.post('/api/travel-claim/' + this.travelId + '/transport', formData)
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
