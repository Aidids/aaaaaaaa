<template>
    <div class="h-100 d-flex flex-column">
        <div class="d-md-flex justify-content-between align-items-center gap-2 mt-2">
            <h5 class="col-md-6 col-xl-8">Documents</h5>
            <input v-debounce:1000ms="search" type="text" placeholder="Search document"
                   class="form-control my-2 my-md-0">
            <button class="button" type="button" @click="addDocument">Upload</button>
        </div>
        <hr>
        <div class="h-100">
            <loader v-if="this.pageLoad" :large="false" class="h-100 d-flex"/>
            <loader v-else-if="this.searchLoad" :large="false" class="h-100 d-flex"/>
            <div v-else-if="this.documentData.length === 0" class="h-100 d-flex flex-column justify-content-center align-items-center">
                    <i class="bi bi-file-earmark-excel text-danger" style="font-size: 5.5rem;"></i>
                    <h6 class="text-danger">No Documents Added Yet</h6>
            </div>
            <div v-else>
                <TableMain>
                    <thead>
                    <tr>
                        <th class="text-start">Document Name</th>
                        <th class="text-center" style="width: 30rem">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="i in this.documentData" :key="i">
                        <td class="text-start">{{ i.name }}</td>
                        <td class="text-center">
                            <div>
                                <PreviewAttachment
                                  :is-button="true"
                                  :href="localUrl + `/profile/` + i.path"
                                />
                                <button v-if="this.isAdmin" @click="editDocument(i)"
                                        class="btn btn-outline-success m-1">
                                    Edit
                                </button>
                                <button v-if="this.isAdmin" @click="deleteDocument(i.id)"
                                        class="btn btn-outline-danger m-1">Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </TableMain>
            </div>
        </div>
        <Pagination v-if="this.documentData.length > 0"
                    :perPage="5"
                    :totalPages="this.totalPages"
                    :currentPage="this.currentPage"
                    @pagechanged="onPageChange"/>
    </div>
    <DocumentModal :document="this.documentModal" v-model="showModal" @confirm="confirm" @cancel="cancel"/>
    <Modal :v-model="modal.show" :modal="modal" @complete="this.modal.show = false"/>
    <ConfirmationModal message="Are you sure you want to delete this document?"
                       v-model="showConfirmationModal" @confirm="deleteApi"
                       confirmLabel="Delete"
    />
</template>

<script>
import DocumentModal from '../modals/DocumentModal.vue';
import $api from "../../api";
import ApiErrorModal from "../../elements/ApiErrorModal.vue";
import ConfirmationModal from "../../elements/ConfirmationModal.vue";
import Pagination from "../../elements/Pagination.vue";
import TableMain from "../../elements/TableMain.vue";
import EmptyScreen from "../../elements/EmptyScreen.vue";
import Modal from "../../elements/Modal.vue";
import PreviewAttachment from "../../elements/attachments/PreviewAttachment.vue";

export default {
    props: ['user_id'],
    data() {
        return {
            documentApi: '',
            pageLoad: true,
            searchLoad: false,
            showModal: false,
            showConfirmationModal: false,
            documentModal: {},
            documentData: {},
            isAdmin: false,
            deleteId: null,
            currentPage: 1,
            totalPages: 1,
            modal: {
                show: false,
                loader: false,
                message: ""
            },
            localUrl: localStorage.getItem('currentUrl'),
        }
    },
    methods: {
        addDocument() {
            this.documentApi = '/document';
            this.documentModal = {
                'id': '',
                'name': '',
                'file': ''
            };
            this.showModal = true;
        },
        editDocument(data) {
            this.documentApi = '/edit-document';
            this.documentModal = data;
            this.showModal = true;
        },
        cancel(close) {
            this.documentModal = {
                'id': '',
                'name': '',
                'file': ''
            };

            close()
        },
        deleteDocument(id) {
            this.deleteId = id;
            this.showConfirmationModal = !this.showConfirmationModal;
        },
        onPageChange(page) {
            this.pageLoad = !this.pageLoad;
            this.currentPage = page;
            this.documentPagination();
        },
        finishLoad() {
            setTimeout(() => this.pageLoad = false, 250);
        },
        finishSearchLoad() {
            setTimeout(() => this.searchLoad = false, 250);
        },
        async search(query) {
            this.searchLoad = true;
            await $api.get('/api/profile-settings/' + this.user_id + '/document/' + query)
                .then(response => {
                    this.documentData = response.data.data;
                    this.totalPages = response.data.meta.last_page;
                    this.finishSearchLoad();
                });
        },
        async getDocumentApi() {
            await $api.get('/api/profile-settings/' + this.user_id + '/document?page=1')
                .then(response => {
                    this.documentData = response.data.data;
                    this.totalPages = response.data.meta.last_page;
                    this.finishLoad();
                });
        },
        async confirm() {
            this.pageLoad = !this.pageLoad;
            let formData = new FormData();
            formData.append('id', this.documentModal.id ?? '');
            formData.append('name', this.documentModal.name);

            if (this.documentModal.file !== undefined) {
                formData.append('file', this.documentModal.file);
            }

            await $api.post('/api/profile-settings/' + this.user_id + this.documentApi, formData)
                .then(response => {
                    this.showModal = !this.showModal;
                    this.modal = {
                        show: true,
                        loader: false,
                        message: response.data.message
                    }
                    this.getDocumentApi();
                }).catch(error => {
                    this.pageLoad = !this.pageLoad;
                    this.modal = {
                        show: true,
                        loader: false,
                        message: error.response.data.errors
                    };
                });
        },
        async deleteApi() {
            this.pageLoad = !this.pageLoad;
            await $api.delete('/api/profile-settings/' + this.user_id + '/document/' + this.deleteId)
                .then(response => {
                    this.showConfirmationModal = !this.showConfirmationModal;
                    this.modal = {
                        show: true,
                        loader: false,
                        message: response.data.message
                    };
                    this.getDocumentApi();
                }).catch(error => {
                    this.modal = {
                        show: true,
                        loader: false,
                        message: error.response.data.errors
                    };
                });
        },
        async documentPagination() {
            await $api.get('/api/profile-settings/' + this.user_id + '/document?page=' + this.currentPage)
                .then(response => {
                    this.documentData = response.data.data;
                    this.totalPages = response.data.meta.last_page;
                    this.finishLoad();
                });
        }
    },
    created() {
        this.getDocumentApi();
        (parseInt(localStorage.getItem('isAdmin')) === 1) ? this.isAdmin = true : this.isAdmin = false;
    },
    components: {
        Modal,
        DocumentModal,
        ApiErrorModal,
        ConfirmationModal,
        Pagination,
        TableMain,
        EmptyScreen,
        PreviewAttachment
    }
}
</script>
