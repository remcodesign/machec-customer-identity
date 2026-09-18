<?php

use App\Livewire\AdminDashboard;
use App\Livewire\UserIndex;
use App\Livewire\UserShow;
use Illuminate\Support\Facades\Route;
use Machec\Contracts\Enums\RoleName;
use Spatie\Permission\Middleware\RoleMiddleware;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified', RoleMiddleware::using([RoleName::CustomerAdmin, RoleName::DataAdmin])])
    ->group(function (): void {
        Route::livewire('dashboard', AdminDashboard::class)->name('dashboard');
        Route::livewire('users', UserIndex::class)->name('users.index');
        Route::livewire('users/{user}', UserShow::class)->name('users.show');
    });

require __DIR__.'/settings.php';
