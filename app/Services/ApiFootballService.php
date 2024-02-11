<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use App\Models\{League, Team, Fixture, Result, ApiRequest };
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

use App\Jobs\CleanUpOldCachedData;


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

    public function getResults()
    {
        $model = new Result;
        
        $apiParams = [
            'type' => 'result',
            'endpoint' => 'fixtures',
            'args' => [
                'from' => $model::latest('created_at')->value('created_at')->format('Y-m-d'),
                'to' => Carbon::now()->format('Y-m-d')
            ],
            'season' => true,
            'cacheExpire' => '1 minute'
        ];

        CleanUpOldCachedData::dispatch($apiParams['type'], $apiParams['cacheExpire'])->onQueue('default');
        
        $requests = $this->apiQuery($apiParams);

        $model->mapApiToModel($requests);

        return $requests;
    }

    public function getFixtures()
    {
        $model = new Fixture;
        $apiParams = [
            'type' => 'fixture',
            'endpoint' => 'fixtures',
            'args' => [],
            'season' => true,
            'cacheExpire' => '1 day',
        ];

        CleanUpOldCachedData::dispatch($apiParams['type'], $apiParams['cacheExpire'])->onQueue('default');

        $requests = $this->apiQuery($apiParams);
        
          
        $model->mapApiToModel($requests);

        return $requests;
    }

    public function getTeams()
    {
        $model = new Team;
        $apiParams = [
            'type' => 'team',
            'endpoint' => 'teams',
            'args' => [],
            'season' => true,
            'cacheExpire' => '30 day'
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
            'args' => [
                'name' => 'name',
                'country' => 'country',
            ], 
            'season' => false,
            'cacheExpire' => '30 day',
            'filterByCountry' => true,
            'filterByType' => true
        ];

        $requests = $this->apiQuery($apiParams);

        $model->mapApiToModel($requests);
    
      
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
        
        foreach ($leagues as $key => $league) {
            
            $queryString = "league={$league->api_id}";
            

            if (!empty($params['args'])) {
            
                foreach ($params['args'] as $key => $value) {
                    $queryString .= "&$key={$value}";
                
                }

            }


            $queryString = !empty($params['season']) ? $queryString . "&season=$year" : $queryString;

 
            // attempt to load from db
            $request = ApiRequest::where('league_id', $league->id)->where('request_type', $params['type'])->latest('created_at')->get();
            
            $latest = !empty($request) ? $request->first() : null;

            if (!$latest || $date > $latest->created_at->add($params['cacheExpire'])) {
                
               $response = $this->client->get("$endpoint?$queryString");
               
               $json_response = json_decode($response->getBody());

                foreach ($json_response->response as $data) {
                  
                    $apiRequest = ApiRequest::create([
                        'response' => $data,
                        'request_type' => $params['type'],
                        'league_id' => $league->id
                    ]);
                    
                    $requests[$key] = collect($apiRequest);
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

    // protected function mapApiId($model, $request)
    // {
    //     $query = $model->newQuery();
        
    //     $type = strtolower(class_basename($model));

    //     if (Schema::hasColumn($model->getTable(), 'country')) {
    //         $query->where('country', $request->response['country']['name']);
    //     }

        
    //     foreach ($model::mappingKeys() as $key) {
            
    //         $temp[] = $request->response[$key];
    //     }

    //     dd($temp);
        // var_dump($request->response[$type]);
        // $query->where('name', $request->response[$type]['name']);

        
        // $match = $query->first();

        // if ($match) {
                    
        //     $match->update([
        //         'api_id' => $request->response[$type]['id']
        //     ]);
            
        //     return true;
        // } 

        // return false;
    //}
    
    protected function applyFilter($data, $params, $league) 
    {
        dd($data->{$params['type']}->{$params['filterResults']});

        

        if ($data->{$params['filterResults']} !== $league->country) {
            
        }
    }

}
