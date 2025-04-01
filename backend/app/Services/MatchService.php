<?php

namespace App\Services;

use App\Jobs\StartWeekSimulation;
use App\Repositories\Interfaces\MatchRepositoryInterface;
use App\Repositories\Interfaces\FixtureRepositoryInterface;
use App\Repositories\Interfaces\MatchPlayerStatsRepositoryInterface;
use Illuminate\Support\Facades\DB;

class MatchService
{
    protected $matchRepository;
    protected $fixtureRepository;
    protected $matchPlayerStatsRepository;

    public function __construct(
        MatchRepositoryInterface $matchRepository,
        FixtureRepositoryInterface $fixtureRepository,
        MatchPlayerStatsRepositoryInterface $matchPlayerStatsRepository
    ) {
        $this->matchRepository = $matchRepository;
        $this->fixtureRepository = $fixtureRepository;
        $this->matchPlayerStatsRepository = $matchPlayerStatsRepository;
    }

    public function getAllMatches()
    {
        return $this->matchRepository->getAll();
    }

    public function getFixturesByWeek($week)
    {
        return $this->matchRepository->getByWeek($week);
    }

    public function startWeekSimulation($week)
    {
        // Get fixtures for the week
        $fixtures = $this->matchRepository->getByWeek($week);

        if ($fixtures->isEmpty()) {
            return ['error' => 'No scheduled matches found for this week', 'code' => 404];
        }

        // Dispatch the StartWeekSimulation job
        dispatch(new StartWeekSimulation($week));

        return [
            'message' => "Week $week matches started",
            'fixtures' => $fixtures,
            'code' => 200
        ];
    }

    public function resetSimulation()
    {
        DB::transaction(function () {
            $this->matchPlayerStatsRepository->truncate();
            $this->matchRepository->truncate();
            $this->fixtureRepository->truncate();
        });

        return ['message' => 'Simulation reset', 'code' => 200];
    }
}
