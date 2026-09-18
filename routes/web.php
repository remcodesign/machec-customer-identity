<?php

use App\Livewire\IdentityAuth\AdminDashboard;
use App\Livewire\IdentityAuth\ServiceClientCreate;
use App\Livewire\IdentityAuth\ServiceClientIndex;
use App\Livewire\IdentityAuth\UserCreate;
use App\Livewire\IdentityAuth\UserIndex;
use App\Livewire\IdentityAuth\UserShow;
use Illuminate\Support\Facades\Route;
use Machec\Contracts\Enums\RoleName;
use Spatie\Permission\Middleware\RoleMiddleware;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified', RoleMiddleware::using([RoleName::CustomerAdmin, RoleName::DataAdmin])])
    ->group(function (): void {
        Route::livewire('dashboard', AdminDashboard::class)->name('dashboard');
        Route::livewire('users', UserIndex::class)->name('users.index');

        Route::middleware(RoleMiddleware::using(RoleName::CustomerAdmin))
            ->group(function (): void {
                Route::livewire('users/create', UserCreate::class)->name('users.create');

                Route::livewire('service-clients', ServiceClientIndex::class)->name('service-clients.index');
                Route::livewire('service-clients/create', ServiceClientCreate::class)->name('service-clients.create');
            });

        Route::livewire('users/{user}', UserShow::class)->name('users.show')->whereNumber('user');
    });

require __DIR__.'/settings.php';
