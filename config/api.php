<?php

return [

    /*
    |--------------------------------------------------------------------------
    | New York Times API
    |--------------------------------------------------------------------------
    |
    | New York Times API credentials..
    |
    */

    'nytimes_api_endpoint' => env('NY_TIMES_API_ENDPOINT', 'https://api.nytimes.com/svc/books/v3'),
    'nytimes_api_key' => env('NY_TIMES_API_KEY', null),

    /*
    |--------------------------------------------------------------------------
    | New York Times API Overview Endpoint
    |--------------------------------------------------------------------------
    |
    | One of 2 endpoints for the New York Times Best Sellers API.
    |
    */

    'nytimes_api_overview_endpoint' => env('NY_TIMES_API_OVERVIEW_ENDPOINT', '/lists/overview.json'),

    /*
    |--------------------------------------------------------------------------
    | New York Times API Throttle Limit
    |--------------------------------------------------------------------------
    |
    | The number of requests per minute for the New York Times Best Sellers API.
    |
    */

    'nytimes_api_throttle_limit' => env('NY_TIMES_API_THROTTLE_LIMIT', 30),
];
