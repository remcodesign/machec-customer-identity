<?php

use App\Models\User;
use Machec\Contracts\Enums\RoleName;
use Spatie\Permission\Models\Role;

test('issues a stateful session cookie after valid login from the BFF origin', function (): void {
    $user = User::factory()->create(['password' => 'correct-password']);

    $response = $this->withHeader('Origin', 'http://localhost:3000')
        ->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'correct-password',
        ]);

    $response->assertOk()
        ->assertJson([
            'customer_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ])
        ->assertCookie(config('session.cookie'));

    $this->assertAuthenticatedAs($user);
    $this->assertDatabaseHas('usr_audit_log', [
        'user_id' => $user->id,
        'action' => 'login',
    ]);
});

test('registering creates a user with the customer role and issues a session, no separate login required', function (): void {
    Role::findOrCreate(RoleName::Customer);

    $response = $this->withHeader('Origin', 'http://localhost:3000')
        ->postJson('/api/v1/auth/register', [
            'name' => 'New Customer',
            'email' => 'new-customer@example.com',
            'password' => 'a-strong-password',
            'password_confirmation' => 'a-strong-password',
        ]);

    $response->assertOk()
        ->assertJson(['name' => 'New Customer', 'email' => 'new-customer@example.com'])
        ->assertCookie(config('session.cookie'));

    $user = User::whereEmail('new-customer@example.com')->firstOrFail();

    expect($user->hasRole(RoleName::Customer))->toBeTrue();
    $this->assertAuthenticatedAs($user);
    $this->assertDatabaseHas('usr_audit_log', [
        'user_id' => $user->id,
        'action' => 'registered',
    ]);
});

test('logging out invalidates the session and writes a usr_audit_log row', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->withHeader('Origin', 'http://localhost:3000')
        ->postJson('/api/v1/auth/logout');

    $response->assertNoContent();
    $this->assertGuest();
    $this->assertDatabaseHas('usr_audit_log', [
        'user_id' => $user->id,
        'action' => 'logout',
    ]);
});

test('rejects login from an origin outside the configured stateful domains', function (): void {
    $user = User::factory()->create(['password' => 'correct-password']);

    $response = $this->withHeader('Origin', 'https://attacker.test')
        ->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'correct-password',
        ]);

    $response->assertForbidden();
    $this->assertGuest();
});

test("rejects registration when password and password_confirmation don't match", function (): void {
    $response = $this->withHeader('Origin', 'http://localhost:3000')
        ->postJson('/api/v1/auth/register', [
            'name' => 'New Customer',
            'email' => 'mismatched@example.com',
            'password' => 'a-strong-password',
            'password_confirmation' => 'a-different-password',
        ]);

    $response->assertUnprocessable()->assertJsonValidationErrors('password');
    $this->assertDatabaseMissing('users', ['email' => 'mismatched@example.com']);
});

test('rejects registration with an email that already exists in users', function (): void {
    $existing = User::factory()->create();

    $response = $this->withHeader('Origin', 'http://localhost:3000')
        ->postJson('/api/v1/auth/register', [
            'name' => 'New Customer',
            'email' => $existing->email,
            'password' => 'a-strong-password',
            'password_confirmation' => 'a-strong-password',
        ]);

    $response->assertUnprocessable()->assertJsonValidationErrors('email');
});
