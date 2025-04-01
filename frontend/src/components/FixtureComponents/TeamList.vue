<template>
  <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
    <div v-for="team in teams" :key="team.id" class="col">
      <div class="card shadow-sm border-primary">
        <div class="card-body text-center">
          <h5 class="card-title fw-bold text-secondary">{{ team.name }}</h5>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    teams: Array,
  },
};
</script>

<style scoped>
/* Styles specific to TeamList */
</style>
