<?php

use App\Http\Controllers\Api\CustomerAddressController;
use App\Http\Controllers\Api\CustomerContactController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\PasswordResetRequestController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\WhoamiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/auth')->group(function (): void {
    Route::post('login', LoginController::class)->middleware(['throttle:login', 'stateful-origin']);
    Route::post('register', RegisterController::class)->middleware('stateful-origin');
    Route::post('logout', LogoutController::class)->middleware('throttle:login');
});

Route::post('v1/password/reset-request', PasswordResetRequestController::class)->middleware('throttle:login');

Route::prefix('internal/v1')->middleware('auth:sanctum')->group(function (): void {
    Route::get('whoami', WhoamiController::class);
    Route::get('customers/{customer}/addresses/{address}', CustomerAddressController::class)
        ->whereNumber(['customer', 'address'])
        ->middleware('abilities:identity:addresses:read');
    Route::get('customers/{customer}', CustomerContactController::class)
        ->whereNumber('customer')
        ->middleware('abilities:identity:customers:read');
});
