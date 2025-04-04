<?php

namespace App\Services;

use App\Repositories\Interfaces\MatchRepositoryInterface;
use App\Repositories\Interfaces\PlayerRepositoryInterface;
use App\Repositories\Interfaces\TeamRepositoryInterface;
use App\Repositories\Interfaces\MatchPlayerStatsRepositoryInterface;

class MatchSimulationService
{
    protected $matchRepository;
    protected $playerRepository;
    protected $teamRepository;
    protected $matchPlayerStatsRepository;

    const MATCH_DURATION_MINUTES = 48;

    public function __construct(
        MatchRepositoryInterface $matchRepository,
        PlayerRepositoryInterface $playerRepository,
        TeamRepositoryInterface $teamRepository,
        MatchPlayerStatsRepositoryInterface $matchPlayerStatsRepository
    ) {
        $this->matchRepository = $matchRepository;
        $this->playerRepository = $playerRepository;
        $this->teamRepository = $teamRepository;
        $this->matchPlayerStatsRepository = $matchPlayerStatsRepository;
    }

    public function simulateMinute($match)
    {
        $match->current_minute += 1;

        if ($match->current_minute >= self::MATCH_DURATION_MINUTES) {
            $match->status = 'completed';
            $match->save();
            $this->matchRepository->updateTeamStats($match);
            return [
                'message' => 'Match completed',
                'final_score' => $match->score_team1 . '-' . $match->score_team2,
                'minute' => $match->current_minute
            ];
        }

        // Determine which team attacks for this minute
        $attackingTeamId = $this->determineAttackingTeam($match);
        $defendingTeamId = ($attackingTeamId == $match->team1_id) ? $match->team2_id : $match->team1_id;

        $attackingTeam = $this->teamRepository->find($attackingTeamId);
        $defendingTeam = $this->teamRepository->find($defendingTeamId);

        // Simulate the attack
        $attackResult = $this->simulateAttack($match, $attackingTeamId, $defendingTeamId);

        // Add team information to the attack result
        $attackResult['attack_team'] = $attackingTeamId;
        $attackResult['attack_team_name'] = $attackingTeam->name;
        $attackResult['minute'] = $match->current_minute;
        $attackResult['score'] = $match->score_team1 . '-' . $match->score_team2;

        if (isset($attackResult['shooter'])) {
            $attackResult['team'] = $attackingTeam->name;
            $attackResult['team_id'] = $attackingTeamId;
            $attackResult['opponent'] = $defendingTeam->name;
            $attackResult['opponent_id'] = $defendingTeamId;
        }
        $match->save();

        return $attackResult;
    }

    public function finalizeMatch($match)
    {
        return $this->matchRepository->finalizeMatch($match);
    }

    public function updateTeamStats($match)
    {
        $matchPlayerStats = $this->matchPlayerStatsRepository->getByMatch($match->id);

        foreach ($matchPlayerStats as $stat) {
            $player = $this->playerRepository->find($stat->player_id);

            $playerAttributes = [
                'total_score' => $player->total_score + $stat->points,
                'assists' => $player->assists + $stat->assists
            ];

            if ($stat->two_point_success > 0) {
                $playerAttributes['two_point_success'] = $player->two_point_success + 1;
            }

            if ($stat->three_point_success > 0) {
                $playerAttributes['three_point_success'] = $player->three_point_success + 1;
            }

            $this->playerRepository->update($player->id, $playerAttributes);
        }
    }

    private function determineAttackingTeam($match)
    {
        if ($match->current_minute % 2 == 1) { // Odd minutes: team1 attacks
            $match->attack_count_team1 += 1;
            return $match->team1_id;
        } else { // Even minutes: team2 attacks
            $match->attack_count_team2 += 1;
            return $match->team2_id;
        }
    }

