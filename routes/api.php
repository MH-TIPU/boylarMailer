<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriberListController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Subscriber List Routes
    Route::apiResource('lists', SubscriberListController::class);
    Route::post('lists/{list}/subscribers', [SubscriberListController::class, 'addSubscribers']);
    Route::delete('lists/{list}/subscribers', [SubscriberListController::class, 'removeSubscriber']);
    Route::put('lists/{list}/subscribers/status', [SubscriberListController::class, 'updateSubscriberStatus']);
    Route::post('lists/{list}/import', [SubscriberListController::class, 'import']);
    Route::get('lists/{list}/export', [SubscriberListController::class, 'export']);
}); 