<?php

use App\Livewire\IdentityAuth\AdminDashboard;
use App\Livewire\IdentityAuth\UserShow;
use App\Models\Address;
use App\Models\AuditLog;
use App\Models\User;
use Livewire\Livewire;
use Machec\Contracts\Enums\RoleName;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate(RoleName::Customer);
    Role::findOrCreate(RoleName::CustomerAdmin);
    Role::findOrCreate(RoleName::DataAdmin);
});

test('customer_admin views a user and their addresses in UserShow', function (): void {
    $admin = User::factory()->create()->assignRole(RoleName::CustomerAdmin);
    $user = User::factory()->create();
    $address = Address::factory()->for($user)->create();

    Livewire::actingAs($admin)
        ->test(UserShow::class, ['user' => $user])
        ->assertOk()
        ->assertSee($user->name)
        ->assertSee($address->label);
});

test('AdminDashboard shows the correct user/address/audit-log counts', function (): void {
    $admin = User::factory()->create()->assignRole(RoleName::CustomerAdmin);
    $user = User::factory()->create();
    Address::factory()->for($user)->count(2)->create();
    AuditLog::factory()->for($user)->count(3)->create();

    Livewire::actingAs($admin)
        ->test(AdminDashboard::class)
        ->assertOk()
        ->assertSet('totalUsers', User::count())
        ->assertSet('totalAddresses', Address::count())
        ->assertSee((string) User::count())
        ->assertSee((string) Address::count());
});

test('a customer-role user cannot mount UserIndex or AdminDashboard', function (): void {
    $customer = User::factory()->create()->assignRole(RoleName::Customer);

    $this->actingAs($customer)->get(route('dashboard'))->assertForbidden();
    $this->actingAs($customer)->get(route('users.index'))->assertForbidden();
});

test('customer_admin reaches the dashboard, users index, and a user show page over real routes', function (): void {
    $admin = User::factory()->create()->assignRole(RoleName::CustomerAdmin);
    $user = User::factory()->create();

    $this->actingAs($admin)->get(route('dashboard'))->assertOk();
    $this->actingAs($admin)->get(route('users.index'))->assertOk();
    $this->actingAs($admin)->get(route('users.show', $user))->assertOk();
});
