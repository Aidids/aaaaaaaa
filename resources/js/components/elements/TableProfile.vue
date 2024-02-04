<template>
  <div class="profile" :class="card && 'custom-card'">
    <img class="mb-2 d-print-none" src="/img/icons/profile.png" alt="profile_img">
    <div v-if="user" class="ms-2" style="font-size: 0.85rem;">
      <a v-if="navigate" :href="currentUrl + '/profile-settings/' + user.id"
         class="fw-bold text-truncate pointer"
         data-toggle="tooltip" data-placement="top"
         :title="user.name" target="_blank"
         :style="nameStyle"
      >
        {{ user.name }}
      </a>
      <p v-else class="fw-bold pointer"
         data-toggle="tooltip" data-placement="top"
         :title="user.name" :style="nameStyle"
      >
        {{ user.name }}
      </p>
      <p class="fw-light text-truncate">{{ user.position ?? user.title }}</p>
      <p class="fw-lighter ms-auto">{{ user.department ?? 'No department set' }}</p>
    </div>
    <small class="text-danger ms-3" v-else>User not found</small>
  </div>
</template>
<script>
export default {
  props: {
    user: {
      type: Object,
      default: null
    },
    navigate: {
      type: Boolean,
      default: false
    },
    card: {
      type: Boolean,
      default: false,
    },
    nameStyle: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      currentUrl: localStorage.getItem('currentUrl'),
    }
  },
}
</script>
