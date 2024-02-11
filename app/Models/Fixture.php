<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Jobs\CreateFixtureJob;

class Fixture extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'home_team_id',
        'away_team_id',
        'kickoff_at',
    ];

    protected $casts = [
        'kickoff_at' => 'datetime:Y-m-d H:i:s',
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
        return $this->homeTeam->league;
    }

    public static function mappingkeys()
    {
        return ['goals', 'teams','fixture'];
    }

    public function result()
    {
        return $this->hasOne(Result::class);
    }


    public function mapApiToModel($apiRequestCollection)
    {
    
        foreach ($apiRequestCollection as $collection) {
            
            foreach ($collection as $apiRequest) {
                CreateFixtureJob::dispatch($apiRequest->response)->onQueue('default');
            }
        }
    }

    
}
