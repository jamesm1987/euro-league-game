<?php


$namespace = 'App\Http\Controllers\Backend';



Route::group(['as' => 'backend.', 'middleware' => ['auth:backend'], 'namespace' => $namespace, 'prefix' => 'backend'], function () {
    
    $dir = (__DIR__ . '/backend');
    
    require ($dir . '/users.php');
    
});