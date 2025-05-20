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

    /*
    |--------------------------------------------------------------------------
    | New York Times API Refresh Time
    |--------------------------------------------------------------------------
    |
    | Configuration for when the NYTimes Best Sellers list refreshes.
    | Day should be one of: SUNDAY, MONDAY, TUESDAY, WEDNESDAY, THURSDAY, FRIDAY, SATURDAY
    | Time should be in 24-hour format (HH:mm)
    |
    | NYT currently refreshes on Wednesday at 7:00 PM EST.
    |
    */

    'nytimes_refresh_day' => env('NY_TIMES_REFRESH_DAY', 'WEDNESDAY'),
    'nytimes_refresh_time' => env('NY_TIMES_REFRESH_TIME', '19:00'),
    'nytimes_timezone' => env('NY_TIMES_TIMEZONE', 'America/New_York'),
];
