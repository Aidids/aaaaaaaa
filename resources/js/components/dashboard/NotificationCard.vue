<template>
    <div class="grid-notification">
        <h4>Notifications</h4>
        <div class="card notification overflow-y-auto">
            <loader v-show="load"/>

            <div v-if="newNotification.length > 0">
                <article v-for="data in newNotification" :key="data.id">
                    <NotificationItem v-if="data.hour_diff < 12" :data="data"/>
                </article>

                <div class="previous-notification-label">
                    <div></div>
                    <strong class="text-secondary">PREVIOUS NOTIFICATIONS</strong>
                    <div></div>
                </div>

                <article v-for="data in newNotification" :key="data.id">
                    <NotificationItem  v-if="data.hour_diff >= 12" :unread="false" :data="data"/>
                </article>
            </div>

            <div v-else class="d-flex flex-column justify-content-center align-items-center my-auto px-5">
                <i class="bi bi-bell-slash-fill text-secondary" style="font-size: 4rem;"></i>
                <p class="text-secondary">You have no notifications</p>
            </div>
        </div>
    </div>
</template>
<script>
import NotificationItem from "./NotificationItem.vue"
import $api from "../api";
export default {
    components: {NotificationItem},

    data() {
      return {
          load: true,
          newNotification: {},
      };
    },

    created() {
        this.getNewNotification();
    },

    methods: {
        async getNewNotification() {
            await $api.get('/api/notification/' + parseInt(localStorage.getItem('user_id')))
                .then(response => {
                    this.newNotification = response.data.data;
                    this.load = false;
                });
        },
    }

}
</script>
