<template>
    <div class="h-100 d-flex flex-column">

        <!--header-->
        <div class="d-flex align-items-center  gap-2 mt-2">
            <h5>Emergency Contacts</h5>
            <button v-show="emergencyData.length < 2" class="button" @click="addContact()">Add Contact</button>
        </div>
        <hr>


        <div class="h-100">
            <loader v-if="this.emergencyLoad" :large="false" class="h-100 d-flex"/>

            <div v-else-if="emergencyData.length === 0"
                 class="h-100 d-flex flex-column justify-content-center align-items-center">
                <i class="bi bi-telephone-x text-danger" style="font-size: 5rem;"></i>
                <h6 class="text-danger">No Emergency Contact Added Yet</h6>
            </div>

            <div v-else>
                <div v-for="contact in emergencyData" :key="contact.id" class="d-xl-flex align-items-start mb-2">
                    <div class="col-xl-3 my-2 d-flex flex-row align-items-center">
                        <i class="bi bi-person color-primary me-2" style="font-size: 1.75rem;"></i>
                        <span>{{ contact.name }}</span>
                    </div>

                    <div class="col-xl-7 my-2">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-people color-primary me-2" style="font-size: 1.25rem;"></i>
                            <p> {{ contact.relationship }} </p>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-telephone color-primary me-2" style="font-size: 1.25rem;"></i>
                            <p> {{ contact.phone }} </p>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-envelope color-primary me-2" style="font-size: 1.25rem;"></i>
                            <p> {{ contact.email ?? 'Email not set' }} </p>
                        </div>
                        <div class="d-flex align-items-start mt-1">
                            <i class="bi bi-geo-alt fa-lg color-primary me-2" style="font-size: 1.25rem;"></i>
                            <p>
                                {{ contact.address }},
                                <br>
                                {{ contact.city }},
                                {{ contact.state }},
                                <br>
                                {{ contact.zip }},
                                {{ contact.country }}
                            </p>
                        </div>
                    </div>

                    <div class="col-xl-2 my-2">
                        <button @click="updateContact(contact)" class="btn btn-outline-secondary me-2">Edit</button>
                        <button @click="deleteContact(contact.id)" class="btn btn-outline-danger">Remove</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <ContactModal :contact="this.contactModel" v-model="showModal" @confirm="confirm" @cancel="cancel"/>
    <msg-modal :message="this.message" v-model="msgModal" @cancel="cancel"/>
    <error-modal :errors="this.error" v-model="errorModal"/>
</template>

<script>
import ContactModal from "../modals/ContactModal.vue";
import $api from "../../api";
import {useVuelidate} from '@vuelidate/core';
import {required, email} from '@vuelidate/validators';

export default {
    setup() {
        return {v$: useVuelidate()}
    },
    props: ['user_id'],
    components: {
        ContactModal
    },
    data() {
        return {
            emergencyLoad: true,
            emergencyData: [],
            showModal: false,
            msgModal: false,
            errorModal: false,
            contactModel: {},
            message: '',
            error: {},
        }
    },
    validations() {
        return {
            contactModel: {
                name: {required},
                relationship: {required},
                phone: {required},
                email: {email},
                address: {required},
                city: {required},
                state: {required},
                zip: {required},
                country: {required},
            }
        }
    },
    methods: {
        async getEmergencyApi() {
            await $api.get('/api/profile-settings/' + this.user_id + '/emergency')
                .then(response => {
                    this.emergencyData = response.data.data;
                    this.emergencyLoad = false;
                });
        },
        addContact() {
            this.contactModel = {
                name: null,
                relationship: null,
                phone: null,
                email: null,
                address: null,
                city: null,
                state: null,
                zip: null,
                country: null,
            };

            this.showModal = true;
        },
        updateContact(data) {
            this.contactModel = data;
            this.showModal = true;
        },
        async confirm() {
            this.v$.$validate();

            if (!this.v$.$error) {
                await $api.post('/api/profile-settings/' + this.user_id + '/emergency', this.contactModel)
                    .then(response => {
                        this.message = response.data.message;
                        this.getEmergencyApi();
                        this.showModal = false;
                        this.msgModal = true;
                    }).catch(error => {
                        this.message = error;
                        this.msgModal = true;
                    });
            } else {
                this.error = this.v$.$errors;
                this.errorModal = true;
            }

        },
        async deleteContact(id) {
            await $api.delete('/api/profile-settings/' + this.user_id + '/emergency/' + id)
                .then(response => {
                    this.message = response.data.message;
                    this.getEmergencyApi();
                    this.showModal = false;
                    this.msgModal = true;
                }).catch(error => {
                    this.message = error;
                    this.msgModal = true;
                });
        },
        cancel(close) {
            close()
        }
    },
    created() {
        this.getEmergencyApi();
    }
}
</script>

