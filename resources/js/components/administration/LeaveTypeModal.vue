<template>
    <vue-final-modal v-slot="{ params, close }" v-bind="$attrs" classes="modal-container" content-class="modal-content">
        <div style="min-width: 90vw;">
            <div class="modal-header">
                <h5 class="modal-title">{{leaveType.name}} Details</h5>
                <button @click="close" type="button" class="btn-close"></button>
            </div>
            <div class="modal-body" style="overflow: auto; height:70vh">
                <hr>
                <div v-if="leaveType.entitlement && leaveType.entitlement.length > 0">
                    <label class="form-label">Entitlement</label>
                    <TableMain>
                        <thead class="small text-center">
                        <tr>
                            <th>Start Period</th>
                            <th>End Period</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="entitlement in leaveType.entitlement" class="small text-center">
                            <td>{{entitlement.start_period}} year</td>
                            <td>
                                <span v-if="entitlement.end_period !== 100">{{entitlement.end_period}} year</span>
                                <span class="text-muted" v-else>-</span>
                            </td>
                            <td>{{entitlement.amount}} days</td>
                        </tr>
                        </tbody>
                    </TableMain>
                </div>

                <div v-if="leaveType.carry_forward && leaveType.carry_forward.length > 0">
                    <label class="form-label">Carry Forward</label>
                    <TableMain>
                        <thead class="small text-center">
                        <tr>
                            <th>Start Period</th>
                            <th>End Period</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="carry_forward in leaveType.carry_forward" class="small text-center">
                            <td>{{carry_forward.start_period}} year</td>
                            <td>
                                <span v-if="carry_forward.end_period !== 100">{{carry_forward.end_period}} year</span>
                                <span class="text-muted" v-else>-</span>
                            </td>
                            <td>{{carry_forward.amount}} days</td>
                        </tr>
                        </tbody>
                    </TableMain>
                </div>

            </div>

            <div class="modal-footer">
                <button @click="close" type="submit" class="btn text-center btn-secondary">Close</button>
            </div>
        </div>
    </vue-final-modal>
</template>

<script>
import TableMain from "../elements/TableMain.vue";
import CustomInput from '../elements/CustomInput.vue';

export default {
    props: ['leaveType'],
    data() {
        return {
            disabled: true,
            addModel1: '',
            addModel2: '',
            addModel3: '',
            addModel4: '',
            addModel5: '',
            addModel6: '',
            genders: [
                {name: 'All', value: null},
                {name: 'Male', value: 'm'},
                {name: 'Female', value: 'f'},
            ],
        }
    },
    methods: {
        toggleProRated(){
            this.leaveType.pro_rated = !this.leaveType.pro_rated;
        }
    },
    components: {
        CustomInput,
        TableMain
    },
    inheritAttrs: false,
}

</script>
