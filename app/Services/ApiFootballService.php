<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use App\Models\League;
use App\Models\Team;
use App\Models\Fixture;
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
        $model = new Fixture;
        $apiParams = [
            'type' => 'fixture',
            'endpoint' => 'fixtures',
            'args' => [
                'league' => 'api_id',
            ],
            'season' => true,
            'cacheExpire' => Carbon::now()->addHours(1),
            // 'filterResults' => [
                // 'filterBy' => 'country',
                // 'prefixType' => true
            // ],
        ];

        $requests = $this->apiQuery($apiParams);
        
        foreach ($requests as $request) {    
            $this->mapApiId($model, $request);
        }


        return $requests;
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
            'cacheExpire' => Carbon::now()->addDays(30),
            'filterResults' => [
                'filterBy' => 'country',
                'prefixType' => true
            ],
        ];

        $requests = $this->apiQuery($apiParams);
        
        foreach ($requests as $request) {    
            $this->mapApiId($model, $request);
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
            'cacheExpire' => Carbon::now()->addDays(30),
            'filterResults' => [
                'filterBy' => 'country',
                'field' => 'name',
                'prefixType' => false
            ],
        ];

        $requests = $this->apiQuery($apiParams);
        
        foreach ($requests as $request) {
            $this->mapApiId($model, $request);
        }
      
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
    
        $requests = [];
        
        foreach ($leagues as $id => $league) {
            
            $queryString = "";
            
            
            foreach ($params['args'] as $key => $value) {
    
                $queryString .= "$key={$league->$value}&";
            
            }

            $queryString = substr($queryString, 0, -1) . (!empty($params['season']) ? "&season=$year" : "");
                        
            // attempt to load from db
            $request = ApiRequest::where('league_id', $id)->where('request_type', $params['type'])->latest()->first();

            if (!$request || ($date > $params['cacheExpire'])) {   
               $response = $this->client->get("$endpoint?$queryString");
               $json_response = json_decode($response->getBody());

                foreach ($json_response->response as $data) {

                    $apiRequest = new ApiRequest([
                        'response' => $data,
                        'request_type' => $params['type'],
                        'league_id' => $id
                    ]);
                    $requests[] = $apiRequest;
                    
                    $apiRequest->save();

                }                
            } else {
                $requests[] = $request;
            }
        }

        return $requests;
    }  
    
    protected function saveRequest($request) {    

        $apiRequest = ApiRequest::Create([
            'response' => $request['response'],
            'request_type' => $request['request_type'],
            'league_id' => $request['league_id'],
        ]);

        return $apiRequest;


    }

    protected function mapApiId($model, $request)
    {
        $query = $model->newQuery();
        
        $type = strtolower(class_basename($model));

        if (Schema::hasColumn($model->getTable(), 'country')) {
            $query->where('country', $request->response['country']['name']);
        }

        $query->where('name', $request->response[$type]['name']);

        
        $match = $query->first();

        if ($match) {
                    
            $match->update([
                'api_id' => $request->response[$type]['id']
            ]);
            
            return true;
        } 

        return false;
    }
    
    protected function applyFilter($data, $params, $league) 
    {
        dd($data->{$params['type']}->{$params['filterResults']});

        

        if ($data->{$params['filterResults']} !== $league->country) {
            
        }
    }

}
