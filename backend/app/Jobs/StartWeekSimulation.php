<?php

namespace App\Jobs;

use App\Models\Matches;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class StartWeekSimulation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $week;

    public function __construct($week)
    {
        $this->week = $week;
    }

    public function handle()
    {
        $fixtures = Matches::where('week', $this->week)->get();

        if ($fixtures->isEmpty()) {
            return response()->json(['error' => 'No scheduled matches found for this week'], 404);
        }

        Matches::whereIn('id', $fixtures->pluck('id'))->update([
            'start_time' => now(),
            'status' => 'ongoing',
        ]);

        foreach ($fixtures as $match) {
            dispatch(new SimulateMinuteJob($match, 1))->delay(now()->addSeconds(SimulateMinuteJob::TICK_DURATION));
        }

        return response()->json([
            'message' => "Week $this->week matches started",
            'fixtures' => $fixtures
        ]);
    }
}
