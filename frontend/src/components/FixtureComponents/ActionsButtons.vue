<template>
  <div class="mt-4 d-flex justify-content-between">
    <button @click="resetFixtures" class="btn btn-danger">
      Reset Fixtures
    </button>
    <button @click="continueSimulation" class="btn btn-success">
      Continue Simulation
    </button>
  </div>
</template>

<script>
export default {
  props: {
    resetFixtures: Function,
    continueSimulation: Function,
  },
};
</script>

<style scoped>
/* Styles specific to ActionButtons */
</style>
