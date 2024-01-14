<?php

namespace App\Traits\Models;
use App\Models\ApiRequest;
use Illuminate\Support\Facades\Schema;

trait HasApiRequests

{
    public function mapApiToModel($requests)
    {
        $model = new (self::class);

        $query = $model->newQuery();

        dd($requests);
        // $type = strtolower(class_basename($model));

        // foreach ($request as $request) {

        // if (Schema::hasColumn($model->getTable(), 'country')) {
        //     $query->where('country', $request->response['country']['name']);
        // }

        

        foreach($requests as $request) {
    
            foreach ($request->toArray() as $item) {
                // dd($item['response']['country']['name']);
                foreach($model::mappingKeys() as $property => $column) {
                    
                    $query->where($column, $item['response'][$property]['name']);
                }


            }
        }

            dd($query->first());
        // foreach ($requests as $request) {
        //     foreach ($model::mappingKeys() as $key) {
                
        //         $temp[] = $request->response[$key];
        //     }

        // dd($temp);
        // var_dump($request->response[$type]);
        // $query->where('name', $request->response[$type]['name']);

        
        // $match = $query->first();

        // if ($match) {
                    
        //     $match->update([
        //         'api_id' => $request->response[$type]['id']
        //     ]);
            
        //     return true;
        // } 

        // return false;


    }

}