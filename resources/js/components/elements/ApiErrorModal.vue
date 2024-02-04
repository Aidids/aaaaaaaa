<template>
    <vue-final-modal v-slot="{ params, close }" v-bind="$attrs" classes="modal-container" content-class="modal-msg">
        <ol>
            <li v-for="error of errors" class="mb-2">
                <p>{{ error[0] }}</p>
            </li>
        </ol>
        <button @click="close" type="button" class="btn btn-primary mx-auto">Close</button>
    </vue-final-modal>
</template>

<script>
export default {
    props: ['errors'],
    inheritAttrs: false,
    emits: ['confirm', 'cancel'],
}
</script>
