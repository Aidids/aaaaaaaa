<template>
    <h4 class="mb-0">Redeem Leave</h4>
    <small class="text-secondary">Select the leave you would like to redeem and fill in the form below to update your
        leave balance</small>
    <h5 class="text-secondary mt-4 mb-2">Leave Type</h5>
    <div class="form-control py-3 mb-4">
        <Dropdown
            :model-value="form.leave_type"
            :options="leaveTypes"
            optionLabel="label"
            option-value="value"
            placeholder="Select Leave Type"
            class="w-100"
            @update:modelValue="selectLeaveType($event)"/>
        <FormError :error="v$.form.leave_type.$error" error-text="Leave Type is required"/>
    </div>

    <FormDate
        title="Working Day Information"
        description="Select which working day you would like to apply for replacement leave"
        :start-date="form.start_date"
        :end-date="form.end_date"
        :error-start-date="v$.form.start_date.$error"
        :error-end-date="v$.form.end_date.$error"
        @update:start-date="(v) => form.start_date = v"
        @update:end-date="(v) => form.end_date = v"
    />
    <FormApprovers
        date-exist=""
        :supervisor="form.first_approver"
        :hod="form.second_approver"
        :error="v$.form.first_approver.$error || v$.form.second_approver.$error"
        @update:supervisor="form.first_approver = $event"
        @update:hod="form.second_approver = $event"
    />
    <h5 class="mt-4 mb-2">Attachment</h5>
    <div class="form-control py-3">
        <FormMultipleAttachment
            :approvers-exists="true"
            @update:fileChange="form.files = $event"
            description="Attachment is required as evidence for replacement leave eligibility"
        />
    </div>
    <FormError :error="v$.form.files.$error" error-text="Attachments is required and cannot be more than 10"/>

    <FormRemarks
        :approvers-exists="form.first_approver !== undefined || form.second_approver !== undefined"
        v-model="form.remark"
    />
    <button class="btn btn-success mt-4" @click="submit">Submit</button>
    <Modal v-model="modal.show" :modal="modal" :is-close="modal.isClose" :has-confirm="modal.hasConfirm"></Modal>
</template>

<script>
import FormDate from "../../form/FormDate.vue";
import FormApprovers from "../../form/FormApprovers.vue";
import FormMultipleAttachment from "../../elements/attachments/FormMultipleAttachment.vue";
import FormRemarks from "../../form/FormRemarks.vue";
import Modal from "../../elements/Modal.vue";
import $api from "../../api";
import {mapState} from "pinia";
import {useModalStore} from "../../../stores/modal";
import {useVuelidate} from "@vuelidate/core";
import {required} from "@vuelidate/validators";
import FormError from "../../elements/FormError.vue";

export default {
    components: {FormError, FormDate, FormApprovers, FormMultipleAttachment, FormRemarks, Modal},
    setup() {
        return {v$: useVuelidate()}
    },
    created() {
        useModalStore().init()
    },
    data() {
        return {
            form: {
                files: []
            },
            leaveTypes: [
                {
                    value: 'offshore-leave',
                    label: 'Offshore Leave',
                },
                {
                    value: 'replacement-leave',
                    label: 'Replacement Leave'
                }
            ],
            postApiUrl: '',
        }
    },
    validations() {
        return {
            form: {
                leave_type: {required},
                start_date: {required},
                end_date: {required},
                first_approver: {
                    required: function () {
                        return this.form.first_approver || this.form.second_approver
                    }
                },
                second_approver: {
                    required: function () {
                        return this.form.first_approver || this.form.second_approver
                    }
                },
                files: {
                    required: function () {
                        return this.form.files.length > 0 && this.form.files.length <= 10
                    }
                }
            }
        }
    },
    methods: {
        selectLeaveType(leaveType) {
            this.form.leave_type = leaveType
            this.postApiUrl = leaveType === 'replacement-leave' ? '/api/redeem-replacement-leave/apply' : '/api/redeem-offshore-leave/apply'
        },

        async submit() {
            const validated = await this.v$.$validate()

            if (!validated) {
                return useModalStore().show('Please fill up all fields before submitting');
            }

            useModalStore().load()

            let formData = new FormData;

            formData.append('start_date', this.form.start_date);
            formData.append('end_date', this.form.end_date);

            if (this.form.first_approver) {
                formData.append('first_approver_id', this.form.first_approver.id);
                formData.append('first_approver_status', 'pending');
            }

            if (this.form.second_approver) {
                formData.append('second_approver_id', this.form.second_approver.id);
                formData.append('second_approver_status', 'pending');
            }

            // Loop over the files array and append each file individually
            for (let i = 0; i < this.form.files.length; i++) {
                formData.append('files[]', this.form.files[i]);
            }

            (this.form.remark) && formData.append('remark', this.form.remark);

            const config = {headers: {'Content-Type': 'multipart/form-data'}};
            await $api.post(this.postApiUrl, formData, config)
                .then(response => {
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: 'Application submitted successfully',
                        life: 3000
                    })
                    setTimeout(() => {
                        useModalStore().finishLoad()
                        window.location.href = '/redeem-leave/history/' + this.form.leave_type
                    }, 2000)
                }).catch((error) => {
                    useModalStore().show(error.response.data.message)
                })
        },
    },
    computed: {
        ...mapState(useModalStore, ['modal']),
    },
}
</script>
