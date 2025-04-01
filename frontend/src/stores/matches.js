import { defineStore } from 'pinia';
import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
const API_BASE_URL = process.env.VUE_APP_BACKEND_URL || '';

export const useMatchStore = defineStore('matches', {
    state: () => ({
        matches: [],
        isSimulationActive: false,
        hasOngoingMatches: false,
        selectedWeek: 1,
        weeks: [1, 2, 3, 4],
        expandedMatches: {},
        expandedPlayers: {},
    }),

    getters: {
        ongoingMatches: (state) => {
            return state.matches.filter(match => match.status === 'ongoing');
        },
        getWinner: (state) => (match) => {
            if (match.status !== "completed") return null;

            if (match.score_team1 > match.score_team2) {
                return match.team1.name;
            } else if (match.score_team2 > match.score_team1) {
                return match.team2.name;
            } else {
                return "Berabere";
            }
        }
    },

    actions: {
        async fetchFixtures() {
            try {
                const response = await axios.get(`${API_BASE_URL}/api/fixtures/${this.selectedWeek}`);
                this.matches = response.data.matches.map(match => {
                    return {
                        ...match,
                        events: this.generateEventsFromPlayerStats(match),
                        lastAttack: null,
                        attack_count_team1: match.attack_count_team1 || 0,
                        attack_count_team2: match.attack_count_team2 || 0,
                        team1_stats: match.team1_stats || {},
                        team2_stats: match.team2_stats || {}
                    };
                });
                this.checkOngoingMatches();
            } catch (error) {
                console.error("Fikstür verileri alınamadı:", error);
            }
        },

        generateEventsFromPlayerStats(match) {
            let events = [];

            if (!match.player_stats || !Array.isArray(match.player_stats) || match.player_stats.length === 0) {
                return events;
            }

            match.player_stats.forEach(stat => {
                if (stat.points > 0) {
                    const teamName = stat.player.team_id === match.team1_id ? match.team1.name : match.team2.name;

                    events.push({
                        minute: Math.floor(Math.random() * 48) + 1,
                        shooter: stat.player.name,
                        team_name: teamName,
                        points: Math.min(stat.points, 3),
                        result: 'success',
                        shot_type: `${Math.min(stat.points, 3)} sayılık`,
                    });
                }
            });

            events.sort((a, b) => b.minute - a.minute);

            if (match.status === 'completed') {
                events.unshift({
                    minute: 48,
                    message: 'Match completed',
                    result: 'info'
                });
            }

            return events;
        },

        async startSimulation() {
            try {
                this.isSimulationActive = true;
                await axios.post(`${API_BASE_URL}/api/startWeekSimulation/${this.selectedWeek}`);
                await this.fetchFixtures();
            } catch (error) {
                console.error("Simülasyon başlatılamadı:", error);
                this.isSimulationActive = false;
            }
        },

        async resetSimulation() {
            try {
                await axios.post(`${API_BASE_URL}/api/resetSimulation`);
                window.location.href = '/';
            } catch (error) {
                console.error("Simülasyon durdurulamadı/devam ettirilemedi:", error);
            }
        },

        checkOngoingMatches() {
            this.hasOngoingMatches = this.matches.some(match => match.status === 'ongoing');

            if (!this.hasOngoingMatches) {
                this.isSimulationActive = false;
            }
        },

        getStatusClass(status) {
            return {
                completed: "bg-green-500 text-white",
                ongoing: "bg-yellow-500 text-white",
                scheduled: "bg-gray-500 text-white",
            }[status] || "bg-gray-500 text-white";
        },

        getStatusText(status) {
            return {
                completed: "Tamamlandı",
                ongoing: "Devam Ediyor",
                scheduled: "Başlamadı",
            }[status] || status;
        },

        getTeamPlayerStats(match, teamId) {
            if (!match.player_stats) return [];
            return match.player_stats.filter(stat => stat.player.team_id === teamId);
        },

        getTopPlayers(match, teamId, limit = 2) {
            const players = this.getTeamPlayerStats(match, teamId);
            return players.sort((a, b) => b.points - a.points).slice(0, limit);
        },

        toggleAllPlayers(matchId) {
            this.expandedPlayers = {
                ...this.expandedPlayers,
                [matchId]: !this.expandedPlayers[matchId]
            };
        },

        getEventClass(event) {
            if (!event) return "bg-gray-100";

            if (event.result === 'success') {
                return "bg-green-50 text-green-800";
            } else if (event.result === 'miss') {
                return "bg-red-50 text-red-800";
            } else if (event.message === 'Match completed') {
                return "bg-blue-50 text-blue-800";
            }

            return "bg-gray-100";
        },

        formatEventDescription(event) {
            if (!event) return '';

            if (event.result === 'success') {
                return `${event.shooter} (${event.team_name}) ${event.points} sayılık atışı isabetli${event.assister ? ' (Asist: ' + event.assister + ')' : ''}. ${event.points} sayı!`;
            } else if (event.result === 'miss') {
                return `${event.shooter} (${event.team_name}) ${event.shot_type} atışı kaçırdı.`;
            } else if (event.message === 'Match completed') {
                return 'Maç tamamlandı.';
            }

            return event.message || '';
        },

        toggleMatchDetails(matchId) {
            this.expandedMatches = {
                ...this.expandedMatches,
                [matchId]: !this.expandedMatches[matchId]
            };
        },

        setupEchoListeners() {
            window.Pusher = Pusher;
            window.Echo = new Echo({
                broadcaster: "pusher",
                key: "9d358a1e5a59a6f2288f",
                cluster: "eu",
                encrypted: true,
            });

            window.Echo.channel("match-simulation").listen("MatchSimulationUpdated", (event) => {
                console.log("Match updates received:", event);

                event.matches.forEach((updatedMatch) => {
                    const matchIndex = this.matches.findIndex(m => m.id === updatedMatch.id);

                    if (matchIndex !== -1) {
                        // Maç verilerini güncelle
                        this.matches[matchIndex] = {
                            ...this.matches[matchIndex],
                            score_team1: updatedMatch.score_team1,
                            score_team2: updatedMatch.score_team2,
                            current_minute: updatedMatch.current_minute,
                            status: updatedMatch.status,
                            attack_count_team1: updatedMatch.attack_count_team1,
                            attack_count_team2: updatedMatch.attack_count_team2,
                            player_stats: updatedMatch.player_stats || this.matches[matchIndex].player_stats, // Oyuncu istatistiklerini güncelle
                        };

                        if (updatedMatch.result) {
                            if (!this.matches[matchIndex].events) {
                                this.matches[matchIndex].events = [];
                            }

                            this.matches[matchIndex].events.unshift({
                                ...updatedMatch.result,
                                minute: updatedMatch.result.minute || updatedMatch.current_minute
                            });

                            if (updatedMatch.result.shooter) {
                                this.matches[matchIndex].lastAttack = {
                                    ...updatedMatch.result,
                                    minute: updatedMatch.result.minute || updatedMatch.current_minute
                                };
                            }

                            if (updatedMatch.result.attack_team_name) {
                                this.matches[matchIndex].currentPossession = updatedMatch.result.attack_team_name;
                            }
                        }

                        if (updatedMatch.status === "completed") {
                            this.matches[matchIndex].currentPossession = null;

                            const hasCompletionEvent = this.matches[matchIndex].events.some(e => e.message === 'Match completed');
                            if (!hasCompletionEvent) {
                                this.matches[matchIndex].events.unshift({
                                    minute: updatedMatch.current_minute || 48,
                                    message: 'Match completed',
                                    result: 'info'
                                });
                            }
                        }
                    }
                });

                this.checkOngoingMatches();
            });
        },
        cleanupEchoListeners() {
            if (window.Echo) {
                window.Echo.leave('match-simulation');
            }
        }
    }
});