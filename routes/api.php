<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TripController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * User Routes
 */
Route::middleware(['throttle:5,1'])->group(function(){
    Route::post('login', [AuthController::class, 'login'])->name('login');
});

Route::post('register', [AuthController::class, 'register']);

/**
 * Trip Routes
 */
Route::middleware(['auth:sanctum'])->group( function () {
    Route::resource('trip', TripController::class);

    Route::get('trips', [TripController::class, 'terms']);

    Route::get('trip/reserve/{trip}', [TripController::class, 'reserve']);
    
    Route::get('trip/price/{start_price}/{end_price?}', [TripController::class, 'priceRange']);

});

Route::fallback(function(){
    return response()->json([
        'message' => 'Route Not Found.'], 404);
});