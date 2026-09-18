<?php

use App\Models\Address;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('whoami resolves the authenticated customer from the forwarded Sanctum cookie', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/internal/v1/whoami');

    $response->assertOk()->assertJson([
        'customer_id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
    ]);
});

test('the addresses endpoint returns the full address for a customer_id/address_id pair the token is scoped to read', function (): void {
    $user = User::factory()->create();
    $address = Address::factory()->for($user)->create();
    Sanctum::actingAs($user, ['identity:addresses:read']);

    $response = $this->getJson("/api/internal/v1/customers/{$user->id}/addresses/{$address->id}");

    $response->assertOk()->assertJson([
        'label' => $address->label,
        'line1' => $address->line1,
        'city' => $address->city,
        'postal_code' => $address->postal_code,
        'country_code' => $address->country_code,
    ]);
});

test('the customers endpoint returns name and email only for OrderShow', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['identity:customers:read']);

    $response = $this->getJson("/api/internal/v1/customers/{$user->id}");

    $response->assertOk()
        ->assertExactJson(['name' => $user->name, 'email' => $user->email]);
});

test('the addresses endpoint 404s, not 403s, when the address does not belong to the given customer_id', function (): void {
    $owner = User::factory()->create();
    $otherCustomer = User::factory()->create();
    $address = Address::factory()->for($owner)->create();
    Sanctum::actingAs($owner, ['identity:addresses:read']);

    $response = $this->getJson("/api/internal/v1/customers/{$otherCustomer->id}/addresses/{$address->id}");

    $response->assertNotFound();
});

test('rejects a call whose token lacks the identity:addresses:read or identity:customers:read ability', function (): void {
    $user = User::factory()->create();
    $address = Address::factory()->for($user)->create();
    Sanctum::actingAs($user, ['some:other:ability']);

    $this->getJson("/api/internal/v1/customers/{$user->id}/addresses/{$address->id}")->assertForbidden();
    $this->getJson("/api/internal/v1/customers/{$user->id}")->assertForbidden();
});

test('rejects non-numeric customer_id/address_id route segments with 404 before the ability check ever runs', function (): void {
    Sanctum::actingAs(User::factory()->create(), ['identity:addresses:read', 'identity:customers:read']);

    $this->getJson('/api/internal/v1/customers/not-a-number/addresses/1')->assertNotFound();
    $this->getJson('/api/internal/v1/customers/1/addresses/not-a-number')->assertNotFound();
    $this->getJson('/api/internal/v1/customers/not-a-number')->assertNotFound();
});
