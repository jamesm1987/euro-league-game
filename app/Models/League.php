<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Emoji\Emoji;
use App\Contracts\ApiMappable;
use App\Traits\Models\HasApiRequests;

class League extends Model implements ApiMappable
{
    use HasFactory, HasApiRequests;

    protected $fillable = [
        'name',
        'country',
        'api_id',
    ];

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public static function getCountries()
    {


        return [
            [
                'name' => 'England',
                'country_code' => 'gb',
                'flag' => Emoji::flagsForFlagEngland()
            ],
            [
                'name' => 'Spain',
                'country_code' => 'es',
                'flag' => Emoji::flagsForFlagSpain()
            ],
            [
                'name' => 'Germany',
                'country_code' => 'ge',
                'flag' => Emoji::flagsForFlagGermany()
            ],
            [
                'name' => 'Italy',
                'country_code' => 'it',
                'flag' => Emoji::flagsForFlagItaly()
            ],
            [
                'name' => 'France',
                'country_code' => 'fr',
                'flag' => Emoji::flagsForFlagFrance(),
            ]
        ];
    }

    public static function getCountry($country) {

        $countries = self::getCountries();

        $country = array_search($country,  array_column($countries, 'name'));
        
        return ($country !== false) ? $country : false;
    }

    public function getTeamId($name)
    {

        $team = $this->where('name', $name)->first();

        return !is_null($team) ? $team->id : false;
    }

    public static function getLeagues()
    {
        return self::orderBy('id')->pluck('name', 'id')->toArray();
    }

    public static function mappingkeys()
    {
        return [
            'league' => 'name', 
            'country' => 'country'
        ];
    }


    public function toResource()
    {
        return new \App\Http\Resources\LeagueResource($this);
    }


}
