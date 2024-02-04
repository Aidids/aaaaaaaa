<template>
    <div class="d-flex justify-content-start align-items-center mb-3">
        <h4 class="mb-0">Assign Approvers</h4>
        <button class="button ms-2" @click="addApprovers">Add Approver</button>
    </div>

    <div class="d-xl-flex flex-row">
        <div class="col-xl-5 me-xl-5">
            <label class="form-label">1st Level Approvers</label>
            <TableMain>
                <thead>
                <tr>
                    <th>Name</th>
                    <th class="text-center">Action</th>
                </tr>
                </thead>
                <tbody>
                <tr class="mb-0" v-for="user in users1" :key="user.id">
                    <td class="text-start">
                        <div class="profile">
                            <img class="mb-2" src="/img/icons/profile.png" alt="profile_img">
                            <div class="ms-2" style="font-size: 0.85rem;">
                                <p class="fw-bold text-truncate">{{ user.name }}</p>
                                <p class="fw-light text-truncate">{{ user.position }}</p>
                                <p class="fw-lighter ms-auto">{{ user.department ?? 'No department set' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <a :href="'/profile/' + user.id" target="_blank"  class="button">View Profile</a>
                    </td>
                </tr>
                </tbody>
            </TableMain>
        </div>

        <div class="col-xl-5">
            <label class="form-label">2nd Level Approvers</label>
            <TableMain>
                <thead>
                <tr>
                    <th>Name</th>
                    <th class="text-center">Action</th>
                </tr>
                </thead>
                <tbody>
                <tr class="mb-0" v-for="user in users2" :key="user.id">
                    <td class="text-start">
                        <TableProfile :user="user"/>
                    </td>
                    <td class="text-center">
                        <a :href="'/profile/' + user.id" target="_blank"  class="button">View Profile</a>
                    </td>
                </tr>
                </tbody>
            </TableMain>
        </div>
    </div>

    <ApproversModal :approvers="approversModal" v-model="showModal" @confirm="confirm" @cancel="cancel"/>
    <msg-modal @cancel="reloadpage" :message="message.text" v-model="message.show"/>
</template>

<script>

import ApproversModal from "./ApproversModal.vue";
import $api from "../api.js";
import ApiErrorModal from "../elements/ApiErrorModal.vue";
import ConfirmationModal from "../elements/ConfirmationModal.vue";
import { useApproverStore } from "../../stores/approvers";
import TableMain from "../elements/TableMain.vue";
import TableProfile from "../elements/TableProfile.vue";

export default {
    props: ['user_id'],
    components: {
        ApproversModal,
        ApiErrorModal,
        ConfirmationModal,
        TableMain,
        TableProfile
    },
    data() {
        return {
            approversModal: {},
            click: false,
            users: {},
            isAdmin: false,
            users1: [],
            users2: [],
            showModal: false,
            showMessageModal: false,
            showErrorModal: false,
            errorData: {},
            messageModal: {},
            approverData: {},
            showConfirmationModal: false,
            message: {
                'text' : '',
                'show' : false,
            }
        };

    },
    computed: {},
    methods: {
        addApprovers() {
            this.showModal = !this.showModal;
            this.approversModal = {
                'name': '',
                'selectedUser': null,
                'selectedApproverLevel': null,
            }
        },
        cancel(close) {
            this.approversModal = {
                'name': '',
            };
            close()
        },
        reloadpage () {
            window.location.reload();
        },
        async search(query){
        },
        async confirm() {
            let formData = new FormData();
            formData.append( 'user_id', this.approversModal.selectedUser.id ?? '');
            formData.append( 'approver_id', this.approversModal.selectedApproverLevel.value);

            await $api.post( '/api/approver/assign', formData)
                .then(response => {

                    this.users1.push({
                        'selectedUser': response.data.data.selectedUser,
                        'selectedApproverLevel': response.data.data.selectedApproverLevel,
                    });
                    this.users2.push({
                        'selectedUser': response.data.data.selectedUser,
                        'selectedApproverLevel': response.data.data.selectedApproverLevel,
                    });

                    this.showModal = false;
                    this.message.text = 'Successfully add ' + response.data.data.name + ' as approver';
                    this.message.show = true;

                    useApproverStore().apiCall = false;
                }).catch(error => {
                    console.log(error);
                });
        },

        async getApproverApi() {
            await $api.get('/api/approver')
                .then(response => {
                    let users1 = [];
                    let users2 = [];
                    response.data.data.forEach( (user) => {
                        if (user.approver_level === 1) {
                            users1.push(user);
                        } else if (user.approver_level === 2) {
                            users2.push(user);
                        }
                    });
                    this.users1 = users1;
                    this.users2 = users2;
                });
        },
    },
    created() {
        this.getApproverApi();
    }
}
</script>

