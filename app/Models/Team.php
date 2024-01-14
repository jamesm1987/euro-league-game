<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Arr;
use App\Contracts\ApiMappable;
use Illuminate\Database\Eloquent\Builder;   


class Team extends Model implements ApiMappable
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'league_id',
        'price',
        'api_id',
    ];




    public function league()
    {
        return $this->belongsTo(League::class);
    }

    protected function price(): Attribute
    {
        return Attribute::make(
            // get: fn (int $value) => ($value * 1000000)
        );
    }

    public static function getUnMappedTeams() 
    {
        $teams = self::pluck('name')->toArray();

        $apiTeams = ApiRequest::where('request_type', 'team')->pluck('response');
        $unmappedTeams = [];
        foreach ($apiTeams as $apiTeam) {
            if (in_array($apiTeam['team']['name'], $teams)) {
                continue;
            }

            $unmappedTeams[$apiTeam['team']['id']] = $apiTeam['team']['name'];
        }

        return Arr::sort($unmappedTeams);
    }

    public static function mappingkeys()
    {
        return ['team'];
    }

    public function scopeByApiId(Builder $query, string $id): void
    {
        $query->where('api_id', $id);
    }

    public function mapApiId($apiRequests)
    {

    }

}


