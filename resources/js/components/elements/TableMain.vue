<template>
    <div class="table-responsive" :class="card ? 'card' : ''">
        <table class="table table-striped" :class="table">
            <slot></slot>
        </table>
    </div>
</template>

<script>
export default {
    props: {
        card: {
            type: Boolean,
            default: false,
        },
        table: {
          type: String,
          default: 'table-main'
        }
    }
}
</script>
