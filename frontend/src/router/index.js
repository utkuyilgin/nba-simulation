import { createRouter, createWebHistory } from 'vue-router';
import MatchList from '@/views/MatchList.vue';
import Fixture from "@/views/Fixture.vue";

const routes = [
    { path: '/', component: Fixture },
    { path: '/matches', component: MatchList },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
