<template>
  <div class="d-flex justify-content-between align-items-center mt-5">
    <h5 class="mb-0">Expense Form</h5>
    <div>
      <small class="text-secondary">Submission Month:
        <span class="color-primary">{{ this.humanReadableDate(this.date) }}</span>
      </small>
    </div>
  </div>
  <hr>
  <ProgressLoad v-if="load" :value="progress"/>
  <template v-else>
    <EmptyState
        v-if="expenses.length === 0"
        title="Expense claim not available"
        subtitle="Click on the button below to add your first expense claim"
        :show-add-button="true"
        add-label="Expense" @add="addExpenseApi"/>
    <div v-else class="d-flex flex-column-reverse flex-lg-row" style="padding-bottom: 60px;">
      <div class="align-self-start shadow rounded mt-2 me-3" style="position: sticky; top: -20px;">
        <TableMain table="table-main sticky mb-0" style="max-height: 75vh;">
          <thead>
          <tr>
            <th class="text-start" style="width: 25px;"></th>
            <th class="text-start" style="width: 260px;">Description</th>
            <th class="text-end pe-3" style="min-width: 100px;">Total</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(expense, index) in expenses" :key="index">
            <td class="text-center text-secondary">
              <strong>{{ index + 1 }}</strong></td>
            <td class="text-start">
              <strong>{{ expense.description ?? 'Not Selected' }}
                {{ expense.description_name ? '(' + expense.description_name + ')' : '' }}</strong>
            </td>
            <td class="text-end pe-3">RM {{ (expense.amount ?? 0).toFixed(2) }}</td>
          </tr>
          </tbody>
        </TableMain>
        <p class="border-top text-end p-3">
          Total Amount <strong class="color-primary ms-2">RM {{ getTotalAmount.toFixed(2) }}</strong>
        </p>
      </div>
      <div class="flex-fill">
        <FormList v-for="(expense, index) in expenses" :key="expense.id" :index="index"
                  @remove="removeExpenseApi($event)">
          <div class="d-md-flex gap-3">
            <div class="col d-flex flex-column">
              <label class="mb-1">Category</label>
              <Dropdown
                  :class="eachError(index, 'description') && 'p-invalid'"
                  :model-value="expense.description"
                  placeholder="Select expense"
                  :options="options"
                  optionLabel="label"
                  :required="true"
                  @update:model-value="setExpensesDropdown(index, $event)">
                <template #value="slotProps">
                  <p v-if="slotProps.value">{{ slotProps.value }}</p>
                  <span v-else>{{ slotProps.placeholder }}</span>
                </template>
                <template #option="slotProps">
                  {{ slotProps.option.label }}
                </template>
              </Dropdown>
              <small v-if="eachError(index, 'description')" class="text-danger">*Expense is required</small>
              <div v-if="expense.description === 'Others'" type="text">
                <label class="mt-2 mb-1">Description</label>
                <InputText :class="eachError(index, 'description_name') && 'p-invalid'"
                           class="form-control" v-model="expense.description_name"
                           placeholder="Enter expense description"/>
                <small v-if="eachError(index, 'description_name')" class="text-danger">*Description is required</small>
              </div>
              <label class="mt-2 mb-1">Account Code</label>
              <InputText v-model="expense.account_code" placeholder="Enter Code"
                         type="text" class="form-control mb-1" maxlength="100"/>
              <label class="mt-2 mb-1">Amount</label>
              <InputNumber :class="eachError(index, 'amount') && 'p-invalid'"
                           v-model="expense.amount" mode="currency" currency="MYR" :min="0"
                           :max="9999999"
                           placeholder="Enter amount"/>
              <small v-if="eachError(index, 'amount')" class="text-danger">*Amount is required</small>

              <FormRemarks title="Remark" v-model="expense.remark" :show-title="false"/>
            </div>
            <div class="col d-flex flex-column">
              <ApiAttachment
                  :travel-id="travelId"
                  :id="expense.id"
                  api="expense"
                  :path="expense.path"
                  :error="eachError(index, 'path')"
                  @updatePath="(event) => expense.path = event"
              />
            </div>
          </div>
        </FormList>
      </div>
    </div>
    <FloatingButton @add="addExpenseApi" add-label="Expense" :is-editing="$parent.$props.form_id !== undefined"
                    :is-empty="expenses.length === 0" @cancel="$emit('cancel')" @reset="$emit('reset')"
                    @submit="submit" submit-label="Submit"/>
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
import $api from "../../../api";
import ProgressLoad from "../../../elements/ProgressLoad.vue";
import {useModalStore} from "../../../../stores/modal";
import {useLoadButton} from "../../../../stores/loadButton";
import InputNumber from "primevue/inputnumber";
import InputText from "primevue/inputtext";
import FormList from "../../../elements/FormList.vue";
import FloatingButton from "./FloatingButton.vue";
import conversion from "../../../../mixins/conversion";

