<?php

namespace App\Repositories\Interfaces;

interface MatchPlayerStatsRepositoryInterface
{
    public function firstOrNew(array $attributes);
    public function getByMatch($matchId);
}
