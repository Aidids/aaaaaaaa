<template>
    <vue-final-modal v-slot="{ params, close }" v-bind="$attrs" classes="modal-container" content-class="modal-content">
        <div>
            <div class="modal-header">
                <h5 class="modal-title">
                    Add Emergency Contacts
                </h5>
                <button @click="close" type="button" class="btn-close"></button>
            </div>
            <div class="modal-body">
                <hr>
                <div class="row">
                    <CustomInput v-model="this.contact.name"
                    label="Name" class="col-md-6" maxLength="250"
                     type="text" placeholder="Contact Name"
                     :required="true"
                    />

                    <CustomInput v-model="this.contact.relationship"
                     label="Relationship" class="col-md-6" maxLength="250"
                     type="text" placeholder="Contact relationship"
                     :required="true"
                    />

                    <CustomInput v-model="this.contact.phone"
                     label="phone" class="col-md-6" maxLength="250"
                     type="text" placeholder="Enter phone"
                     :required="true"
                    />

                    <CustomInput v-model="this.contact.email"
                     label="email" class="col-md-6" maxLength="250"
                     type="email" placeholder="Enter email"
                     :required="false"
                    />
                </div>

                <CustomInput v-model="this.contact.address"
                 label="Address" class="w-100" maxLength="250"
                 type="textarea" placeholder="Enter address details"
                 :required="true"
                />

                <div class="row">
                    <CustomInput v-model="this.contact.city"
                     label="city" class="col-md-6" maxLength="250"
                     type="text" placeholder="Enter address details"
                     :required="true"
                    />

                    <CustomInput v-model="this.contact.state"
                     label="State" class="col-md-6" maxLength="250"
                     type="text" placeholder="Enter state"
                     :required="true"
                    />

                    <CustomInput v-model="this.contact.zip"
                     label="zip" class="col-md-6" maxLength="250"
                     type="text" placeholder="Enter zip"
                     :required="true"
                    />

                    <CustomInput v-model="this.contact.country"
                     label="country" class="col-md-6" maxLength="250"
                     type="text" placeholder="Enter country"
                     :required="true"
                    />
                </div>
            </div>

            <div class="modal-footer mt-4">
                <button @click="$emit('cancel', close)" type="button" class="btn btn-secondary me-2">Cancel</button>
                <button @click="$emit('confirm', close)" type="submit" class="btn text-center btn-success">Save</button>
            </div>

        </div>
    </vue-final-modal>
</template>

<script>
import CustomInput from '../../elements/CustomInput.vue';
export default {
    props: ['contact'],
    components: {
      CustomInput
    },
    name: 'CustomModal',
    inheritAttrs: false,
    emits: ['confirm', 'cancel'],

}
</script>
