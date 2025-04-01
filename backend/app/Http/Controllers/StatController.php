<?php

namespace App\Http\Controllers;

use App\Models\Stat;

class StatController extends Controller
{
    public function index()
    {
        return response()->json(Stat::all());
    }
}
