<template>
  <div class="card shadow-sm">
    <div class="card-body" @click="fixturesStore.toggleMatchDetails(match.id)">
      <div class="d-flex justify-content-between align-items-center">
        <span class="small fw-medium">Hafta {{ match.week || 'Bilgi Yok' }}</span>
        <span :class="fixturesStore.getStatusClass(match.status)" class="badge">
          {{ fixturesStore.getStatusText(match.status) }}
        </span>
      </div>
      <div v-if="match.status === 'completed'" class="mt-2 text-center">
        <span class="fw-bold text-success">🏆 Kazanan: {{ fixturesStore.getWinner(match) }}</span>
      </div>
      <div class="d-flex justify-content-between align-items-center my-2">
        <span class="fw-semibold small">{{ match.team1.name }}</span>
        <span class="fw-bold mx-2">{{ match.score_team1 }} - {{ match.score_team2 }}</span>
        <span class="fw-semibold small">{{ match.team2.name }}</span>
      </div>

      <div class="d-flex justify-content-between align-items-center my-2">
        <span class="fw-semibold small">Attacks: {{ match.attack_count_team1 }} - {{ match.attack_count_team2 }}</span>
        <span class="fw-semibold small">Total Score: {{ match.score_team1 + match.score_team2 }}</span>
      </div>

      <div v-if="match.status === 'ongoing'" class="bg-light p-2 rounded small">
        <div class="d-flex justify-content-between align-items-center">
          <span class="text-muted">{{ match.current_minute }}/48 dk</span>
          <span v-if="match.currentPossession" class="badge bg-success">
            Top: {{ match.currentPossession }}
          </span>
        </div>
        <div v-if="match.lastAttack" class="mt-1 border-start border-warning ps-2">
          <p class="small mb-0">
            <span class="fw-semibold">{{ match.lastAttack.team_name }}</span>: {{ match.lastAttack.shooter }} {{ match.lastAttack.shot_type || (match.lastAttack.points + " sayılık") }}
            <span :class="match.lastAttack.result === 'success' ? 'text-success' : 'text-danger'">
              {{ match.lastAttack.result === 'success' ? 'isabetli!' : 'kaçırdı' }}
            </span>
          </p>
        </div>
      </div>

      <div class="d-flex justify-content-between small mt-2">
        <span>
          <i class="fas" :class="fixturesStore.expandedMatches[match.id] ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
          {{ fixturesStore.expandedMatches[match.id] ? 'Kapat' : 'Detaylar' }}
        </span>
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