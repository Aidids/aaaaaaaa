<template>
    <div class="d-flex flex-xl-row flex-wrap-reverse">
        <div class="col-xl-6 me-xl-3">
            <h4 class="mb-3">Offshore Leave Redemption</h4>
            <div class="d-flex flex-wrap card mb-4">
                <loader v-show="loadApi"/>
                <div class="col-12 d-flex flex-wrap flex-md-nowrap flex-row px-0 mb-2">
                    <div class="form-group me-md-1 w-100">
                        <label class="mb-2">Start date</label>
                        <Datepicker
                            v-model="form.start_date"
                            :enable-time-picker="false" ref="startDate"
                            placeholder="Please select start date"
                            :state="null" :max-date="form.end_date" auto-apply
                        >
                        </Datepicker>
                    </div>
                    <div class="form-group w-100">
                        <label class="mb-2">End date</label>
                        <Datepicker
                            v-model="form.end_date"
                            :enable-time-picker="false" ref="startDate"
                            placeholder="Please select end date"
                            :state="null" :min-date="form.start_date" auto-apply
                        >
                        </Datepicker>
                    </div>
                </div>
                <div class="col-12 d-flex flex-wrap flex-md-nowrap flex-row px-0">
                    <Supervisor wrapper-class="me-md-1 w-100" v-model="form.first_approver"/>
                    <Hod wrapper-class="w-100" v-model="form.second_approver"/>
                </div>
                <div class="my-3">
                    <label class="form-label">Upload attachment <small class="text-danger">(Max: 10 files)</small></label>
                    <FileUpload
                        ref="file"
                        name="files[]"
                        :multiple="true"
                        :show-upload-button="false"
                        accept="image/*, application/pdf"
                        :maxFileSize="5000000"
                        :preview-width="150"
                        @change="onFileChange"
                        :file-limit="10"
                    />
                </div>
                <div class="w-100 text-end mt-3">
                    <button v-show="this.form.files.length <= 10" v-if="disableForm" class="btn btn-secondary" @click="disabledMessage">Apply</button>
                    <button v-show="this.form.files.length <= 10" v-else class="btn btn-success" @click="postForm">Apply</button>
                </div>
            </div>
        </div>

        <div class="col-xl-5 ps-xl-2 mb-3 mb-xl-0">
            <h4 class="mb-3">How to apply ?</h4>
            <h6><i class="bi bi-1-circle me-2"></i>Complete the form with the necessary details.</h6>
            <h6><i class="bi bi-2-circle me-2"></i>Attach the required attachment as <strong>evidence</strong> of your offshore trip.</h6>
            <h6><i class="bi bi-3-circle me-2"></i>Once your superiors approve your request, the HR department will be notified.</h6>
            <h6><i class="bi bi-4-circle me-2"></i>The HR team will verify your application and send you an email upon approval.</h6>
            <h4 class="my-3">Information</h4>
            <h6><i class="bi bi-info-circle me-2"></i>For every 7 days spent offshore, you will earn 1 day of leave.</h6>
            <h6><i class="bi bi-info-circle me-2"></i>If you spend 30 days offshore, you will earn 4 days of leave.</h6>
            <h6><i class="bi bi-info-circle me-2"></i>Offshore leave earned will <strong>expire</strong> at the end of the year.</h6>
        </div>
    </div>
    <MsgModal
        v-model="msgModal.show"
        :message="msgModal.message"
        @cancel="cancel"
    />
</template>

<script>
import $api from "../../api";
import Hod from "../../dropdown/Hod.vue";
import Supervisor from "../../dropdown/Supervisor.vue";
import MsgModal from "../../elements/MsgModal.vue";

export default {
    components: {Supervisor, Hod, MsgModal},

    data() {
        return {
            form: {
                files: [],
            },
            disableForm: true,
            supervisors: [],
            headOfDepartments: [],
            msgModal: {
                show: false,
                message: ''
            },
            loadApi: false,
        }
    },

    methods: {
        onFileChange() {
            this.form.files = this.$refs.file.files;
        },

        convertDate: function(date) {
            let dateTime = new Date(date);
            let year = dateTime.getFullYear();
            let month = (dateTime.getMonth() + 1).toString().padStart(2, '0');
            let day = dateTime.getDate().toString().padStart(2, '0');

            return `${year}-${month}-${day}`;
        },

        disabledMessage() {
          this.msgModal.show = true;
          this.msgModal.message = 'Please fill in all missing fields to submit offshore leave request.';
        },

        async postForm() {
            this.loadApi = true;

            let formData = new FormData();

            formData.append('leave_balance_id', null);
            formData.append('duration', null);
            formData.append('start_date', this.convertDate(this.form.start_date));
            formData.append('end_date', this.convertDate(this.form.end_date));

            if (this.form.first_approver) {
                formData.append('first_approver_id', this.form.first_approver.id);
                formData.append('first_approver_status', 'pending');
            }

            if (this.form.second_approver) {
                formData.append('second_approver_id', this.form.second_approver.id);
                formData.append('second_approver_status', 'pending');
            }

            // Loop over array of files and append each file individually
            for (let i = 0; i < this.form.files.length; i++) {
                formData.append('files[]', this.form.files[i]);
            }

            const config = { headers: { 'Content-Type': 'multipart/form-data' } };
            await $api.post('/api/offshore-leave/' + this.$parent.user_id, formData, config)
                .then(response => {
                    this.$parent.getOffshoreLeaveRequest();
                    this.form = {
                        files: [],
                    };
                    this.$refs.file.files = [];
                    this.msgModal.show = true;
                    this.msgModal.message = 'Success, an email has been sent to your assigned approvers.';
                    this.loadApi= false;
                }).catch(error => {

                });
        },

        cancel(close) {
            close()
        }
    },

    watch: {
        form: {
            deep: true,
            handler (n,o) {
                this.disableForm =
                    n.start_date === '' || n.start_date == null
                    || n.end_date === '' || n.end_date == null
                    || (n.first_approver === '' || n.first_approver == null) && (n.second_approver === '' || n.second_approver == null)
                    || n.files.length === 0
            }
        }
    }
}
</script>
