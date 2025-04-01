<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\MatchSimulationUpdated;
use App\Listeners\UpdateMatchStats;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        MatchSimulationUpdated::class => [
            UpdateMatchStats::class,
        ],
    ];


    public function boot(): void
    {
        parent::boot();
    }
}
