<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\League;
use App\Http\Resources\LeagueTeamsResource;

use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $leagues = League::with('teams')->get();

        return Inertia::render('Dashboard', [
            'leagueTeams' => LeagueTeamsResource::collection($leagues),
        ]);
    }
}
