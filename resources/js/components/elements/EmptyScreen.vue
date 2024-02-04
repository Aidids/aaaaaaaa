<template>
    <!--    Deprecated: Use EmptyState.vue. will remove gradually-->
    <div class="text-center my-5">
        <img src="/img/icons/agreement.png" alt="no_documents" class="ps-4 mb-2" width="150">
        <p class="text-center fw-light">{{ this.text }}</p>
    </div>
</template>

<script>
export default {
    props: {
        icon: {
            type: String,
            default: 'bi bi-folder-x'
        },
        text: {
            type: String,
            default: 'No Data available'
        }
    }
}
</script>
