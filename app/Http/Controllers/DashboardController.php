<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\League;

use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $leagues = League::with('teams')->get();

        return Inertia::render('Dashboard', [
            'leagues' => $leagues
        ]);
    }
}
