<template>
    <div class="d-flex flex-column w-100 border border-1 p-3 rounded-3 mb-2" :class="styleClass">
        <label class="form-label mb-3 w-100 fw-bold">{{ label }}</label>
        <span v-if="user">
            <TableProfile :user="user"/>
            <div class="comment-box ms-3">
                    <span v-if="remark && remark !== 'null'" style="word-wrap: break-word;">
                    {{ remark }}
                </span>
                <span v-else class="text-secondary fw-lighter" style="word-wrap: break-word;">
                    No remarks added
                </span>
            </div>
            <small v-if="status !== 'pending'" class="text-secondary ms-3 d-block mb-3">
                <span>Status change to </span>
                <span class="badge text-capitalize" :class="getStatusOption(status)" style="font-size: 0.7rem">{{ status }}</span>
                <span> on </span>
                <span class="fw-bold">{{ date }}</span>
            </small>
        </span>
        <div v-else class="my-auto align-self-center d-flex flex-column text-center text-danger">
            <i class="bi bi-person-fill-x" style="font-size: 2.5rem;"></i>
            <small>Not Assigned</small>
        </div>
    </div>
</template>
<script>
import TableProfile from "./TableProfile.vue";
import statusOption from "../../mixins/statusOption";

export default {
    components: {TableProfile},
    mixins: [statusOption],
    props: {
        styleClass: {
            type: String,
            default: ''
        },
        label: {
            type: String,
            default: ''
        },
        user: {
            type: Object,
            default: null
        },
        remark: {
            type: String,
            default: ''
        },
        status: {
            type: String,
            default: ''
        },
        date: {
            type: String,
            default: '',
        }
    }
}
</script>
