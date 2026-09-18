<?php

use App\Models\User;

test('an admin logging in through the web login screen writes a login usr_audit_log row', function (): void {
    $admin = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'email' => $admin->email,
        'password' => 'password',
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertAuthenticated();

    $this->assertDatabaseHas('usr_audit_log', [
        'user_id' => $admin->id,
        'action' => 'login',
        'subject_type' => User::class,
        'subject_id' => $admin->id,
    ]);
});

test('a failed web login attempt writes no usr_audit_log row', function (): void {
    $admin = User::factory()->create();

    $this->post(route('login.store'), [
        'email' => $admin->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
    $this->assertDatabaseMissing('usr_audit_log', [
        'user_id' => $admin->id,
    ]);
});

test('an admin logging out through the web logout route writes a logout usr_audit_log row', function (): void {
    $admin = User::factory()->create();

    $this->actingAs($admin)->post(route('logout'));

    $this->assertGuest();
    $this->assertDatabaseHas('usr_audit_log', [
        'user_id' => $admin->id,
        'action' => 'logout',
        'subject_type' => User::class,
        'subject_id' => $admin->id,
    ]);
});