    private function simulateAttack($match, $attackingTeamId, $defendingTeamId)
    {
        $attacker = $this->playerRepository->getRandomFromTeam($attackingTeamId);
        $assister = $this->playerRepository->getRandomFromTeamExcept($attackingTeamId, $attacker->id);
        $team = $this->teamRepository->find($attackingTeamId);

        // rebound added
        $shotType = (rand(1, 100) <= 70) ? 2 : 3;  // %70 ihtimalle 2 sayılık, %30 ihtimalle 3 sayılık
        $baseSuccessRate = ($shotType == 2) ? 67 : 50; // Temel başarı oranı

        $playerStat = $this->matchPlayerStatsRepository->firstOrNew([
            'match_id' => $match->id,
            'player_id' => $attacker->id,
        ]);

        // calculate success rate
        $currentSuccessRate = ($shotType == 2) ? $playerStat->two_point_percentage ?? 55 : $playerStat->three_point_percentage ?? 55;
        $finalSuccessRate = ($currentSuccessRate + $baseSuccessRate) / 2;
        $success = (rand(1, 100) <= $finalSuccessRate);

        // shot type
        if ($shotType == 2) {
            $playerStat->two_point_attempts += 1;
        } else {
            $playerStat->three_point_attempts += 1;
        }

        // if success shot
        if ($success) {
            if ($attackingTeamId == $match->team1_id) {
                $match->score_team1 += $shotType;
            } else {
                $match->score_team2 += $shotType;
            }

            $playerStat->points += $shotType;
            //handle success rates
            if ($shotType == 2) {
                $playerStat->two_point_success += 1;
                $playerStat->two_point_percentage = $this->calculateSuccessRate(
                    $playerStat->two_point_success,
                    $playerStat->two_point_attempts
                );
            } else {
                $playerStat->three_point_success += 1;
                $playerStat->three_point_percentage = $this->calculateSuccessRate(
                    $playerStat->three_point_success,
                    $playerStat->three_point_attempts
                );
            }

            // assister stat updating
            if ($assister) {
                $assisterStat = $this->matchPlayerStatsRepository->firstOrNew([
                    'match_id' => $match->id,
                    'player_id' => $assister->id,
                ]);

                $assisterStat->assists += 1;
                $assisterStat->save();
            }

            $playerStat->save();

            // Fast break situation simulation
            if (rand(1, 100) <= 20) { // 20% chance of fast attack
                return $this->handleFastBreak($match, $attackingTeamId, $defendingTeamId, $shotType, $attacker, $assister);
            }

            return [
                'result' => 'success',
                'points' => $shotType,
                'shooter' => $attacker->name,
                'shooter_id' => $attacker->id,
                'team_name' => $team->name,
                'assister' => $assister ? $assister->name : null,
                'assister_id' => $assister ? $assister->id : null
            ];
        } else {
            // unsuccessful shot
            if ($shotType == 2) {
                $playerStat->two_point_percentage = $this->calculateSuccessRate(
                    $playerStat->two_point_success,
                    $playerStat->two_point_attempts
                );
            } else {
                $playerStat->three_point_percentage = $this->calculateSuccessRate(
                    $playerStat->three_point_success,
                    $playerStat->three_point_attempts
                );
            }

            $playerStat->save();

            // Rebound situation
            return $this->handleRebound($match, $attackingTeamId, $defendingTeamId, $attacker, $shotType, $playerStat);
        }
    }

    private function handleRebound($match, $attackingTeamId, $defendingTeamId, $attacker, $shotType, $playerStat)
    {
        // Rebound situation, steal ball from opponent simulation
        $rebounder = $this->playerRepository->getRandomFromTeam($defendingTeamId);
        $rebounderStat = $this->matchPlayerStatsRepository->firstOrNew([
            'match_id' => $match->id,
            'player_id' => $rebounder->id,
        ]);

        // Rebounder statistics increase
        $rebounderStat->rebounds += 1;
        $rebounderStat->save();

        // Fastbreak or normal attack status after rebound
        if (rand(1, 100) <= 30) { // Fast attack with &20 chances
            return $this->handleFastBreak($match, $defendingTeamId, $attackingTeamId, $shotType, $rebounder, null);
        }

        // Start a normal attack
        return [
            'result' => 'miss',
            'shot_type' => $shotType . '-pointer',
            'shooter' => $attacker->name,
            'shooter_id' => $attacker->id,
            'team_name' => $attackingTeamId == $match->team1_id ? $match->team1->name : $match->team2->name
        ];
    }

    private function handleFastBreak($match, $attackingTeamId, $defendingTeamId, $shotType, $attacker, $assister)
    {
        // Fast break: Shot with higher success rate
        $baseSuccessRate = ($shotType == 2) ? 75 : 70;  // Fast break success rate 75% (2-pointers) and 70% (3-pointers)
        $playerStat = $this->matchPlayerStatsRepository->firstOrNew([
            'match_id' => $match->id,
            'player_id' => $attacker->id,
        ]);

        $currentSuccessRate = ($shotType == 2) ? $playerStat->two_point_percentage ?? 55 : $playerStat->three_point_percentage ?? 55;
        $finalSuccessRate = ($currentSuccessRate + $baseSuccessRate) / 2;
        $success = (rand(1, 100) <= $finalSuccessRate);

        if ($success) {
            if ($attackingTeamId == $match->team1_id) {
                $match->score_team1 += $shotType;
            } else {
                $match->score_team2 += $shotType;
            }

            $playerStat->points += $shotType;
            $playerStat->save();

            // If an assist was made
            if ($assister) {
                $assisterStat = $this->matchPlayerStatsRepository->firstOrNew([
                    'match_id' => $match->id,
                    'player_id' => $assister->id,
                ]);
                $assisterStat->assists += 1;
                $assisterStat->save();
            }

            return [
                'result' => 'success',
                'points' => $shotType,
                'shooter' => $attacker->name,
                'shooter_id' => $attacker->id,
                'team_name' => $attackingTeamId == $match->team1_id ? $match->team1->name : $match->team2->name,
                'assister' => $assister ? $assister->name : null
            ];
        }

        return [
            'result' => 'miss',
            'shot_type' => $shotType . '-pointer',
            'shooter' => $attacker->name,
            'shooter_id' => $attacker->id,
            'team_name' => $attackingTeamId == $match->team1_id ? $match->team1->name : $match->team2->name
        ];
    }




    private function calculateSuccessRate($successfulShots, $totalAttempts)
    {
        if ($totalAttempts == 0) {
            return 0; // Hiç girişim yoksa başarı oranı %0 olsun
        }

        return round(($successfulShots / $totalAttempts) * 100, 2);
    }

}

