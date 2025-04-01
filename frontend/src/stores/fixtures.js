import { defineStore } from 'pinia';
import axios from 'axios';

const API_BASE_URL = process.env.VUE_APP_BACKEND_URL || '';

export const useFixturesStore = defineStore('fixtures', {
    state: () => ({
        teams: [],
        fixtures: []
    }),

    actions: {
        async fetchTeams() {
            try {
                const response = await axios.get(`${API_BASE_URL}/api/teams`);
                this.teams = response.data;
            } catch (error) {
                console.error('Takımları alırken hata oluştu:', error);
            }
        },
        async checkExistingFixtures() {
            try {
                const response = await axios.get(`${API_BASE_URL}/api/fixtures`);
                if (response.data.length > 0) {
                    this.fixtures = response.data;
                }
            } catch (error) {
                console.error('Fikstürler alınırken hata oluştu:', error);
            }
        },
        async createFixture() {
            try {
                const response = await axios.get(`${API_BASE_URL}/api/createFixture`);
                this.fixtures = response.data;
            } catch (error) {
                console.error('Fikstür oluşturulurken hata oluştu:', error);
            }
        },
        async resetFixtures() {
            try {
                await axios.delete(`${API_BASE_URL}/api/fixtures`);
                this.fixtures = [];
                await this.createFixture();
            } catch (error) {
                console.error('Simülasyon sıfırlanırken hata oluştu:', error);
            }
        }
    }
});