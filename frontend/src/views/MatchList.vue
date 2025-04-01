<template>
  <div class="container py-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 bg-light p-3 rounded">
      <h1 class="h4 fw-bold">NBA Schedule</h1>

      <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0">
        <select v-model="fixturesStore.selectedWeek" class="form-select form-select-sm" @change="fixturesStore.fetchFixtures">
          <option v-for="week in fixturesStore.weeks" :key="week" :value="week">Week {{ week }}</option>
        </select>
        <button @click="startSimulation" class="btn btn-success btn-sm" :disabled="isSimulationButtonDisabled">
          {{ fixturesStore.isSimulationActive ? 'Simulation is ongoing' : 'Start Simulation' }}
        </button>
        <button @click="fixturesStore.resetSimulation" class="btn btn-danger btn-sm">
          Restart
        </button>
        <span v-if="fixturesStore.isSimulationActive" class="text-success fw-bold small">Simulation is ongoing</span>
      </div>
    </div>

    <div v-if="fixturesStore.matches.length" class="row g-2">
      <div v-for="match in fixturesStore.matches" :key="match.id" class="col-md-6 col-lg-6">
        <MatchCard :match="match" />
        <MatchDetail :match="match" />
        <PlayerStats :match="match" />
      </div>
    </div>
    <p v-else class="text-muted text-center mt-4">No matches found for this week.</p>
  </div>
</template>

<script setup>
import MatchCard from '@/components/MatchListComponents/MatchCard.vue';
import MatchDetail from '@/components/MatchListComponents/MatchDetail.vue';
import PlayerStats from '@/components/MatchListComponents/PlayerStats.vue';

import { onMounted, onBeforeUnmount, computed, ref } from 'vue';
import { useMatchStore } from '@/stores/matches';

const fixturesStore = useMatchStore();

// Local state for button disabling
const isSimulationButtonDisabled = ref(false);

const startSimulation = () => {
  fixturesStore.startSimulation();  // Start the simulation
  fixturesStore.isSimulationActive = true; // Disable button by updating state
  isSimulationButtonDisabled.value = true; // Disable the button after the click
};

onMounted(() => {
  fixturesStore.fetchFixtures();
  fixturesStore.setupEchoListeners();
});

onBeforeUnmount(() => {
  fixturesStore.cleanupEchoListeners();
});
</script>
