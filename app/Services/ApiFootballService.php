<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use App\Models\League;
use App\Models\Team;
use App\Models\ApiRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;


class ApiFootballService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api-football-v1.p.rapidapi.com/v3/',
            'headers' => [
                'x-rapidapi-key' => config('api-football.api_key'),
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function getFixtures()
    {

        $apiParams = [
            'type' => 'fixture',
            'endpoint' => 'fixtures',
            'args' => [
                'league' => 'api_id',
            ],
            'season' => true
        ];

        $requests = $this->apiQuery($apiParams);
        
        $apiRequest = $this->saveRequests($requests);


        return $apiRequest;
    }

    public function getTeams()
    {
        $model = new Team;
        $apiParams = [
            'type' => 'team',
            'endpoint' => 'teams',
            'args' => [
                'league' => 'api_id',
            ],
            'season' => true,
            'cacheExpire' => Carbon::now()->addDays(30)
        ];

        $requests = $this->apiQuery($apiParams);
        
        if ($requests['save']) {
            
            $this->mapApiId($model, $requests);
        }

        return $requests;
    }

    public function getLeagues()
    {
        $model = new League;
        $apiParams = [
            'type' => 'league', 
            'endpoint' => 'leagues', 
            'args' => ['search' => 'name'], 
            'season' => false,
            'cacheExpire' => Carbon::now()->addDays(30)
        ];

        $requests = $this->apiQuery($apiParams);
        
        if ($requests['save']) {
            Log::info($requests);
            // $this->saveRequests($requests);
            // $this->mapApiId($model, $requests);
        }
    
        // dd($requests);
      
        return $requests;
    }

    protected function getYear()
    {
        $date = Carbon::now();

        return ($date->month >= 8 && $date->month <= 12) ? $date->year : $date->subYear()->year;
    }


    protected function apiQuery($params)
    {
        $leagues = League::all();
        $year = $this->getYear();
        $endpoint = $params['endpoint'];
        $date = Carbon::now();
        $save = false;


        foreach ($leagues as $id => $league) {
            
            $queryString = "";
            
            
            foreach ($params['args'] as $key => $value) {
    
                $queryString .= "$key={$league->$value}&";
            
            }

            $queryString = substr($queryString, 0, -1) . (!empty($params['season']) ? "&season=$year" : "");
            
            // attempt to load from db
            $request = ApiRequest::where('league_id', $league->id)->where('request_type', $params['type'])->first();
            $response = !empty($request) ? $request->response : false;

            if (!$request || ($date > $params['cacheExpire'])) {   
               $request = $this->client->get("$endpoint?$queryString");
               
               $json_response = json_decode($request->getBody());

               if (isset($json_response->response)) {

                foreach ($json_response->response as $data) {
          
                    $id = $leagueData->league->id;
                    $leagueName = $leagueData->league->name;
                    $leagueCountry = $leagueData->country->name;

                    // Now you can use these values as needed
                    // For example, printing the league name

                    if (in_array($leagueCountry, $leagues)) {
                        echo "League Name: $leagueName Country: $leagueCountry";
                    }
                }
            } else {
                // Handle the case where 'response' property is not present in the object
                echo "No response data available.";
            }


               $save = true;         
            }
    
            $data[$id]['request'] = $response;
            
            $data[$id]['league_id'] = $league->id;
            $data[$id]['type'] = $params['type'];
        }

        $data['save'] = $save;

        return $data;
    }  
    
    protected function saveRequest($request) {    

        $apiRequest = ApiRequest::Create([
            'response' => $request['response'],
            'request_type' => $request['type'],
            'league_id' => $request['league_id'],
        ]);

        return $apiRequest;


    }

    protected function mapApiId($model, $data)
    {
        $type = strtolower(class_basename($model));

        foreach ($data as $responseItem) {
            
            if (!isset($responseItem->response) || !is_array($responseItem->response)) {
                return;
            }
            
                foreach ($responseItem->response as $record) {
                
                    $query = $model::where('name', $record[$type]['name']);

                    if (Schema::hasColumn($model->getTable(), 'country')) {
                        $query->where('country', $record[$type]['country']['name']);
                    }

                    $match = $query->first();
                
                    if ($match) {
                    
                        $match->update([
                            'api_id' => $record[$type]['id']
                        ]);
                    } 
                }
        }

        return true;
    }

}
