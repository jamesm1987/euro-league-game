<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Casts\Attribute;
// use Illuminate\Database\Eloquent\Relations\MorphTo;

class ApiRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'response' => 'array'
    ];

    protected $fillable = [
        'response',
        'request_type',
        'league_id',
    ];


    // public function requestable(): MorphTo
    // {
    //     return $this->morphTo();
    // }

    public static function teams()
    {
        return self::where('request_type', 'team')->pluck('response')->map(function($leagues, $key) {

            return Arr::map(json_decode($leagues), function($obj, $key) {
               
                return ['id' => $obj->team->id, 'name' => $obj->team->name];
            });

        });
    }

    /**
     * Get response as json.
     */
    public function response()
    {
        return $this->response;
    }
    
}

