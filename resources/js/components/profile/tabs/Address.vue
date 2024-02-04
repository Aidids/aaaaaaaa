<template>
    <h5 class="my-2">Address Details</h5>
    <hr>
    <loader v-show="this.permLoad || this.currLoad" :large="false"/>
    <AddressInfoRow title="Permanent address" iconClass="bi bi-house color-primary" :details="permanent.details"
                    :city="permanent.city" :state="permanent.state" :zip="permanent.zip" :country="permanent.country"
                    :phone="permanent.phone">
        <button v-if="permanent.details == null"
                @click="openModal(permanent, 'Add permanent address', 'permanent-address')" class="button"
                data-bs-toggle="modal" data-bs-target="#adressModal">Add
        </button>
        <div v-else>
            <button @click="openModal(permanent, 'Edit permanent address', 'permanent-address')"
                    class="btn btn-outline-secondary me-2"
                    data-bs-toggle="modal" data-bs-target="#adressModal">Edit
            </button>
            <button @click="removeAddress('permanent-address')" class="btn btn-outline-danger"
                    data-bs-toggle="modal" data-bs-target="#delete-address">Remove
            </button>
        </div>
    </AddressInfoRow>
    <AddressInfoRow title="Current address" iconClass="bi bi-geo-alt color-primary" :details="current.details"
                    :city="current.city" :state="current.state" :zip="current.zip" :country="current.country"
                    :phone="current.phone">
        <button v-if="current.details == null"
                @click="openModal(current, 'Add current address', 'current-address')" class="button"
                data-bs-toggle="modal" data-bs-target="#adressModal">Add
        </button>
        <div v-else>
            <button @click="openModal(current, 'Edit current address', 'current-address')"
                    class="btn btn-outline-secondary"
                    data-bs-toggle="modal" data-bs-target="#adressModal">Edit
            </button>
            <button @click="removeAddress('current-address')" class="btn btn-outline-danger"
                    data-bs-toggle="modal" data-bs-target="#delete-address">Remove
            </button>
        </div>
    </AddressInfoRow>
    <AddressModal :user_id="this.user_id" :title="this.titleModal"
                  :address="this.addressModal" :type="this.type"/>
    <DeleteAddress :user_id="this.user_id" :type="this.type"/>
    <system-msg :message="this.message"/>
</template>

<script>
import AddressModal from '../modals/AddressModal.vue';
import DeleteAddress from '../modals/DeleteAddress.vue';
import $ from "jquery";
import $api from "../../api";
import AddressInfoRow from "../../elements/AddressInfoRow.vue";

export default {
    props: ['user_id'],
    components: {
        AddressInfoRow,
        AddressModal,
        DeleteAddress
    },
    data() {
        return {
            id: null,
            titleModal: '',
            permanent: {},
            current: {},
            addressModal: {},
            type: '',
            message: '',
            permLoad: true,
            currLoad: true,
            emptyAddress: {
                details: null,
                city: null,
                state: null,
                zip: null,
                country: null,
                phone: null,
            }
        }
    },
    created() {
        this.getPermAddress();
        this.getCurrAddress();
    },
    methods: {
        openModal(data, title, type) {
            this.addressModal = data;
            this.titleModal = title;
            this.type = type;
        },
        removeAddress(type) {
            this.type = type;
        },
        updated(message) {
            this.message = message;
            $('#modal').fadeIn();
        },
        fetchApi(type) {
            (type == 'permanent-address') ? this.getPermAddress() : this.getCurrAddress();
        },
        async getPermAddress() {
            await $api.get('/api/profile-settings/' + this.user_id + '/permanent-address')
                .then(response => {
                    this.permanent = response.data.data;
                    this.permLoad = false;
                });
        },
        async getCurrAddress() {
            await $api.get('/api/profile-settings/' + this.user_id + '/current-address')
                .then(response => {
                    this.current = response.data.data;
                    this.currLoad = false;
                });
        }
    },
}
</script>

<style>
.bi.default {
    font-size: 1.25rem;
}
</style>
