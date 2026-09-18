<?php

use App\Livewire\IdentityAuth\AddressForm;
use App\Models\Address;
use App\Models\AuditLog;
use App\Models\User;
use Livewire\Livewire;
use Machec\Contracts\Enums\RoleName;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate(RoleName::CustomerAdmin);
});

test('customer_admin creating an address writes a usr_audit_log row attributed to the admin', function (): void {
    $admin = User::factory()->create()->assignRole(RoleName::CustomerAdmin);
    $user = User::factory()->create();

    Livewire::actingAs($admin)
        ->test(AddressForm::class, ['user' => $user])
        ->set('form.label', 'Home')
        ->set('form.line1', 'Main Street 1')
        ->set('form.city', 'Amsterdam')
        ->set('form.postal_code', '1000AA')
        ->set('form.country_code', 'NL')
        ->call('save')
        ->assertDispatched('address-saved');

    $address = $user->addresses()->sole();

    expect(AuditLog::query()
        ->where('user_id', $admin->id)
        ->where('action', 'address.created')
        ->where('subject_type', Address::class)
        ->where('subject_id', $address->id)
        ->exists()
    )->toBeTrue();
});

test('customer_admin updating an address writes an address.updated usr_audit_log row', function (): void {
    $admin = User::factory()->create()->assignRole(RoleName::CustomerAdmin);
    $user = User::factory()->create();
    $address = Address::factory()->for($user)->create();

    Livewire::actingAs($admin)
        ->test(AddressForm::class, ['user' => $user, 'address' => $address])
        ->set('form.city', 'Rotterdam')
        ->call('save');

    expect(AuditLog::query()
        ->where('user_id', $admin->id)
        ->where('action', 'address.updated')
        ->where('subject_type', Address::class)
        ->where('subject_id', $address->id)
        ->exists()
    )->toBeTrue();
});
