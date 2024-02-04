<template>
    <h5 v-if="title !== ''" class="mt-4 mb-2" :class="{'text-disabled' : typeof dateExist === 'undefined'}">{{title}}</h5>
    <div class="d-flex flex-column form-control pb-3">
        <div class="d-md-flex w-100">
          <Supervisor
              :is-disabled="typeof dateExist === 'undefined'"
              wrapperClass="w-100 mt-2 me-md-2"
              :model-value="supervisor"
              :error="error"
              @update:modelValue="$emit('update:supervisor', supervisor = $event)"/>
          <Hod :is-disabled="typeof dateExist === 'undefined'"
               wrapperClass="w-100 mt-2"
               :model-value="hod"
               :error="error"
               @update:modelValue="$emit('update:hod', hod = $event)"/>
        </div>
      <FormError :error="error" error-text="You must choose at least 1 approver"/>
    </div>
    <small :class="[(typeof dateExist === 'undefined') ? 'text-disabled' : 'text-secondary']">{{description}}</small>
</template>

<script>
import Supervisor from "../dropdown/Supervisor.vue"
import Hod from "../dropdown/Hod.vue";
import FormError from "../elements/FormError.vue";

export default {
    components: {FormError, Supervisor, Hod},

    emits: ['update:supervisor', 'update:hod'],

    props: {
        supervisor: {
            type: Object,
            default: () => {},
        },
        hod: {
            type: Object,
            default: () => {},
        },
        dateExist: {
            type: String,
            default: undefined
        },
        wrapperClass: {
            type: String,
            default: 'mt-4 mb-2'
        },
        title: {
            type: String,
            default: 'Assign Approvers'
        },
        description: {
            type: String,
            default: 'Note: If you have 2 approvers needed for leave request, please assign the respective approvers'
        },
        error: {
          type: Boolean,
          default: false,
        }
    },
}
</script>
