<template>
    <vue-final-modal v-slot="{ params, close }" v-bind="$attrs" classes="modal-container" content-class="modal-msg">
        <div class="text-center">
            <p class="mb-4">{{ this.message }}</p>
            <div class="d-flex justify-content-center">
                <button @click="close" type="button" class="btn btn-secondary mx-1">Close</button>
                <button @click="$emit('confirm', close)" type="submit" class="btn text-center btn-success mx-1">{{ this.confirmLabel }}</button>
            </div>
        </div>
    </vue-final-modal>
</template>

<script>
export default {
    props: ['message', 'confirmLabel'],
    inheritAttrs: false,
    emits: ['confirm'],
}
</script>
