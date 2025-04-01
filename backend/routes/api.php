<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\FixtureController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/matches/simulate', [MatchController::class, 'simulate']);
Route::get('/matches', [MatchController::class, 'index']);
Route::get('/matches/{id}', [MatchController::class, 'show']);
Route::get('/teams', [TeamController::class, 'index']);
Route::get('/createFixture', [FixtureController::class, 'createFixture']);
Route::get('/fixtures', [FixtureController::class, 'getFixtures']);
Route::get('/fixtures/{week}', [MatchController::class, 'getFixturesByWeek']);
Route::post('/startWeekSimulation/{week}', [MatchController::class, 'startWeekSimulation']);
Route::delete('/resetFixtures', [FixtureController::class, 'resetFixtures']);
Route::post('/resetSimulation', [MatchController::class, 'resetSimulation']);

Route::get('/match-completed/{matchId}', function ($matchId) {
    return response()->json(['completed' => \Illuminate\Support\Facades\Cache::get("match_{$matchId}_completed", false)]);
});
