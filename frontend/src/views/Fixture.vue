<template>
  <div class="container py-4">
    <h1 class="h4 fw-bold mb-4 text-primary">Teams</h1>

    <!-- Team List -->
    <TeamList :teams="teams" />

    <!-- Fixture section -->
    <FixtureAccordion :fixtures="fixtures" :groupedFixtures="groupedFixtures" />

    <!-- Conditional Buttons -->
    <div v-if="fixtures.length === 0" class="text-center mt-4">
      <CreateFixtureButton :createFixture="createFixture" />
    </div>
    <div v-else>
      <ActionButtons :resetFixtures="resetFixtures" :continueSimulation="continueSimulation" />
    </div>
  </div>
</template>

<script>
import { useFixturesStore } from '@/stores/fixtures';
import { storeToRefs } from 'pinia';
import { useRouter } from 'vue-router';
import { onMounted, computed } from 'vue';

// Import child components
import TeamList from '../components/FixtureComponents/TeamList.vue';
import FixtureAccordion from '../components/FixtureComponents/FixtureAccordion.vue';
import ActionButtons from '../components/FixtureComponents/ActionsButtons.vue';
import CreateFixtureButton from '../components/FixtureComponents/CreateFixtureButton.vue';

export default {
  components: {
    TeamList,
    FixtureAccordion,
    ActionButtons,
    CreateFixtureButton,
  },
  setup() {
    const fixturesStore = useFixturesStore();
    const { teams, fixtures } = storeToRefs(fixturesStore);
    const { fetchTeams, checkExistingFixtures, createFixture, resetFixtures } = fixturesStore;
    const router = useRouter();

    onMounted(() => {
      fetchTeams();
      checkExistingFixtures();
    });

    // Group fixtures week by week
    const groupedFixtures = computed(() => {
      return fixtures.value.reduce((acc, fixture) => {
        if (!acc[fixture.week]) {
          acc[fixture.week] = [];
        }
        acc[fixture.week].push(fixture);
        return acc;
      }, {});
    });

    const continueSimulation = () => {
      router.push('/matches');
    };

    return {
      teams,
      fixtures,
      groupedFixtures,
      fetchTeams,
      checkExistingFixtures,
      createFixture,
      resetFixtures,
      continueSimulation,
    };
  }
};
</script>

<style scoped>
/* Styles specific to the main Teams component */
</style>
