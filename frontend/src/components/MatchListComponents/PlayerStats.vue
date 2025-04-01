<template>
  <div v-if="fixturesStore.expandedPlayers[match.id]" class="mt-3">
    <h4 class="small fw-bold mb-1">All Players</h4>
    <div class="row g-2 small">
      <div v-for="stat in match.player_stats" :key="stat.player.id" class="col-6">
        <div class="p-2 border rounded">
          <p class="fw-bold">{{ stat.player.name }}</p>
          <p class="mb-0">{{ stat.points }} points, {{ stat.assists }} assists</p>
          <p class="mb-0">{{stat.two_point_percentage }}% 2P,
            {{ stat.three_point_percentage }}% 3P</p>
        </div>
      </div>
    </div>
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
