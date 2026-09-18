<?php

use App\Models\User;
use Machec\Contracts\Enums\RoleName;
use Spatie\Permission\Models\Role;

test('guests are redirected to the login page', function (): void {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('customer_admin can visit the dashboard', function (): void {
    Role::findOrCreate(RoleName::CustomerAdmin);
    $user = User::factory()->create()->assignRole(RoleName::CustomerAdmin);
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('an authenticated user without an admin role cannot visit the dashboard', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertForbidden();
});
