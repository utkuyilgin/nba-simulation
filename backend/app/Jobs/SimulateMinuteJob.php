<?php

namespace App\Jobs;

use App\Services\MatchSimulationService;
use App\Events\MatchSimulationUpdated;
use App\Repositories\Interfaces\MatchRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laravel\Reverb\Loggers\Log;

class SimulateMinuteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $match;
    protected $minute;

    const MATCH_DURATION_MINUTES = 48;
    const TICK_DURATION = 5;

    public function __construct($match, $minute)
    {
        $this->match = $match;
        $this->minute = $minute;
    }

    public function handle(MatchSimulationService $simulationService, MatchRepositoryInterface $matchRepository)
    {

        $result = $simulationService->simulateMinute($this->match);

        $matchData = [
            'id' => $this->match->id,
            'score_team1' => $this->match->score_team1,
            'score_team2' => $this->match->score_team2,
            'current_minute' => $this->match->current_minute,
            'status' => $this->match->status,
            'attack_count_team1' => $this->match->attack_count_team1,
            'attack_count_team2' => $this->match->attack_count_team2,
            'player_stats' => $this->match->playerStats()->with('player')->take(5)->get(),
            'result' => $result,
        ];


        broadcast(new MatchSimulationUpdated([$matchData]));

        $this->match->updated_at = now();
        $this->match->save();

        // If match is not over, dispatch the next tick
        if ($this->minute < self::MATCH_DURATION_MINUTES) {
            $nextMinute = $this->minute + 1;
            dispatch(new SimulateMinuteJob($this->match, $nextMinute))->delay(now()->addSeconds(self::TICK_DURATION));
        } else {
            $simulationService->finalizeMatch($this->match);
        }
    }
}
