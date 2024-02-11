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

class CreateResultJob implements ShouldQueue
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
        $fixture = Fixture::where('api_id', $this->data['fixture']['id'])->first();
        
        if ($fixture) {
            return;
        }

        $this->createResult($fixture);

    }
    protected function createResult($fixture)
    {
        $result = $this->data['score']['fulltime'];


        if ($this>apiHasResult($result)) {

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
