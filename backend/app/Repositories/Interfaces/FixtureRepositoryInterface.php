<?php

namespace App\Repositories\Interfaces;

interface FixtureRepositoryInterface
{
    public function truncate();
    public function getAllFixtures();
    public function getFixturesWithMatches();
    public function createFixtures();
    public function resetFixtures();
}
