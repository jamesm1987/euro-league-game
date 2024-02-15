<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\TeamResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TeamCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => TeamResource::collection($this->collection),
        ];
    }
}
