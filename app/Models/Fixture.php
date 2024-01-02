<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fixture extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'home_team_id',
        'away_team_id',
        'kick_off_at',
    ];

    protected $casts = [
        'kick_off_at' => 'datetime:Y-m-d',
    ];

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }
    
}


