<?php

namespace App\Http\Controllers;

use App\Services\MatchService;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    protected $matchService;

    public function __construct(MatchService $matchService)
    {
        $this->matchService = $matchService;
    }

    /**
     * Display matches and their stats
     */
    public function index()
    {
        $matches = $this->matchService->getAllMatches();
        return response()->json(['matches' => $matches]);
    }

    public function startWeekSimulation($week)
    {
        if (is_null($week)) {
            return response()->json(['error' => 'Week parameter is required'], 400);
        }

        $result = $this->matchService->startWeekSimulation($week);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], $result['code']);
        }

        return response()->json([
            'message' => $result['message'],
            'fixtures' => $result['fixtures']
        ]);
    }

    /**
     * Get fixtures by week that are not completed
     */
    public function getFixturesByWeek($week)
    {
        $fixtures = $this->matchService->getFixturesByWeek($week);

        if ($fixtures->isEmpty()) {
            return response()->json(['error' => 'No matches found for this week'], 404);
        }

        return response()->json(['matches' => $fixtures]);
    }

    public function resetSimulation()
    {
        $result = $this->matchService->resetSimulation();
        return response()->json(['message' => $result['message']]);
    }
}
