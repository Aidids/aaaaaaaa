<template>
    <div class="input-search my-2 my-xl-0">
        <i class="fa fa-search text-secondary"></i>
        <input
            :value="value"
            @input="$emit('update:value', $event.target.value)"
            type="text"
            class="me-3 form-control"
            :placeholder="placeholder"
        />
    </div>
</template>

<script>
export default {
    props: {
        value: {
            type: String,
            required: true,
            default: ''
        },
        placeholder: {
            type: String,
            default: "Search employee"
        }
    }
};
</script>
