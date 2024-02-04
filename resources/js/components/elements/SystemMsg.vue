<template>
<div class="modal" id="modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="min-height: 10rem !important;">
      <div class="modal-body d-flex flex-column align-items-center justify-content-center">
        <p class="mb-4 text-capitalize">{{ this.message }}</p>
        <button @click="closeModal" type="button" class="button">Close</button>
      </div>
    </div>
  </div>
</div>
</template>

<script>
import $ from "jquery";
export default {
    props: ['message'],
    methods: {
     closeModal() {
        $('#modal').hide();
     }
    }
}
</script>

<style>
    .modal{
        background-color: rgba(0,0,0,0.5);
    }
</style>
