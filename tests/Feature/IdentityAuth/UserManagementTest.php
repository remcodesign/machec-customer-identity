<?php

use App\Livewire\UserCreate;
use App\Livewire\UserIndex;
use App\Livewire\UserShow;
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

test('customer_admin creates a user with a role through UserCreate', function (): void {
    $admin = User::factory()->create()->assignRole(RoleName::CustomerAdmin);

    Livewire::actingAs($admin)
        ->test(UserCreate::class)
        ->set('form.name', 'Jane Doe')
        ->set('form.email', 'jane@example.com')
        ->set('form.password', 'a-strong-password')
        ->set('form.password_confirmation', 'a-strong-password')
        ->set('form.role', RoleName::DataAdmin->value)
        ->call('save')
        ->assertHasNoErrors();

    $user = User::where('email', 'jane@example.com')->sole();

    expect($user->hasRole(RoleName::DataAdmin))->toBeTrue()
        ->and(AuditLog::query()
            ->where('user_id', $admin->id)
            ->where('action', 'user.created')
            ->where('subject_type', User::class)
            ->where('subject_id', $user->id)->exists())->toBeTrue();
});

test('customer_admin updates a user\'s name and role through UserShow', function (): void {
    $admin = User::factory()->create()->assignRole(RoleName::CustomerAdmin);
    $user = User::factory()->create()->assignRole(RoleName::Customer);

    Livewire::actingAs($admin)
        ->test(UserShow::class, ['user' => $user])
        ->set('form.name', 'Updated Name')
        ->set('form.role', RoleName::DataAdmin->value)
        ->call('updateUser')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toBe('Updated Name')
        ->and($user->hasRole(RoleName::DataAdmin))->toBeTrue()
        ->and($user->hasRole(RoleName::Customer))->toBeFalse()
        ->and(AuditLog::query()
            ->where('user_id', $admin->id)
            ->where('action', 'user.updated')
            ->where('subject_type', User::class)
            ->where('subject_id', $user->id)->exists())->toBeTrue();
});

test('customer_admin deletes a non-admin user through UserIndex, and it writes a user.deleted usr_audit_log row', function (): void {
    $admin = User::factory()->create()->assignRole(RoleName::CustomerAdmin);
    $user = User::factory()->create()->assignRole(RoleName::Customer);

    Livewire::actingAs($admin)
        ->test(UserIndex::class)
        ->call('deleteUser', $user->id)
        ->assertHasNoErrors();

    expect(User::find($user->id))->toBeNull()
        ->and(AuditLog::query()
            ->where('user_id', $admin->id)
            ->where('action', 'user.deleted')
            ->where('subject_type', User::class)
            ->where('subject_id', $user->id)->exists())->toBeTrue();
});

test('deleting a user with addresses cascades their addresses instead of a foreign key violation', function (): void {
    $admin = User::factory()->create()->assignRole(RoleName::CustomerAdmin);
    $user = User::factory()->create()->assignRole(RoleName::Customer);
    $address = Address::factory()->for($user)->create();

    Livewire::actingAs($admin)
        ->test(UserIndex::class)
        ->call('deleteUser', $user->id)
        ->assertHasNoErrors();

    expect(User::find($user->id))->toBeNull()
        ->and(Address::find($address->id))->toBeNull();
});

test('a data_admin cannot mount UserCreate or submit UserShow\'s edit form or UserIndex\'s delete action', function (): void {
    $dataAdmin = User::factory()->create()->assignRole(RoleName::DataAdmin);
    $user = User::factory()->create()->assignRole(RoleName::Customer);

    $this->actingAs($dataAdmin)->get(route('users.create'))->assertForbidden();

    Livewire::actingAs($dataAdmin)
        ->test(UserShow::class, ['user' => $user])
        ->call('updateUser')
        ->assertForbidden();

    Livewire::actingAs($dataAdmin)
        ->test(UserIndex::class)
        ->call('deleteUser', $user->id)
        ->assertForbidden();

    expect(User::find($user->id))->not->toBeNull();
});

test('customer_admin cannot delete their own account', function (): void {
    $admin = User::factory()->create()->assignRole(RoleName::CustomerAdmin);

    Livewire::actingAs($admin)
        ->test(UserIndex::class)
        ->call('deleteUser', $admin->id)
        ->assertHasErrors(["delete-user-{$admin->id}"]);

    expect(User::find($admin->id))->not->toBeNull();
});

test('customer_admin cannot delete another user holding the CustomerAdmin role', function (): void {
    $admin = User::factory()->create()->assignRole(RoleName::CustomerAdmin);
    $otherAdmin = User::factory()->create()->assignRole(RoleName::CustomerAdmin);

    Livewire::actingAs($admin)
        ->test(UserIndex::class)
        ->call('deleteUser', $otherAdmin->id)
        ->assertHasErrors(["delete-user-{$otherAdmin->id}"]);

    expect(User::find($otherAdmin->id))->not->toBeNull();
});

test('customer_admin editing their own account cannot change their own email, even with a manipulated request', function (): void {
    $admin = User::factory()->create(['email' => 'admin@example.com'])->assignRole(RoleName::CustomerAdmin);

    Livewire::actingAs($admin)
        ->test(UserShow::class, ['user' => $admin])
        ->set('form.name', 'Still Admin')
        ->set('form.email', 'changed@example.com')
        ->set('form.role', RoleName::CustomerAdmin->value)
        ->call('updateUser')
        ->assertHasNoErrors();

    $admin->refresh();

    expect($admin->email)->toBe('admin@example.com')
        ->and($admin->name)->toBe('Still Admin');
});
