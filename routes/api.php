<?php

use App\Http\Controllers\Api\IdentityAuth\CustomerAddressController;
use App\Http\Controllers\Api\IdentityAuth\CustomerContactController;
use App\Http\Controllers\Api\IdentityAuth\LoginController;
use App\Http\Controllers\Api\IdentityAuth\LogoutController;
use App\Http\Controllers\Api\IdentityAuth\PasswordResetRequestController;
use App\Http\Controllers\Api\IdentityAuth\RegisterController;
use App\Http\Controllers\Api\IdentityAuth\WhoamiController;
use App\Http\Middleware\RecordServiceTokenUsageMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/auth')->group(function (): void {
    Route::post('login', LoginController::class)->middleware(['throttle:login', 'stateful-origin']);
    Route::post('register', RegisterController::class)->middleware('stateful-origin');
    Route::post('logout', LogoutController::class)->middleware('throttle:login');
});

Route::post('v1/password/reset-request', PasswordResetRequestController::class)->middleware('throttle:login');

Route::prefix('internal/v1')->middleware(['auth:sanctum', RecordServiceTokenUsageMiddleware::class])->group(function (): void {
    Route::get('whoami', WhoamiController::class);
    Route::get('customers/{customer}/addresses/{address}', CustomerAddressController::class)
        ->whereNumber(['customer', 'address'])
        ->middleware('abilities:identity:addresses:read');
    Route::get('customers/{customer}', CustomerContactController::class)
        ->whereNumber('customer')
        ->middleware('abilities:identity:customers:read');
});
