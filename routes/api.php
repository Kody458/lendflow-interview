<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\NYTBestSellersController;

Route::group(['prefix' => 'v1'], function () {
    Route::get('nyt/bestsellers/overview', [NYTBestSellersController::class, 'overview'])
        ->middleware('throttle:nyt-overview');
});
