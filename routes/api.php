<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\RegisterController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/auth')->group(function (): void {
    Route::post('login', LoginController::class)->middleware(['throttle:login', 'stateful-origin']);
    Route::post('register', RegisterController::class)->middleware('stateful-origin');
    Route::post('logout', LogoutController::class)->middleware('throttle:login');
});
