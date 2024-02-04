<template>
    <div class="container p-0">
        <div class="row">
            <label class="col-xl-3 mt-2">{{ label }}</label>
            <div class="col">
                <div class="row px-2">
                    <slot name="input"></slot>
                </div>
                <div class="row">
                    <slot name="validation"></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        label: String,
    },
};
</script>
