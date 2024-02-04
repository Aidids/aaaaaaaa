<template>
    <h4 class="mb-3">Dayang Enterprise Leave Description</h4>
    <TableMain>
        <thead>
        <tr>
            <th class="text-start">Name</th>
            <th class="text-start">Description</th>
            <th class="text-center">Gender</th>
            <th class="text-center">Amount</th>
            <th class="text-center">Entitlement</th>
            <th class="text-center">Pro Rated</th>
            <th class="text-center">Carry Forward</th>
            <th class="text-center">Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="leaveType in leaveTypeApi" :key="leaveTypeApi.id">
            <td class="text-start"> {{ leaveType.name }} </td>
            <td class="text-start"> {{ leaveType.description }}</td>
            <td class="text-center"> {{ leaveType.gender.name }} </td>
            <td class="text-center text-secondary" v-if="[4,5,11,12].includes(leaveType.id)"> N/A</td>
            <td class="text-center" v-else> {{ leaveType.amount }} </td>
            <td class="text-center">
                <span class="badge" :class="[leaveType.entitlement.length > 0 ? 'bg-success' : 'bg-danger']">
                    {{ leaveType.entitlement.length > 0 ? 'Enabled' : 'Disabled' }}
                </span>
            </td>
            <td class="text-center">
                <span class="badge" :class="[leaveType.pro_rated ? 'bg-success' : 'bg-danger']">
                    {{ leaveType.pro_rated ? 'Enabled' : 'Disabled' }}
                </span>
            </td>
            <td class="text-center">
                <span class="badge" :class="[leaveType.carry_forward.length > 0 ? 'bg-success' : 'bg-danger']">
                    {{ leaveType.carry_forward.length > 0 ? 'Enabled' : 'Disabled' }}
                </span>
            </td>
            <td class="text-center">
                <button
                    v-if="[1,2].includes(leaveType.id)"
                    class="button"
                    @click="viewLeaveType(leaveType)">View</button>
            </td>
        </tr>
        </tbody>
    </TableMain>

    <LeaveTypeModal :leaveType="leaveTypeModal" v-model="showModal" @confirm="confirm" @cancel="cancel"/>
    <msg-modal @cancel="cancel" :message="message.text" v-model="message.show"/>
    <ConfirmationModal message="Warning: This action will permanently delete the selected leave type and all associated data (such as leave requests and approvals). Are you sure you want to proceed?"
                       v-model="showDeleteModal" @confirm="deleteApi"
                       confirmLabel="Delete"
    />

</template>

<script>

import Pagination from "../elements/Pagination.vue";
import LeaveTypeModal from './LeaveTypeModal.vue';
import $api from "../api";
import ConfirmationModal from "../elements/ConfirmationModal.vue";
import TableMain from "../elements/TableMain.vue";

export default {
    components: {
        Pagination,
        LeaveTypeModal,
        ConfirmationModal,
        TableMain
    },
   data() {
       return {
           deleteId: null,
           postApi: '',
           showModal: false,
           showDeleteModal: false,
           leaveTypeModal: {},
           leaveTypeApi: [],
           message: {
               'text' : '',
               'show' : false,
           },
       };
   },
    methods: {
        cancel(close){
          close();
        },
        viewLeaveType(data) {
            this.postApi = '/api/leave-type/' + (data.id);
            this.message.text = 'Leave type successfully updated';
            data.showMenu = false;
            this.leaveTypeModal = data;
            this.showModal = true;
        },
        async confirm(){
            let formData = new FormData();
            formData.append('name', this.leaveTypeModal.name);
            formData.append('description', this.leaveTypeModal.description);
            formData.append('amount', this.leaveTypeModal.amount);
            formData.append('gender', this.leaveTypeModal.gender.value);
            formData.append('pro_rated', this.leaveTypeModal.pro_rated ? 1 : 0);
            formData.append('carry_forward', this.leaveTypeModal.carry_forward ? 1 : 0);

            await $api.post(this.postApi, formData)
                .then(response => {
                    this.getLeaveTypeApi();
                    this.showModal = false;
                    this.message.show = true;
                }).catch(error => {
                    console.log(error);
                });

        },
        async deleteApi() {
            await $api.delete('/api/leave-type/' + this.deleteId)
                .then(response => {
                    this.getLeaveTypeApi();
                    this.showDeleteModal = false;
                    this.message.text = 'Successfully deleted ' + response.data.data.name;
                    this.message.show = true;
                });
        },
        async getLeaveTypeApi() {
            await $api.get('/api/leave-type')
                .then(response => {
                    let data = [];
                    response.data.data.map(function (value, key) {
                        data.push({
                            'id' : value.id,
                            'name' : value.name,
                            'showMenu': false,
                            'description': value.description,
                            'amount': value.amount,
                            'gender': value.gender,
                            'entitlement': value.entitlement,
                            'pro_rated': value.pro_rated,
                            'carry_forward': value.carry_forward,
                        });
                    });
                    this.leaveTypeApi = data;
                });
        },
    },
    created() {
        this.getLeaveTypeApi();
    }
}
</script>
