<?php

namespace App\Providers;

use App\Repositories\FixtureRepository;
use App\Repositories\Interfaces\FixtureRepositoryInterface;
use App\Repositories\Interfaces\MatchPlayerStatsRepositoryInterface;
use App\Repositories\Interfaces\MatchRepositoryInterface;
use App\Repositories\Interfaces\PlayerRepositoryInterface;
use App\Repositories\Interfaces\TeamRepositoryInterface;
use App\Repositories\MatchPlayerStatsRepository;
use App\Repositories\MatchRepository;
use App\Repositories\PlayerRepository;
use App\Repositories\TeamRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MatchRepositoryInterface::class, MatchRepository::class);
        $this->app->bind(PlayerRepositoryInterface::class, PlayerRepository::class);
        $this->app->bind(TeamRepositoryInterface::class, TeamRepository::class);
        $this->app->bind(MatchPlayerStatsRepositoryInterface::class, MatchPlayerStatsRepository::class);
        $this->app->bind(FixtureRepositoryInterface::class, FixtureRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
