<template>
  <div v-else class="mt-4 text-center">
    <button @click="createFixture" class="btn btn-primary btn-lg">
      <i class="bi bi-calendar-plus"></i> Create Fixture
    </button>
  </div>
</template>

<script>
export default {
  props: {
    createFixture: Function,
  },
};
</script>

<style scoped>
/* Styles specific to CreateFixtureButton */
</style>
