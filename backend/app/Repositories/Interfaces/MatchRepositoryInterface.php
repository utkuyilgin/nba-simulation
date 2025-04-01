<?php

namespace App\Repositories\Interfaces;

interface MatchRepositoryInterface
{
    public function find($id);
    public function update($id, array $attributes);
    public function getWithPlayerStats($id);
    public function finalizeMatch($match);
    public function updateTeamStats($match);
    public function getAll();
    public function getByWeek($week);
    public function truncate();
}
