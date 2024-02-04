<template>
    <div class="w-100">
        <Toast/>
        <h4 class="mb-3 me-2">Eform History</h4>
        <TabView v-model:activeIndex="active" class="eform-tab" @tab-change="updateUrl(tabs, active)">
            <TabPanel>
            <template #header>
                    <i class="bi bi-currency-exchange me-2"></i>
                    <span>Travel Claim</span>
                </template>
                <keep-alive>
                    <TEHistory v-if="active === 0" ref="travel-claim"/>
                </keep-alive>
            </TabPanel>
            <TabPanel>
                <template #header>
                    <i class="bi bi-airplane-engines me-2"/>
                    <span>Travel Authorization</span>
                </template>
                <keep-alive>
                    <TAHistory v-if="active === 1" ref="travel-authorization"/>
                </keep-alive>
            </TabPanel>
        </TabView>
    </div>
    <Modal v-model="modal.show" :modal="modal" :is-close="modal.isClose" :has-confirm="modal.hasConfirm"
           @confirm="confirmAction"/>

</template>

<script>
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import TAHistory from "./travel/TAHistory.vue";
import TEHistory from "./expenses/TEHistory.vue";
import tabOption from "../../mixins/tabOption";
import Modal from "../elements/Modal.vue";
import {useModalStore} from "../../stores/modal";
import {mapState} from "pinia";
import Toast from "primevue/toast";

export default {
    components: {
        Modal,
        TAHistory,
        TEHistory, TabView, TabPanel,
        Toast
    },
    mixins: [tabOption],
    data() {
        return {
            active: 0,
            tabs: [
                'travel-claim',
                'travel-authorization'
            ]
        }
    },
    created() {
        this.active = this.getTabIndex(this.tabs);
        useModalStore().init();
    },
    methods : {
        confirmAction(close) {
            if(this.active === 1){
                this.$refs["travel-authorization"].cancelTravelRequestApi()
            } else if(this.active === 0) {
                this.$refs["travel-claim"].cancelTravelRequestApi()
            }
            close()
        }
    },
    computed: {
        ...mapState(useModalStore, ['modal']),
    }
}
</script>
