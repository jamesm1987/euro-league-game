<?php

namespace App\Traits\Models;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use App\Models\ApiRequest;

trait HasApiRequests

{
    public function request(): MorphOne
    {
        return $this->morphOne(ApiRequest::class, 'requestable');
    }
}