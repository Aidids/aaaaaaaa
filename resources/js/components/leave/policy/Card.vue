<template>
    <div class="notes mb-4" :class="color">
        <div><span></span></div>
        <div class="d-flex align-items-center">
            <i :class="icon" class="bi text-secondary me-2" style="font-size: 2rem;"></i>
            <h5>{{title}}</h5>
        </div>
        <slot></slot>
    </div>
</template>
<script>
export default {
    props: {
        icon: {
            type: String,
            default: 'bi-calendar2-week'
        },
        title: {
            type: String,
            default: ''
        },
        color: {
            type: String,
            default: 'yellow-red-pin',
        }
    }
}
</script>
