<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Machec\Contracts\Enums\RoleName;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Route::middleware(['web', 'auth', 'role:'.RoleName::CustomerAdmin->value])
        ->get('/__test/customer-admin-only', fn () => 'ok');
});

test('attaches a role to a user via the pivot table', function (): void {
    Role::findOrCreate(RoleName::CustomerAdmin);
    $user = User::factory()->create();

    $user->assignRole(RoleName::CustomerAdmin);

    expect($user->hasRole(RoleName::CustomerAdmin))->toBeTrue()
        ->and(DB::table('model_has_roles')->where('model_id', $user->id)->exists())->toBeTrue();
});

test('logs in as the seeded customer_admin account and passes a customer_admin-gated role check', function (): void {
    $this->seed(DatabaseSeeder::class);
    $admin = User::role(RoleName::CustomerAdmin)->firstOrFail();

    $response = $this->actingAs($admin)->get('/__test/customer-admin-only');

    $response->assertOk();
});

test('a user without any role fails an admin gate check', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/__test/customer-admin-only');

    $response->assertForbidden();
});

test('rejects a customer-role token calling an internal admin endpoint with 403', function (): void {
    Role::findOrCreate(RoleName::Customer);
    $user = User::factory()->create();
    $user->assignRole(RoleName::Customer);

    $response = $this->actingAs($user)->get('/__test/customer-admin-only');

    $response->assertForbidden();
});
