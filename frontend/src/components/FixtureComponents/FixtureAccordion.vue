<template>
  <div v-if="fixtures.length > 0">
    <h2 class="h5 fw-bold mt-5 text-success">Fixture</h2>
    <div class="accordion mt-3" id="fixtureAccordion">
      <div v-for="(weekFixtures, week) in groupedFixtures" :key="week" class="accordion-item">
        <h2 class="accordion-header">
          <button
              class="accordion-button fw-bold text-dark bg-light"
              type="button"
              data-bs-toggle="collapse"
              :data-bs-target="'#week' + week"
              aria-expanded="true"
              :aria-controls="'week' + week">
            Week {{ week }}
          </button>
        </h2>
        <div :id="'week' + week" class="accordion-collapse collapse show">
          <div class="accordion-body">
            <ul class="list-group">
              <li v-for="fixture in weekFixtures" :key="fixture.id" class="list-group-item">
                <span class="fw-semibold">{{ fixture.team1.name }}</span>
                <span class="text-muted"> vs </span>
                <span class="fw-semibold">{{ fixture.team2.name }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    fixtures: Array,
    groupedFixtures: Object,
  },
};
</script>

<style scoped>
/* Styles specific to FixtureAccordion */
</style>
