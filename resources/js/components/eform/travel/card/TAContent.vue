<template>
    <div class="bg-success-subtle p-3 pb-0">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="col-5 text-secondary-emphasis text-start align-self-start" style="word-wrap: break-word;">{{ overallTravel.from }}</h5>
            <i class="bi bi-airplane-engines text-secondary"
               style="font-size: 1.5rem; transform: rotate(90deg)"/>
            <h5 class="col-5 text-secondary-emphasis text-end align-self-start" style="word-wrap: break-word;">{{ overallTravel.to }}</h5>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <h6>{{ this.displayDate(overallTravel.start_date) }}</h6>
            <h6>{{ overallTravel.duration }} day(s)</h6>
            <h6>{{ this.displayDate(overallTravel.end_date) }}</h6>
        </div>
    </div>
</template>

<script>
import calculateWorkDay from "../../../../mixins/calculateWorkDay";

export default {
    props: ['overallTravel'],
    mixins: [calculateWorkDay]
}
</script>
