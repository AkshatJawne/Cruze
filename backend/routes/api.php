<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\DriverController; 
use App\Http\Controllers\TripController; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'submit']);
Route::post('/login/verify', [LoginController::class, 'verify']);

Route::group(['middleware' => 'auth:sanctum'], function () {

    // to get the driver information
    Route::get('/driver', [DriverController::class, 'show']);
    // to update the driver information
    Route::post('/driver', [DriverController::class, 'update']);

    Route::post('/trip', [TripController::class, 'store']);
    Route::get('trip/{trip}', [TripController::class, 'show']);
    Route::post('trip/{trip}/accept', [TripController::class, 'accept']);
    Route::post('trip/{trip}/start', [TripController::class, 'start']);
    Route::post('trip/{trip}/end', [TripController::class, 'end']);
    Route::post('trip/{trip}/location', [TripController::class, 'location']);


    // to get the user information
    Route::get('/user', function (Request $request) {
    return $request->user();
    });
});


