<script>
import BooleanBadge from "../../../elements/BooleanBadge.vue";
import calculateWorkDay from "../../../../mixins/calculateWorkDay";
export default {
    components: {BooleanBadge},
    props: ["detail"],
    mixins: [calculateWorkDay],
    computed: {
        getIcon() {
            let icon = 'bi bi-x-circle text-danger';
            if (this.detail.flight_type === 1) {
                icon = 'bi bi-arrow-right';
            } else if (this.detail.flight_type === 2) {
                icon = 'bi bi-arrow-left-right';
            }
            return icon;
        },
        getTooltip() {
            let tooltip = 'Flight ticket not required';
            if (this.detail.flight_type === 1) {
                tooltip = 'One way flight ticket';
            } else if (this.detail.flight_type === 2) {
                tooltip = 'Return flight ticket';
            }
            return tooltip;
        }
    }
}
</script>

<template>
    <div class="w-100 d-flex justify-content-between align-items-center pe-3">
        <h5 class="col-6 text-start align-self-start" style="word-wrap: break-word;">{{ detail.from }}</h5>
        <i :class='getIcon' style="font-size: 1.2rem;" v-tooltip.top="getTooltip" type="text"/>
        <h5 class="col-6 text-end align-self-start" style="word-wrap: break-word;">{{ detail.to }}</h5>
    </div>
    <div class="w-100 d-flex justify-content-between align-items-center">
        <h6>{{ displayDate(detail.start_date) }}</h6>
<!--        <h6>{{ calculateDays(detail.start_date, detail.end_date) }} day(s)</h6>-->
        <h6>{{ displayDate(detail.end_date) }}</h6>
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <BooleanBadge :bool-state="detail.accomodation === 1" label="Accomodation"></BooleanBadge>
    </div>
</template>

