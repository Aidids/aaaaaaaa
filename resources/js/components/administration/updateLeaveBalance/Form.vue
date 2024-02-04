<template>
    <div class="col-md-6 px-0">
        <h4 class="mb-3">Update Leave Balance</h4>
        <div class="bg-white shadow p-2 mb-4">
            <LeaveType wrapper-class="m-2" v-model="form.leave_type"/>
            <Users wrapper-class="m-2" v-model="form.user" />
            <div class="m-2">
                <label class="form-label">Remark</label>
                <textarea v-model="form.remark" class="form-control"></textarea>
            </div>
            <div class="d-md-flex justify-content-between align-items-end">
                <div class="d-flex flex-column m-2">
                    <label class="form-label">Add Balance</label>
                    <Dropdown
                        class="w-full"
                        v-model="form.selectedBalance"
                        :options="balanceOption"
                        optionLabel="value"
                        placeholder="Select Value"
                    />
                </div>
                <button v-if="disabled" class="btn btn-secondary h-25 m-2" disabled>Update</button>
                <button v-else class="btn btn-success h-25 m-2" @click="openModal">Update</button>
            </div>
        </div>
    </div>
    <ConfirmationModal v-model="showConfirmationModal" :form="this.form"/>
</template>

<script>
import Users from "../../dropdown/Users.vue";
import LeaveType from "../../dropdown/LeaveType.vue";
import ConfirmationModal from "./ConfirmationModal.vue";
import balanceOption from "../../../mixins/balanceOption";

export default {
    components: {Users, LeaveType, ConfirmationModal},

    mixins: [balanceOption],

    data() {
        return {
            form: {},
            disabled: true,
            showConfirmationModal: false,
        }
    },

    methods: {
        openModal() {
            this.showConfirmationModal = true;
        },

        // to be used inside confirmation modal child component
        // DO NOT REMOVE
        resetForm() {
            this.form = {};
        }
    },

    watch: {
        form: {
            deep: true,
            handler (n,o) {
                this.disabled =
                    n.leave_type == null
                    || n.user == null
                    || n.selectedBalance == null
            }
        }
    }
}
</script>
