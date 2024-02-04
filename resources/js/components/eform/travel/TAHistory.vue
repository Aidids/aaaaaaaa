<template>
    <div class="my-3 h-100">
        <loader v-if="pageLoad"/>
        <div v-else-if="travelRequests.length === 0"
             class="h-100 d-flex flex-column align-items-center justify-content-center">
            <EmptyState subtitle="No Request for Travel Authorization"/>
        </div>
        <div v-else v-for="(request, index) in travelRequests" :key="index" class="d-md-inline-flex">
            <TACard :is-approve-view="isApproveView" :request="request" @view-more="viewMore(request)"
                    @cancel="cancelRequest(request)" @viewAttachment="viewAttachment(request, $event)"
                    @action="confirmAction($event, request)"/>

        </div>
        <Pagination
            class="mt-2"
            v-show="this.totalPages > 1"
            :perPage="6"
            :totalPages="this.totalPages"
            :currentPage="this.currentPage"
            @pagechanged="onPageChange"/>
    </div>

    <TAModal
        :request="selectedRequest"
        v-model="showViewRequestModal"
        title="Travel Authorization Request"
        :is-approve-view="isApproveView"
        @action="confirmAction($event, selectedRequest)"/>

    <ViewAttachmentModal v-if="selectedRequest.approvers"
                         v-model="showViewAttachmentModal"
                         title="Travel Authorization Attachments"
                         :attachments="attachments"
                         :attachment_url="attachment_url"/>

    <ActionModal v-model="showActionModal" :action='approvalAction' @confirm="actionTravelRequestApi($event)"/>

</template>
<script>

import TACard from "./card/TACard.vue";
import $api from "../../api";
import Pagination from "../../elements/Pagination.vue";
import TAModal from "../travel/TAModal.vue";
import ViewAttachmentModal from "../../elements/attachments/ViewAttachmentModal.vue";
import ActionModal from "../../elements/ActionModal.vue";
import {mapState} from "pinia";
import calculateWorkDay from "../../../mixins/calculateWorkDay";
import {useProfileStore} from "../../../stores/getProfile";
import EmptyState from "../../elements/EmptyState.vue";
import {useModalStore} from "../../../stores/modal";


export default {
    components: {EmptyState, ActionModal, ViewAttachmentModal, TAModal, Pagination, TACard},
    props: {
        isApproveView: {
            type: Boolean,
            default: false,
        }
    },
    mixins: [calculateWorkDay],
    data() {
        return {
            travelRequests: [],
            selectedRequest: {},
            approvalAction: '',
            showViewRequestModal: false,
            showActionModal: false,
            showViewAttachmentModal: false,
            attachments: [],
            attachment_url: localStorage.getItem('currentUrl') + '/e-form/travel-authorization/',
            pageLoad: false,
            totalPages: 1,
            currentPage: 1,
        }
    },
    created() {
        this.getTravelRequestAPI();
    },
    methods: {
        viewAttachment(request, attachmentType) {
            this.selectedRequest = request;
            this.attachments = this.selectedRequest.approvers.attachments.filter((attachment) => attachment.hr_upload === attachmentType);
            this.showViewAttachmentModal = !this.showViewAttachmentModal;
        },

        confirmAction(action, request) {
            this.showViewRequestModal = false;
            this.selectedRequest = request;
            this.approvalAction = action;
            this.showActionModal = true;
        },

        viewMore(request) {
            this.selectedRequest = request;
            this.showViewRequestModal = true;
        },

        onPageChange(page) {
            this.pageLoad = true;
            this.currentPage = page;
            this.travelRequestPagination();
        },

        cancelRequest(request) {
            this.selectedRequest = request;
            useModalStore().confirm('Canceling your request is irreversible. Do you want to proceed?')
        },

        async getTravelRequestAPI() {
            let url = this.isApproveView ? '/api/travel-authorization/approver' : '/api/travel-authorization';
            await $api.get(url).then((response) => {
                this.travelRequests = response.data.data;
                this.totalPages = response.data.meta.last_page;
                this.pageLoad = false;
            });
        },

        async travelRequestPagination() {
            let url = this.isApproveView ? '/api/travel-authorization/approver' : '/api/travel-authorization';
            await $api
                .get(
                    url + '?page=' + this.currentPage
                )
                .then((response) => {
                    this.travelRequests = response.data.data;
                    this.totalPages = response.data.meta.last_page;
                    this.pageLoad = false;
                });
        },

        async cancelTravelRequestApi() {

            await $api
                .post('/api/travel-authorization/' + this.selectedRequest.id + '/cancel')
                .then((response) => {
                    this.getTravelRequestAPI();
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: response.data.message,
                        life: 3000
                    });
                })
                .catch((error) => {
                    useModalStore().show(error.response.data.message)
                });
        },

        async actionTravelRequestApi(remark) {
            let profile = useProfileStore().profile;
            let formData = new FormData();
            formData.append(profile.approver_level + '_id', profile.id);
            formData.append(profile.approver_level + '_status', this.approvalAction);
            formData.append(profile.approver_level + '_remark', remark);
            formData.append(profile.approver_level + '_date', this.convertDate(new Date()));
            await $api
                .post('/api/travel-authorization/' + this.selectedRequest.id + '/review', formData)
                .then((response) => {
                    this.getTravelRequestAPI();
                    this.showActionModal = false;
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: response.data.message,
                        life: 3000
                    });
                })
                .catch((error) => {
                    useModalStore().show(error.response.data.message)
                });
        },
    },
    computed: {
        ...mapState(useModalStore, ['modal']),
        ...mapState(useProfileStore, ['profile'])
    }
}
</script>