export default {
  components: {
    FloatingButton,
    FormList,
    ProgressLoad,
    ApiAttachment, EmptyState, FormRemarks, TableMain, InputNumber, InputText,
  },
  props: ['travelId', 'date'],
  mixins: [travelOption, conversion],
  emits: ['cancel', 'reset'],
  setup() {
    return {v$: useVuelidate()}
  },
  data() {
    return {
      load: true,
      expenses: [],
      options: [],
      expense_total: 0,
      page: 1,
      total_page: null,
      meal_allowance_rate: 25
    }
  },
  validations() {
    return {
      expenses: {
        $each: helpers.forEach(
            {
              description: {required},
              description_name: {
                isValid: function (value, m) {
                  return value || m.description !== 'Others'
                }
              },
              total_hours: {
                isValid: function (value, m) {
                  return m.total_hours > 0 || m.description !== 'Meal Allowance'
                }
              },
              amount: {
                isValid: function (value, m) {
                  return m.amount > 0 || m.description === 'Meal Allowance'
                }
              },
              path: {required}
            }
        )
      }
    }
  },
  created() {
    this.page = 1;
    this.total_page = null;
    this.options = this.expenseOption.map((x) => x)
    this.expensesIndexApi();
  },
  computed: {
    getTotalAmount() {
      this.expense_total = 0
      for (let i = 0; i < this.expenses.length; i++) {
        this.expense_total += this.expenses[i].amount
      }
      return this.expense_total
    },
    getSelectedOptions() {
      let selectedOption = []
      this.expenses.forEach((data) => {
        if (data.description !== 'Others') {
          data.description && selectedOption.push(data.description)
        }
      })
      return selectedOption
    },
    progress() {
      let progress = (this.page / this.total_page * 100).toFixed(2);
      return parseInt(progress);
    }
  },
  methods: {
    setExpensesDropdown(index, expenseType) {
      this.expenses[index].description = expenseType.value

      if (expenseType.value !== 'Others') {
        this.options = this.expenseOption.filter((option) => !this.getSelectedOptions.includes(option.value))
      }
    },

    eachError(index, key, value = '') {
      let checkValue = false;

      (value !== '') ? checkValue = true
          : checkValue = this.expenses[index][key] !== value
      return (
          this.v$.expenses.$dirty && checkValue &&
          this.v$.expenses.$each.$response.$errors[index][key][0] !== undefined
      );
    },

    async expensesIndexApi() {
      await $api.get('/api/travel-claim/' + this.travelId + '/expense?page=' + this.page)
          .then(response => {
            this.total_page = response.data.meta.last_page;

            response.data.data.forEach((data) => {
              this.expenses.push(data)
            });

            if (this.page < this.total_page) {
              this.page++;
              setTimeout(() => {
                this.expensesIndexApi()
              }, 1000)
            } else {
              this.load = false;
            }
            this.options = this.expenseOption.filter((option) => !this.getSelectedOptions.includes(option.value))
          }).catch(e => {
            this.load = false;
            useModalStore().show(e.response.data.message)
          })
    },

    async addExpenseApi() {
      this.v$.$reset();
      useLoadButton().start();

      let formData = new FormData();
      this.expenses.forEach((data) => formData.append('expense[]', JSON.stringify(data)));

      await $api.post('/api/travel-claim/' + this.travelId + '/expense-add', formData)
          .then(response => {
            setTimeout(() => {
              useLoadButton().finish();
              this.$toast.add({severity: 'success', summary: 'Success', detail: 'Expenses added', life: 3000});
              this.expenses.push(response.data.data);
            }, 1000)
          }).catch(e => {
            useLoadButton().finish();
            useModalStore().show(e.response.data.message)
          })
    },

    async removeExpenseApi(index) {
      useLoadButton().start();

      await $api.delete('/api/travel-claim/' + this.travelId + '/expense-delete?id=' + this.expenses[index].id)
          .then(response => {
            setTimeout(() => {
              this.expenses.splice(index, 1)
              this.options = this.expenseOption.filter((option) => !this.getSelectedOptions.includes(option.value))
              useLoadButton().finish();
              this.$toast.add({severity: 'success', summary: 'Success', detail: response.data.message, life: 3000});
            }, 1000)
          }).catch(e => {
            useLoadButton().finish();
            useModalStore().show(e.response.data.message)
          })
    },

    async submit() {
      const validated = await this.v$.$validate()

      if (!validated) {
        return useModalStore().show('Please fill up all fields before submitting')
      }

      useModalStore().load()

      let formData = new FormData();
      this.expenses.forEach((data) => formData.append('expense[]', JSON.stringify(data)))

      await $api.post('/api/travel-claim/' + this.travelId + '/expense', formData)
          .then((res) => {

            this.$toast.add({
              severity: 'success',
              summary: 'Success',
              detail: 'Application submitted successfully',
              life: 3000
            })
            setTimeout(() => {
              useModalStore().finishLoad()
              window.location.href = '/e-form/' + localStorage.getItem('user_id') + '/travel/travel-claim'
            }, 1000)
          })
          .catch((err) => {
            useModalStore().finishLoad()
          });
    }
  },
}
</script>
