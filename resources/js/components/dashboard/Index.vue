<template>
    <article class="dashboard-grid">
        <NotificationCard/>
        <ProfileCard container="grid-profile" :data="profile" :api-call="apiCall"/>
        <LeaveCard/>
    </article>
    <CalendarCard :data="profile" v-if="profile.department_id"/>
    <RecentLeaveCard/>
</template>

<script>
import ProfileCard from './ProfileCard.vue'
import LeaveCard from './LeaveCard.vue';
import NotificationCard from "./NotificationCard.vue";
import CalendarCard from "./CalendarCard.vue";
import RecentLeaveCard from "./RecentLeaveCard.vue";
import { mapState } from "pinia";
import { useProfileStore } from "../../stores/getProfile";

export default {
    components: {ProfileCard, LeaveCard, NotificationCard, CalendarCard, RecentLeaveCard},

    created() {
        useProfileStore().init();
    },

    computed: {
        ...mapState(useProfileStore, ['profile', 'apiCall'])
    }
}
</script>
