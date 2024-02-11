<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\DB;

use App\Models\Result;
use App\Models\Team;
use App\Models\Fixture;

class CreateFixtureJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fixture = Fixture::firstOrNew(['api_id' => $this->data['fixture']['id']], $this->data);
        
        if ($fixture->result) {
            return;
        }

        $this->createFixture($fixture);

    }
    protected function createFixture($fixture)
    {
        $teams = $this->data['teams'];
        $result = $this->data['score']['fulltime'];

        $fixture->fill([
            'home_team_id' => Team::where('api_id', $teams['home']['id'])->value('id'),
            'away_team_id' => Team::where('api_id', $teams['away']['id'])->value('id'),
            'api_id' => $this->data['fixture']['id'],
            'kickoff_at' => $this->data['fixture']['date']
        ])->save();


        if ($this->apiHasResult($result)) {
            $this->saveResult($fixture, $result);
        }

        return true;


    }

    protected function apiHasResult($result)
    {
        return !is_null($result['home']) && !is_null($result['away']);
    }

    protected function saveResult($fixture, $result): void
    {
        $home_team_score = $result['home'];
        $away_team_score = $result['away'];

        Result::Create([
            'fixture_id' => $fixture->id, 
            'home_team_score' => $home_team_score, 
            'away_team_score' => $away_team_score
        ]);
    }
}
