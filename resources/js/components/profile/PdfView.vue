<template>
  <div class="mt-4 d-none d-print-block">
    <h5 class="d-inline-block me-2">Personal Information</h5>
    <hr>
    <DoubleFormRow label1="I.C No." label2="Passport No." :is-print="true">
      <template #input1>
        <input type="text" class="form-control" disabled v-model="user.ic_no">
      </template>
      <template #input2>
        <input type="text" class="form-control" disabled v-model="user.passport_no">
      </template>
    </DoubleFormRow>
    <DoubleFormRow label1="Date of birth" label2="Place of birth" :is-print="true">
      <template #input1>
        <input type="text" class="form-control" disabled v-model="user.date_of_birth">
      </template>
      <template #input2>
        <input type="text" class="form-control" disabled v-model="user.place_of_birth">
      </template>
    </DoubleFormRow>
    <DoubleFormRow label1="Race" label2="Religion" :is-print="true">
      <template #input1>
        <input type="text" class="form-control" disabled v-model="user.race">
      </template>
      <template #input2>
        <input type="text" class="form-control" disabled v-model="user.religion">
      </template>
    </DoubleFormRow>
    <DoubleFormRow label1="Nationality" label2="Contact No." :is-print="true">
      <template #input1>
        <input type="text" class="form-control" disabled v-model="user.nationality">
      </template>
      <template #input2>
        <input type="text" class="form-control" disabled v-model="user.phone_no">
      </template>
    </DoubleFormRow>
    <h5 class="d-inline-block me-2">Payroll Information</h5>
    <hr>
    <DoubleFormRow label1="E.P.F No." label2="Income Tax No." :is-print="true">
      <template #input1>
        <input type="text" class="form-control" disabled v-model="user.epf_no">
      </template>
      <template #input2>
        <input type="text" class="form-control" disabled v-model="user.income_tax_no">
      </template>
    </DoubleFormRow>
    <DoubleFormRow label1="Bank Name" label2="Bank account type" :is-print="true">
      <template #input1>
        <input type="text" class="form-control" disabled v-model="user.bank_name">
      </template>
      <template #input2>
        <Dropdown
            class="w-100"
            v-model="user.bank_acc_type"
            :options="acc_type"
            optionLabel="label"
            option-value="value"
            :disabled="true"
        />
      </template>
    </DoubleFormRow>
    <DoubleFormRow label1="Bank Account No." label2="Socso No." :is-print="true">
      <template #input1>
        <input type="text" class="form-control" disabled v-model="user.bank_acc_no">
      </template>
      <template #input2>
        <input type="text" class="form-control" disabled v-model="user.socso_no">
      </template>
    </DoubleFormRow>
    <span v-if="user.educations">
      <h5 class="d-inline-block me-2">Education Information</h5>
      <hr>
      <TableMain>
      <thead>
      <tr>
        <th>Qualifications</th>
        <th>Year Graduate</th>
      </tr>
      </thead>
      <tbody>
        <tr v-if="user.educations.length === 0">
          <td class="text-center py-3" colspan="2">User has not added their education information</td>
        </tr>
        <tr v-for="education in user.educations">
          <td>{{education.qualification}}</td>
          <td>{{education.year_passed}}</td>
        </tr>
      </tbody>
    </TableMain>
    </span>
    <h5 class="d-inline-block me-2 mt-2">Family Information</h5>
    <hr>
    <DoubleFormRow label1="Martial Status" label2="Spouse's name" :is-print="true">
      <template #input1>
        <input class="form-control text-capitalize" v-model="user.marital_status" disabled>
      </template>
      <template #input2>
        <input class="form-control text-capitalize" v-model="user.spouse_name" disabled>
      </template>
    </DoubleFormRow>
    <DoubleFormRow label1="Spouse's I.C No" label2="Spouse's working status" :is-print="true">
      <template #input1>
        <input class="form-control text-capitalize" v-model="user.spouse_ic_no" disabled>
      </template>
      <template #input2>
        <input class="form-control text-capitalize" v-model="user.spouse_working" disabled>
      </template>
    </DoubleFormRow>
    <span v-if="user.children">
      <h5 class="d-inline-block me-2 mt-2">Children Information</h5>
      <hr>
      <TableMain>
        <thead>
        <tr>
          <th>Name</th>
          <th>IC No.</th>
        </tr>
        </thead>
        <tbody>
        <tr v-if="user.children.length === 0">
          <td class="text-center py-3" colspan="2">User has no children</td>
        </tr>
        <tr v-for="children in user.children">
          <td>{{children.child_name}}</td>
          <td>{{children.child_ic}}</td>
        </tr>
        </tbody>
      </TableMain>
    </span>
    <div class="py-5 text-center">End of page</div>
  </div>
</template>

<script>
import DoubleFormRow from "../elements/DoubleFormRow.vue";
import TableMain from "../elements/TableMain.vue";
import banks from "../../mixins/banks";
import {mapState} from "pinia";
import {usePersonalInfo} from "../../stores/getPersonalInfo";

export default {
  components: {TableMain,DoubleFormRow},
  mixins: [banks],
  computed: {
    ...mapState(usePersonalInfo, ['user', 'apiCall'])
  }
}
</script>