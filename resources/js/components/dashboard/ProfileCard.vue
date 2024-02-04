<template>
  <div>
    <h4>My Profile</h4>
    <div class="card d-xl-flex flex-xl-row justify-content-around align-items-center">
      <div class="text-center col-xl-6">
        <loader v-show="!apiCall"/>
        <img class="mb-2" src="/img/icons/profile.png" alt="profile_img" width="90">
        <h4 class="fw-normal mb-2">{{ data.name }}</h4>
        <span class="badge-desb">DESB KL</span>
        <p class="mt-2"><small>{{ data.email ?? 'E-mail unassigned' }}</small></p>
      </div>
      <div class="col-xl-6 align-items-start">
        <IconBadge :value="data.title"/>
        <IconBadge icon="bi-buildings" title="Department" :value="data.department ?? 'Department unassigned'"/>
        <IconBadge icon="bi-calendar-plus" title="Joining date" :value="data.joining_date ?? 'Contact HR'"/>
      </div>
    </div>
  </div>
</template>
<script>
import IconBadge from './IconBadge.vue';

export default {
    props: {
        data: {
            type: Object,
            default: null
        },
        apiCall: {
            type: Boolean,
            default: false,
        }
    },

    components: {IconBadge}
}
</script>
