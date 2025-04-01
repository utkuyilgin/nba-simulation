<?php

namespace App\Repositories;

use App\Models\Fixture;
use App\Models\Matches;
use App\Models\Team;
use App\Repositories\Interfaces\FixtureRepositoryInterface;

class FixtureRepository implements FixtureRepositoryInterface
{
    protected $model;

    public function __construct(Fixture $model)
    {
        $this->model = $model;
    }

    public function truncate()
    {
        return $this->model->query()->delete();
    }
    public function getAllFixtures()
    {
        return Fixture::with(['team1', 'team2'])->get();
    }

    public function getFixturesWithMatches()
    {
        return Fixture::with(['team1', 'team2', 'matches.team1', 'matches.team2'])->get();
    }

    public function createFixtures()
    {
        $teams = Team::all();

        if ($teams->count() < 2) {
            return ['error' => 'En az 2 takım olmalı!', 'status' => 400];
        }

        $teamCount = $teams->count();
        $hasDummy = false;
        if ($teamCount % 2 != 0) {
            $teamCount++;
            $hasDummy = true;
        }

        $totalWeeks = $teamCount - 1;
        $matchesPerWeek = $teamCount / 2;
        $teamIds = $teams->pluck('id')->toArray();

        if ($hasDummy) {
            array_push($teamIds, 0);
        }

        $fixtures = [];
        $fixedTeam = array_shift($teamIds);

        for ($week = 1; $week <= $totalWeeks; $week++) {
            if (!$hasDummy || ($fixedTeam != 0 && $teamIds[0] != 0)) {
                $fixture = Fixture::create([
                    'team1_id' => $fixedTeam,
                    'team2_id' => $teamIds[0],
                    'team1_score' => null,
                    'team2_score' => null,
                    'week' => $week
                ]);

                Matches::create([
                    'team1_id' => $fixedTeam,
                    'team2_id' => $teamIds[0],
                    'fixture_id' => $fixture->id,
                    'start_time' => now(),
                    'status' => 'Scheduled',
                    'week' => $week,
                ]);

                $fixtures[] = $fixture;
            }

            for ($i = 1; $i < $matchesPerWeek; $i++) {
                $team1Index = $i;
                $team2Index = $teamCount - 1 - $i;

                if ($hasDummy && ($teamIds[$team1Index] == 0 || $teamIds[$team2Index] == 0)) {
                    continue;
                }

                $fixture = Fixture::create([
                    'team1_id' => $teamIds[$team1Index],
                    'team2_id' => $teamIds[$team2Index],
                    'team1_score' => null,
                    'team2_score' => null,
                    'week' => $week
                ]);

                Matches::create([
                    'team1_id' => $teamIds[$team1Index],
                    'team2_id' => $teamIds[$team2Index],
                    'fixture_id' => $fixture->id,
                    'start_time' => now(),
                    'status' => 'Scheduled',
                    'week' => $week,
                ]);

                $fixtures[] = $fixture;
            }

            $teamIds = $this->rotateArray($teamIds);
        }

        return $this->getFixturesWithMatches();
    }
    private function rotateArray($array)
    {
        $lastElement = array_pop($array);
        array_unshift($array, $lastElement);
        return $array;
    }
    public function resetFixtures()
    {
        Fixture::truncate();
        Matches::where('fixture_id', '!=', null)->delete();
        return ['message' => 'Fixtures truncated'];
    }
}
