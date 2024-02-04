<template>
    <div class="d-flex flex-column justify-content-center align-items-center mb-5">
        <img :src="getImage" alt="" style="width: 15rem; height: 15rem">
        <h4 class="text-center">{{ title }}</h4>
        <small class="text-secondary d-block mb-3">{{ subtitle }}</small>
        <ButtonLoad v-if="showAddButton" @click="$emit('add')">
          <div class="d-inline-flex align-items-center gap-1">
            <i class="bi bi-plus-circle" style="font-size: 1rem; font-style: normal"/>{{ addLabel }}
          </div>
        </ButtonLoad>
    </div>
</template>

<script>
import ButtonLoad from "../elements/ButtonLoad.vue";
export default {
    components: {ButtonLoad},
    emits: ['add'],
    props: {
        type: {
            type: String,
            default: 'empty'
        },
        title: {
            type: String,
            default: 'No Data available'
        },
        subtitle: {
            type: String,
            default: ''
        },
        showAddButton: {
            type: Boolean,
            default: false
        },
        addLabel: {
            type: String,
            default: ''
        }
    },
    computed: {
        getImage() {
            if(this.type === 'empty'){
                return '/img/icons/empty-state.svg';
            }
            if(this.type === 'no-access'){
                return '/img/icons/unauthorized.svg';
            }
        }
    }
}
</script>
