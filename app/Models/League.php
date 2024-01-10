<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Emoji\Emoji;

class League extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country',
        'api_id',
    ];

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
}
