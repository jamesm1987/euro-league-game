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

    protected function createFixture($data)
    {
        $model = new Fixture;

        $teams = $data['teams'];
        $fixture = $data['fixture'];
        $result = $data['score']['fulltime'];

        $data = [
            'home_team_id' => Team::ByApiId($teams['home']['id'])->first('id')->id,
            'away_team_id' => Team::ByApiId($teams['away']['id'])->first('id')->id,
            'api_id' => $fixture['id'],
            'kickoff_at' => $fixture['date']
        ];
        
        $fixtureId = $model->firstOrNew(['api_id' => $fixture['id']], $data);
    
        if (!is_null($result['home']) && !is_null($result['away'])) {
            $this->saveResult($fixtureId, $result);
        }

        return true;


    }

    protected function saveResult($fixture, $result)
    {
        
        $model = new Result;


        $home_team_score = $result['home'];
        $away_team_score = $result['away'];

        dd($fixture);

        $model->upsert(
            ['fixture_id' => $fixture->id, 'home_team_score' => $home_team_score, 'away_team_score' => $away_team_score],
            ['fixture_id'],
            ['fixture_id', 'home_team_score', 'away_team_score']
        );

        return true;
    }

    public function mapApiToModel($apiRequestCollection)
    {
    
        foreach ($apiRequestCollection as $collection) {
            
            foreach ($collection as $apiRequest) {
                $this->createFixture($apiRequest->response);
            }
        }
    }
    
}
