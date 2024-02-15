<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\League;
use App\Http\Resources\LeagueResource;

use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $leagues = LeagueResource::collection(League::with('teams')->get());
        dd($leagues);
        // return Inertia::render('Dashboard', [
        //     'leagues' => $leagues->resource
        // ]);
    }
}
