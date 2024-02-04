<template>
    <vue-final-modal v-slot="{ params, close }" v-bind="$attrs" classes="modal-container" content-class="modal-msg">
        <div class="text-center">
            <p class="mb-3">{{ this.message }}</p>
            <button @click="$emit('cancel', close)" type="button" class="button">Close</button>
        </div>
    </vue-final-modal>
</template>

<script>
export default {
    props: ['message'],
    inheritAttrs: false,
    emits: ['confirm', 'cancel'],
}
</script>
