<template>
    <vue-final-modal :click-to-close="false" v-slot="{ params, close }" v-bind="$attrs" classes="modal-container" content-class="modal-content">
        <div class="modal-body text-center" style="overflow: auto; width: 300px;">
            <p>
                You are about to update
                <br>
                <strong>{{ form && form.user ? form.user.name : 'User not selected' }}</strong>
                <br>
                annual leave with the value
                <br>
                <strong> {{ form && form.selectedBalance ? form.selectedBalance.value : 'Not selected' }} </strong> day(s)
            </p>
            <div class="d-md-flex justify-content-center">
                <button class="btn btn-primary mx-1" @click="addLeaveAddOn(close)">
                    Confirm
                </button>
                <button class="btn btn-outline-secondary mx-1" @click="closeModal(close)">
                    Cancel
                </button>
            </div>
        </div>

    </vue-final-modal>
</template>

<script>
import $api from "../../api";
import {mapActions} from "pinia";
import {useLeaveAddOnStore} from "../../../stores/getLeaveAddOnHistory";

export default {
    props: {
        form: {
            type: Object,
            default: () => {},
        },
    },

    data() {
        return {
            user_id: parseInt(localStorage.getItem('user_id')),
        }
    },

    methods: {
        async addLeaveAddOn(close) {
            let formData = new FormData;

            formData.append('leave_type_id', this.form.leave_type.id);
            formData.append('user_id', this.form.user.id);
            formData.append('pic_id', this.user_id);
            formData.append('added_qty', this.form.selectedBalance.value);
            formData.append('remark', this.form.remark);

            await $api.post('/api/administration/update-annual-leave', formData)
                .then(response => {
                    this.$parent.resetForm();
                    useLeaveAddOnStore().getLeaveAddOnHistory();
                })
                .finally(() => {
                    close();
                });

        },

        closeModal(close) {
            this.$parent.resetForm();
            close();
        },
    },

    computed: {
        ...mapActions(useLeaveAddOnStore, ['getLeaveAddOnHistory']),
    }
}
</script>
