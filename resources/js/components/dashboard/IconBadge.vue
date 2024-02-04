<template>
    <div class="icon-badge">
        <i :class="icon" class="bi"></i>
        <div class="text">
            <p>{{ title }}</p>
            <p class="fw-bold">{{ value }}</p>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        icon: {
            type: String,
            default: 'bi-briefcase'
        },
        title: {
            type: String,
            default: 'Title'
        },
        value: {
            type: String,
            default: 'value'
        }
    }
}
</script>

