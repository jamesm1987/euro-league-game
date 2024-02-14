<?php

namespace App\Traits\Models;
use App\Models\ApiRequest;

trait HasApiRequests

{
    public function mapApiToModel($requests)
    {
        $model = new static();

        $query = $model->newQuery();
        
        foreach($requests as $request) {
    
            foreach ($request->toArray() as $item) {
                foreach($model::mappingKeys() as $property => $column) {
                    $query->where($column, $item['response'][$property]['name']);
                }
            }
        }
    }
}