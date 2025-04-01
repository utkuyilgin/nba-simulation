<?php


namespace App\Http\Controllers;

use App\Repositories\FixtureRepository;
use Illuminate\Http\Request;

class FixtureController extends Controller
{
    protected $fixtureRepository;

    public function __construct(FixtureRepository $fixtureRepository)
    {
        $this->fixtureRepository = $fixtureRepository;
    }

    public function index()
    {
        $fixtures = $this->fixtureRepository->getAllFixtures();
        return response()->json($fixtures);
    }

    public function createFixture()
    {
        $result = $this->fixtureRepository->createFixtures();

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], $result['status']);
        }

        return response()->json($result);
    }

    public function resetFixtures()
    {
        $result = $this->fixtureRepository->resetFixtures();
        return response()->json($result);
    }
    public function getFixtures() {
        $result = $this->fixtureRepository->getAllFixtures();
        return response()->json($result);
    }
}
