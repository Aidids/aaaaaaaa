<template>
    <Toast/>
    <EmptyState
        v-if="unauthorized"
        title="You are not authorized to create claim"
        type="no-access"
        subtitle="There are no fixed approvers assigned to this account. Please contact HR department"
    />
    <main v-else>
        <div class="stepper-wrapper">
            <div v-for="(step, index) in steps" :key="index"  class="stepper-item" :class="{'completed': activeIndex >= (index + 1), 'active': activeIndex === index}">
                <div class="step-counter">
                    <i class="bi bi-check-lg" v-if="activeIndex >= (index + 1)"></i>
                    <span v-else>{{ index + 1 }}</span>
                </div>
                <div class="step-name">
                    {{ step }}
                </div>
                <span v-if="travelClaim.index_page >= (index + 1) && (travelClaim.isDraft)"
                      class="badge bg-success">Draft Saved</span>
            </div>
        </div>

        <Configuration v-if="activeIndex === 0" :travel-claim="travelClaim" @reset="reset" @cancel="back"/>
        <Allowance v-if="activeIndex === 1 && date" :travel-id="travelClaim.id" :date="date" @reset="reset"
                   @cancel="back"/>
        <Transport v-if="activeIndex === 2 && date" :travel-id="travelClaim.id" :date="date" @reset="reset"
                   @cancel="back"/>
        <Expenses v-if="activeIndex === 3 && date" :travel-id="travelClaim.id" :date="date" @reset="reset"
                  @cancel="back"/>
    </main>
    <Modal v-model="modal.show" :modal="modal" :is-close="modal.isClose" @complete="redirect"
           :has-confirm="modal.hasConfirm"
           @confirm="resetTravelClaimApi"/>
</template>

<script>
import Toast from 'primevue/toast';
import Allowance from "./forms/Allowance.vue";
import Transport from "./forms/Transport.vue";
import Expenses from "./forms/Expenses.vue";
import Configuration from "./forms/Configuration.vue";
import {mapState} from "pinia";
import {useModalStore} from "../../../stores/modal";
import {useLoadButton} from "../../../stores/loadButton";
import $api from "../../api";
import EmptyState from "../../elements/EmptyState.vue";
import Modal from "../../elements/Modal.vue";

export default {
    components: {
        Toast,
        EmptyState,
        Modal,
        Configuration,
        Expenses,
        Transport,
        Allowance,
    },

    props: {
        form_id: {
            type: Number,
            default: null,
        }
    },

    created() {
        useLoadButton().init()
        useModalStore().init()
        if (this.form_id) {
            this.showTravelClaimApi(this.form_id)
        } else {
            this.travelClaimApi()
        }
    },
    data() {
        return {
            travelClaim: {},
            activeIndex: 0,
            steps: [
                'Claim Info',
                'Allowance',
                'Transport',
                'Expenses'],
            unauthorized: true,
            date: null,
        }
    },
    methods: {
        redirect() {
            window.location.href = '/e-form/' + localStorage.getItem('user_id') + '/travel-claim'
        },

        back() {
            window.history.back()
        },

        reset() {
            useModalStore().confirm('Resetting will clear all information. Do you want to proceed?')
        },

        //do not remove, used in TEForm > children
        lockDate(date) {
            this.date = new Date(date);
        },

        async travelClaimApi() {
            await $api.get('/api/travel-claim')
                .then(response => {
                    this.travelClaim = response.data.data;
                    this.unauthorized = false;
                })
                .catch(() => {
                    this.unauthorized = true;
                })
        },

        async showTravelClaimApi(id) {
            await $api.get('/api/travel-claim/' + id + '/edit')
                .then(response => {
                    this.travelClaim = response.data.data;
                    this.unauthorized = false;
                })
                .catch((res) => {
                    this.unauthorized = true;
                })
        },

        async resetTravelClaimApi() {
            useLoadButton().start();
            useModalStore().load()
            let params = {
                'id': this.travelClaim.id,
            }
            await $api.delete('/api/travel-claim/reset', {params})
                .then(() => {
                    setTimeout(() => {
                        useLoadButton().finish()
                        useModalStore().finishLoad()
                        location.reload()
                        this.$toast.add({
                            severity: 'success',
                            summary: 'Success',
                            detail: 'Claim info updated',
                            life: 3000
                        });
                    }, 1000);
                })
                .catch((res) => {
                    useLoadButton().finish();
                    useModalStore().show(res.response.data.message)
                })
        }
    },
    computed: {
        ...mapState(useModalStore, ['modal']),
    },
}
</script>
