<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class MatchSimulationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $matches;

    public function __construct($matches)
    {
        $matchCollection = is_array($matches) ? collect($matches) : $matches;

        $this->matches = $matchCollection->map(function ($match) {
            if (is_array($match)) {
                return $match;
            }

            $matchData = $match->toArray();

            if (isset($match->result)) {
                $matchData['result'] = $match->result;
            }

            return $matchData;
        });
    }

    public function broadcastOn()
    {
        return new Channel('match-simulation');
    }
}
