<template>
  <div v-if="fixturesStore.expandedMatches[match.id]" class="border-top p-3">
    <div v-if="match.status !== 'scheduled'" class="row g-2 small">
      <div v-for="team in [match.team1, match.team2]" :key="team.id" class="col-6">
        <div class="border rounded p-2">
          <h4 :class="team.id === match.team1_id ? 'text-primary' : 'text-danger'" class="fw-bold border-bottom pb-1 mb-1">
            {{ team.name }}
          </h4>
        </div>
      </div>
    </div>

    <div v-if="match.player_stats && match.player_stats.length" class="mt-3">
      <h4 class="small fw-bold mb-1">Best Players</h4>
      <div class="row g-2 small">
        <div class="col-6">
          <div v-for="stat in fixturesStore.getTopPlayers(match, match.team1_id, 2)" :key="stat.player.id"
               class="bg-primary text-white p-2 rounded mb-1">
            <p class="fw-medium">{{ stat.player.name }}</p>
            <p class="mb-0">{{ stat.points }} points, {{ stat.assists }} assists</p>
            <p class="mb-0">{{ stat.two_point_percentage }}% 2P, {{ stat.three_point_percentage }}% 3P</p>
          </div>
        </div>

        <div class="col-6">
          <div v-for="stat in fixturesStore.getTopPlayers(match, match.team2_id, 2)" :key="stat.player.id"
               class="bg-danger text-white p-2 rounded mb-1">
            <p class="fw-medium">{{ stat.player.name }}</p>
            <p class="mb-0">{{ stat.points }} points, {{ stat.assists }} assists</p>
            <p class="mb-0">{{stat.two_point_percentage }}% 2P, {{stat.three_point_percentage }}% 3P</p>
          </div>
        </div>
      </div>
    </div>

    <div v-if="match.events && match.events.length" class="mt-3">
      <h4 class="small fw-bold mb-1">Latest Events</h4>
      <div class="overflow-auto bg-light p-2 rounded small" style="max-height: 120px;">
        <div v-for="(event, index) in match.events.slice(0, 5)" :key="index"
             :class="fixturesStore.getEventClass(event)"
             class="mb-1 p-1 rounded">
          <span class="fw-bold">-</span>
          {{ fixturesStore.formatEventDescription(event) }}
        </div>
      </div>
    </div>

    <button @click="fixturesStore.toggleAllPlayers(match.id)" class="btn btn-primary btn-sm mt-2">
      {{ fixturesStore.expandedPlayers[match.id] ? "Hide Player Statistics" : "Show All Players" }}
    </button>
  </div>
</template>

<script setup>
import { useMatchStore } from '@/stores/matches';
import { defineProps } from 'vue';

const props = defineProps({
  match: Object
});
const fixturesStore = useMatchStore();
</script>
