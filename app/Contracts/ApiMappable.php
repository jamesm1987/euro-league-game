<?php

namespace App\Contracts;


interface ApiMappable
{
    public static function mappingkeys();

    public function mapApiToModel($apiRequests);
}