<!--TODO: TO USE IN INPUT FORM WRAP THE FORM ATTACHMENT AS BELOW EXAMPLE-->
<!--<h5 class="mt-4 mb-2">Attachment</h5>-->
<!--<div class="form-control py-3"><FormMultipleAttachment/></div>-->
<!--@module fileupload-->

<template>
    <FileUpload
        :disabled="!approversExists"
        ref="file"
        name="files[]"
        :multiple="true"
        :show-upload-button="false"
        :show-cancel-button="false"
        accept="image/*, application/pdf, .eml, .msg"
        :maxFileSize="5000000"
        :preview-width="150"
        @change="onFileChange"
        :file-limit="10"
        @remove="onFileChange"
    />
    <p :class="[!approversExists? 'text-disabled' : 'text-secondary']">Attachment Uploaded: <strong>{{count}}</strong></p>
    <small
        :class="[!approversExists? 'text-disabled' : 'text-secondary']">{{ description }}</small>
</template>

<script>

export default {
    emits: ['update:fileChange'],
    props: {
        description: {
            type: String,
            default: ''
        },
        approversExists: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            count: 0,
        }
    },
    methods: {
        onFileChange() {
            this.$emit('update:fileChange', this.$refs.file.files)

            if (this.$refs.file) {
              this.count = this.$refs.file.files.length;
            }
        },
    }
}
</script>
