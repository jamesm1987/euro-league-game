<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

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


    public function type()
    {
        return Str::snake(class_basename($this->requestable_type));
    }

    public static function teams()
    {
        return self::where('request_type', 'team')->pluck('response')->map(function($leagues, $key) {

            return Arr::map(json_decode($leagues), function($obj, $key) {
               
                return ['id' => $obj->team->id, 'name' => $obj->team->name];
            });

        });
    }

    

    
}

