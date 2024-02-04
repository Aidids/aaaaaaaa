<template>
<div class="modal fade" id="adressModal" tabindex="-1" role="dialog" aria-labelledby="adressModalLabel" aria-modal="true">
    <div class="modal-body custom-scrollbar undefined">
        <div role="document" class="modal-dialog modal-dialog-top modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-capitalize">{{ this.title }}</h5>
                    <button @click="reset" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body custom-scrollbar undefined">
                    <CustomInput v-model="this.address.details"
                        label="Address Details" class="w-100 mb-4" maxLength="250"
                        type="textarea" placeholder="Add address details here"
                        :required="true"
                        />
                    <div class="row">
                        <CustomInput v-model="this.address.city"
                        label="City" class="col-md-6" maxLength="250"
                        type="text" placeholder="Enter city"
                        :required="true"
                        />

                        <CustomInput v-model="this.address.state"
                        label="State" class="col-md-6" maxLength="250"
                        type="text" placeholder="Enter state"
                        :required="true"
                        />

                        <CustomInput v-model="this.address.zip"
                        label="zip" class="col-md-6" maxLength="250"
                        type="text" placeholder="Enter zip"
                        :required="true"
                        />

                        <CustomInput v-model="this.address.country"
                        label="country" class="col-md-6" maxLength="250"
                        type="text" placeholder="Enter country"
                        :required="true"
                        />

                        <CustomInput v-model="this.address.phone"
                        label="phone" class="col-md-6" maxLength="250"
                        type="text" placeholder="Enter phone"
                        :required="false"
                        />
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="reset" type="button" data-bs-dismiss="modal" class="btn btn-secondary mr-2">Cancel</button>
                    <button :class="(this.disabled) ? 'btn-secondary' : 'btn-success'" @click="postAddress" :data-bs-dismiss="(this.disabled) ? '' : 'modal'" type="submit" class="btn text-center">Save</button>
                    <p v-if="this.error" class="w-100 text-end text-danger mt-3">Inputs with asterisk (*) is required.</p>
               </div>
             </div>
        </div>
    </div>
</div>
</template>

<script>
import $api from '../../api.js';
import CustomInput from '../../elements/CustomInput.vue';
export default {
    props: ['user_id', 'title', 'address', 'type'],
    data() {
        return {
            disabled: true,
            error: false,
        }
    },
    components: {
        CustomInput
    },
    methods: {
        async postAddress() {
            if (this.disabled) {
                this.error = true;
            } else {
                this.error = false;
                let body = {
                    module: this.address.module,
                    details: this.address.details,
                    city: this.address.city,
                    state: this.address.state,
                    zip: this.address.zip,
                    country: this.address.country,
                    phone: this.address.phone,
                };
                await $api.post('/api/profile-settings/' + this.user_id + '/' + this.type,
                    body)
                .then(response => {
                    this.$parent.updated(response.data.message);
                }
                ).catch(error => {
                    this.$parent.updated(error);
                });
            }
        },
        reset(){
            this.error = false;
            this.$parent.fetchApi(this.type);
        },
    },
    watch: {
        address: {
            deep: true,
            handler (n, o) {
                this.disabled = n.details == null || n.details === '' ||
                    n.city == null || n.city === '' ||
                    n.state == null || n.state === '' ||
                    n.zip == null || n.zip === '' ||
                    n.country == null || n.country === '';
            }
        }
    }
}


</script>
