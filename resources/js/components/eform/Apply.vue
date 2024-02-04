<template>
  <h4 class="mb-0">Apply E-Form</h4>
  <small class="text-secondary">Select the dropdown below to apply for e-form</small>
  <div class="form-control py-3 mt-3">
    <Dropdown
        :model-value="selectedForm"
        :options="eFormList"
        optionLabel="label"
        placeholder="Choose E-form"
        class="w-100"
        :required="true"
        @update:modelValue="selectedForm = $event"/>
  </div>
  <div v-if="selectedForm.value === 1">
    <travel-form/>
  </div>
  <div v-if="selectedForm.value === 2">
    <TEForm/>
  </div>
</template>

<script>


import TEForm from "./expenses/TEForm.vue";

export default {

  components: {TEForm},
  data() {
    return {
      selectedForm: {},
      eFormList: [
        {
          value: 1,
          label: 'Travel Authorization Form'
        },
        {
          value: 2,
          label: 'Travel Claim Form'
        }
      ]
    }
  },

}
</script>
