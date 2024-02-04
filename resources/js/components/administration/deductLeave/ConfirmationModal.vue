<template>
    <vue-final-modal :click-to-close="false" v-slot="{ params, close }" v-bind="$attrs" classes="modal-container" content-class="modal-content">
        <div class="modal-body text-center" style="overflow: auto; width: 300px;">
            <loader v-if="loader"/>
            <p>
                You are about to update
                <br>
                <strong v-if="form.deduct_all">Everyone's</strong>
                <strong v-else>{{ form && form.user ? form.user.name : 'User not selected' }}</strong>
                <br>
                annual leave with the value
                <br>
                <strong> {{ form.duration ? form.duration.value : 'Not selected' }} </strong> day(s)
            </p>
            <div class="d-md-flex justify-content-center mt-3">
                <button class="btn btn-success mx-1" @click="addLeaveAddOn(close)">
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

export default {
    components: {},

    props: {
        form: {
            type: Object,
            default: () => {},
        },
    },

    data() {
        return {
            user_id: parseInt(localStorage.getItem('user_id')),
            loader: false,
        }
    },

    methods: {
        async addLeaveAddOn(close) {
            this.loader = true;

            let formData = new FormData;

            if (this.form.deduct_all) {
                formData.append('deduct_all', 1)
            } else {
                formData.append('user_id', this.form.user.id);
            }
            formData.append('hr_startDate', this.form.start_date);
            formData.append('hr_endDate', this.form.end_date);
            formData.append('duration', this.form.duration.value);
            formData.append('remark', this.form.remark);

            await $api.post('/api/administration/deduct-leave/' + this.user_id, formData)
                .then(response => {
                    this.loader = false;
                    this.$parent.callApi();
                    close();
                })
                .finally(() => {
                    this.loader = false;
                    close();
                });

        },

        closeModal(close) {
            this.$parent.resetForm();
            close();
        },
    },
}
</script>
