<template>
<Auth />
</template>

<script>
import Auth from "./Auth.vue";

export default {
    name: "AuthMain",
    data() {
        return {
            data: [],

        }
    },
    components: {
        Auth,

    },
}
</script>
