<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Result extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fixture_id',
        'home_team_score',
        'away_team_score',
    ];

    public function fixture()
    {
        return $this->belongsTo(Fixture::class, 'fixture_id');
    }

    public function homeTeam()
    {
        return $this->fixture->homeTeam;
    }

    public function awayTeam()
    {
        return $this->fixture->awayTeam;
    }

    protected function finalScore(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value) => "{$this->home_team_score} - {$this->away_team_score}"
        );
    }

    public function mapApiToModel($requests)
    {
        dd($requests);
    }
}

