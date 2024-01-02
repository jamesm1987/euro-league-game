<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;


class Team extends Model
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

}
