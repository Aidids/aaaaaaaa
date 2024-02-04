<template>
    <div class="modal fade" id="delete-address" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="min-height: 10rem !important;">
          <div class="modal-body d-flex flex-column align-items-center justify-content-center">
            <p class="mb-4">Are you sure you want to remove this address?</p>
            <div class="d-flex flex-row justify-content-center">
                <button type="button" class="btn btn-secondary mx-1" data-bs-dismiss="modal">Close</button>
                <button @click="postAddress" type="button" class="btn btn-danger mx-1" data-bs-dismiss="modal">Remove</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    </template>

<script>
import $api from '../../api.js';
export default {
    props: ['user_id', 'type'],
    methods: {
        async postAddress() {
            await $api.delete('/api/profile-settings/' + this.user_id + '/' + this.type)
            .then(response => {
                this.$parent.updated(response.data.message);
                this.$parent.fetchApi(this.type);
            }
            ).catch(error => {
                this.$parent.updated(error);
            });
        }
    },
}
</script>
