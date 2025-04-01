<?php

namespace App\Listeners;

use App\Events\MatchSimulationUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateMatchStats implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct()
    {
        //
    }

    public function handle(MatchSimulationUpdated $event)
    {
        foreach ($event->matches as $match) {

            $logInfo = [];

            if (isset($match['score_team1'])) {
                $logInfo['score_team1'] = $match['score_team1'];
            }

            if (isset($match['score_team2'])) {
                $logInfo['score_team2'] = $match['score_team2'];
            }

            if (isset($match['current_minute'])) {
                $logInfo['current_minute'] = $match['current_minute'];
            }

            if (isset($match['status'])) {
                $logInfo['status'] = $match['status'];
            }

            if (isset($match['attack_count_team1'])) {
                $logInfo['attack_count_team1'] = $match['attack_count_team1'];
            }

            if (isset($match['attack_count_team2'])) {
                $logInfo['attack_count_team2'] = $match['attack_count_team2'];
            }

            if (isset($match['result'])) {
                $logInfo['minute'] = $match['current_minute'];
                $logInfo['result'] = $match['result'];

                // Add specific attack details for better tracking
                if (isset($match['result']['attack_team_name'])) {
                    $logInfo['attacking_team'] = $match['result']['attack_team_name'];
                }

                if (isset($match['result']['shooter'])) {
                    $logInfo['shooter'] = $match['result']['shooter'];
                    $logInfo['shot_result'] = $match['result']['result'];
                    if (isset($match['result']['points'])) {
                        $logInfo['points_scored'] = $match['result']['points'];
                    }
                }
            }

            Log::info("Match {$match['id']} updated:", $logInfo);
        }
    }
}
