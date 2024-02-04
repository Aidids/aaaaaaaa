<template>
  <div class="my-5 text-center">
    <loader :large="false"></loader>
    <div>
      <small class="text-secondary">
        Loading data, please do not close or refresh the browser.
      </small>
      <ProgressBar :value="value" style="height: 1rem; width: 50%;" class="mx-auto mt-2"/>
    </div>
  </div>
</template>
<script>
import ProgressBar from 'primevue/progressbar';
export default {
  components: {ProgressBar},
  props: ['value']
}
</script>