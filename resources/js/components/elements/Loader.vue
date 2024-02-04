<template>
    <div :class="large ? 'lds-container' : 'text-center'">
        <div class="lds-ring" :class="large ? 'large' : 'p-2'">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        large: {
            type: Boolean,
            default: true,
        },
    }
}
</script>
