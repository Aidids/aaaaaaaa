<script>
export default {
    props: {
        label1: String,
        label2: String,
        required1: {
            type: Boolean,
            default: false,
        },
        required2: {
            type: Boolean,
            default: false,
        },
        isPrint: {
            type: Boolean,
            default: false
        }
    },
}
</script>

<template>
    <div :class="isPrint ? 'd-sm-flex' : 'd-md-flex'" class="gap-4">
        <div class="container mb-2 row">
            <label class="col-xl-3 p-0 pt-xl-2">{{ label1 }}
                <span v-if="required1" class="text-danger">*</span>
            </label>
            <div class="col">
                <div class="row">
                    <slot name="input1"></slot>
                </div>
                <div class="row">
                    <slot name="validation1"></slot>
                </div>
            </div>
        </div>
        <div class="container mb-2 row">
            <label class="col-xl-3 p-0 pt-xl-2">{{ label2 }}
                <span v-if="required2" class="text-danger">*</span>
            </label>
            <div class="col">
                <div class="row">
                    <slot name="input2"></slot>
                </div>
                <div class="row">
                    <slot name="validation2"></slot>
                </div>
            </div>
        </div>
    </div>
</template>

