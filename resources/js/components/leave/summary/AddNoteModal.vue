<template>
    <vue-final-modal v-slot="{ params, close }" v-bind="$attrs" classes="modal-container" content-class="modal-msg">
        <div class="modal-header">
            <h5>{{ (data.hr_note) ? 'Edit' : 'Add'}} Note</h5>
            <button @click="close" type="button" class="btn-close"></button>
        </div>
        <div class="modal-body mt-3" style="overflow: auto; max-height:80vh;">
            <Textarea
                cols="40"
                v-model="data.hr_note"
                rows="4"
                autoResize
                style="font-size: 0.85rem;"
                maxlength="175"
            />
        </div>
        <div class="modal-footer mt-3">
            <button v-if="disabled" type="button" class="btn btn-secondary">Save</button>
            <button v-else @click="save(close)" type="button" class="btn btn-success">Save</button>
        </div>
    </vue-final-modal>
</template>

<script>
import Textarea from 'primevue/textarea';
import $api from "../../api";

export default {
    components: {Textarea},
    props: ['data'],
    inheritAttrs: false,
    emits: ['confirm', 'cancel'],

    data() {
        return {
            disabled: true,
        }
    },

    methods: {
        async save(close) {
            let formData = new FormData;

            formData.append('hr_note', this.data.hr_note);

            await $api.post('/api/administration/add-leave-note/' + this.data.id, formData)
                .then(response => {
                    this.$parent.messageMethod(response.data.message)
                    close();
                })
                .finally(() => {
                    close();
                });

        },
    },

    watch: {
        data: {
            deep: true,
            handler (n,o) {
                this.disabled = n.hr_note === null
                    || o.hr_note !== n.hr_note
                    || n.hr_note === ''
            }
        }
    }
}
</script>
