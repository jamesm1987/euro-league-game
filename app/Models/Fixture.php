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

    public function teams()
    {
        return [
            'home' => $this->homeTeam, 
            'away' => $this->awayTeam
        ];
    }

    public function league()
    {
        return $this->homeTeam()->league()->id;
    }

    public static function mappingkeys()
    {
        return ['goals', 'teams','fixture'];
    }

    public function result()
    {
        return $this->hasOne(Result::class);
    }

    public function createFixture($fixture, $teams)
    {
        $fixture = new Fixture;


        $fixture->home_team_id = Team::ByApiId($teams->home->id)->get('id');
        $fixture->away_team_id = Team::ByApiId($teams->away->id)->get('id');

        $fixture->api_id = $fixture->id;
        $fixture->kickoff_at = $fixture->date;
        $fixture->save();


    }

    public function mapApiId($apiRequests)
    {
        
    }
    
}
