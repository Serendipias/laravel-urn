<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Model namespaces
    |--------------------------------------------------------------------------
    |
    | Declare the models' namespace to allow to get the model instance by urn.
    |
    */
    'models_namespace' => 'App\Models',

    /*
   |--------------------------------------------------------------------------
   | Urn service
   |--------------------------------------------------------------------------
   |
   | Declare the current service creating the urn, this is useful when having
   | more than one service that persist data.
   |
   */
    'service' => 'laravel',

    /*
   |--------------------------------------------------------------------------
   | Urn stage
   |--------------------------------------------------------------------------
   |
   | Use this to be able to distinguish the resource origin.
   |
   */
    'stage' => env('APP_ENV', 'local'),
];
