<template>
    <vue-final-modal v-slot="{ params, close }" v-bind="$attrs" classes="modal-container" content-class="modal-content">
        <div style="min-width: 30rem;">
            <div class="modal-header mb-3">
                <h5 class="modal-title">Add Document</h5>
                <button @click="close" type="button" class="btn-close"></button>
            </div>
            <div>
                <hr>
                <input v-model="this.document.id" type="hidden">
                <CustomInput v-model="this.document.name"
                             label="Name" class="w-100 mb-3" maxLength="100"
                             type="text" placeholder="Document name"
                             :required="true"
                />
                <div class="d-flex flex-column">
                    <label>
                        File
                        <span class="text-danger">*</span>
                    </label>
                    <input type="file" @change="uploadFile" ref="file">
                    <small class="text-muted font-italic mt-3 d-inline-block">
                        Document size allowed: 5MB. Document type allowed: Only .pdf type is allowed. Please check file and file format before upload.
                    </small>
                </div>

            </div>

            <div class="modal-footer mt-3">
                <button @click="$emit('cancel', close)" type="button" class="button-outline m-1">Cancel</button>
                <button @click="$emit('confirm', close)" type="submit" class="button m-1">Save</button>
            </div>

        </div>
    </vue-final-modal>
</template>

<script>
import CustomInput from '../../elements/CustomInput.vue';
export default {
    props: ['document'],
    components: {
      CustomInput
    },
    name: 'DocumentModal',
    inheritAttrs: false,
    emits: ['confirm', 'cancel'],
    methods: {
        uploadFile(){
            this.document.file = this.$refs.file.files[0];
        },
    },

    watch: {
        document: {
            deep: true,
            handler (n, o) {
                if (n.file === '') {
                    this.$refs.file.value = null;
                }
            }
        }
    },

}
</script>
