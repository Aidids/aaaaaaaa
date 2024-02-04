<template>
    <div class="d-xl-flex align-items-start mb-2">
        <div class="col-xl-4 my-2 d-flex flex-row align-items-center">
            <i class="me-2" :class="iconClass" style="font-size: 1.75rem;"></i>
            <span class="h5">{{ title }}</span>
        </div>
        <div class="col-xl-6 my-2">
            <p v-if="details == null">Address is not yet configured</p>
            <p v-else class="mb-1">{{ details }}, {{ city }}, {{ state }}, {{ zip }}, {{ country }}</p>
            <div class="d-flex align-items-center">
                <i class="bi bi-telephone color-primary me-2"></i>
                <p :class="{'text-muted': phone == null}" class="mb-0">{{ phoneText }}</p>
            </div>
        </div>
        <div class="col-xl-3 my-2">
            <slot></slot>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        title: String,
        iconClass: String,
        details: String,
        city: String,
        state: String,
        zip: String,
        country: String,
        phone: String,
    },
    computed: {
        phoneText() {
            return this.phone == null ? 'Not Set' : this.phone;
        },
    },
};
</script>

