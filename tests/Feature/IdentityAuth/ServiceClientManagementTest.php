<?php

use App\Enums\ServiceAbility;
use App\Livewire\IdentityAuth\ServiceClientCreate;
use App\Livewire\IdentityAuth\ServiceClientIndex;
use App\Models\AuditLog;
use App\Models\ServiceClient;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Livewire\Livewire;
use Machec\Contracts\Enums\RoleName;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Role::findOrCreate(RoleName::Customer);
    Role::findOrCreate(RoleName::CustomerAdmin);
    Role::findOrCreate(RoleName::DataAdmin);
});

test('customer_admin issues a new service token and sees the plaintext exactly once', function (): void {
    $admin = User::factory()->create()->assignRole(RoleName::CustomerAdmin);

    $component = Livewire::actingAs($admin)
        ->test(ServiceClientCreate::class)
        ->set('label', 'commercial-core prod')
        ->set('ability', ServiceAbility::IdentityAddressesRead->value)
        ->call('save')
        ->assertHasNoErrors();

    $plaintextToken = $component->get('plaintextToken');

    expect($plaintextToken)->toBeString()->and($plaintextToken)->toContain('|');
    $component->assertSee($plaintextToken);

    $client = ServiceClient::where('label', 'commercial-core prod')->sole();
    $token = $client->tokens()->sole();

    expect($token->abilities)->toBe([ServiceAbility::IdentityAddressesRead->value])
        ->and($token->token_prefix)->not->toBeNull()
        ->and($token->token_prefix)->toHaveLength(8)
        ->and(AuditLog::query()
            ->where('user_id', $admin->id)
            ->where('action', 'service_client.token_issued')
            ->where('subject_type', PersonalAccessToken::class)
            ->where('subject_id', $token->id)->exists())->toBeTrue();
});

test('ServiceClientIndex shows the masked token_prefix, last_used_at, and usage_count for each token', function (): void {
    $admin = User::factory()->create()->assignRole(RoleName::CustomerAdmin);
    $client = ServiceClient::create(['label' => 'pim-core prod']);
    $newToken = $client->createToken('pim-core prod', [ServiceAbility::IdentityAddressesRead->value]);
    $accessToken = $newToken->accessToken;
    $accessToken->token_prefix = 'abcd1234';
    $accessToken->usage_count = 5;
    $accessToken->last_used_at = now();
    $accessToken->save();

    Livewire::actingAs($admin)
        ->test(ServiceClientIndex::class)
        ->assertSee('pim-core prod')
        ->assertSee('abcd1234')
        ->assertSee('5');
});

test('usage_count increments on each authenticated call to an internal/v1 endpoint', function (): void {
    $client = ServiceClient::create(['label' => 'wms-prod']);
    $newToken = $client->createToken('wms-prod', [ServiceAbility::IdentityCustomersRead->value]);
    $target = User::factory()->create();

    $this->withHeader('Authorization', 'Bearer '.$newToken->plainTextToken)
        ->getJson("/api/internal/v1/customers/{$target->id}")
        ->assertOk();

    expect($newToken->accessToken->fresh()->usage_count)->toBe(1);

    $this->withHeader('Authorization', 'Bearer '.$newToken->plainTextToken)
        ->getJson("/api/internal/v1/customers/{$target->id}")
        ->assertOk();

    expect($newToken->accessToken->fresh()->usage_count)->toBe(2);
});

test('revoking a token immediately rejects further calls that present it', function (): void {
    $admin = User::factory()->create()->assignRole(RoleName::CustomerAdmin);
    $client = ServiceClient::create(['label' => 'wms-prod']);
    $newToken = $client->createToken('wms-prod', [ServiceAbility::IdentityCustomersRead->value]);
    $target = User::factory()->create();

    $this->withHeader('Authorization', 'Bearer '.$newToken->plainTextToken)
        ->getJson("/api/internal/v1/customers/{$target->id}")
        ->assertOk();

    $tokenId = $newToken->accessToken->id;

    Livewire::actingAs($admin)
        ->test(ServiceClientIndex::class)
        ->call('revokeToken', $tokenId);

    expect(PersonalAccessToken::find($tokenId))->toBeNull()
        ->and(AuditLog::query()
            ->where('user_id', $admin->id)
            ->where('action', 'service_client.token_revoked')
            ->where('subject_type', PersonalAccessToken::class)
            ->where('subject_id', $tokenId)->exists())->toBeTrue();

    $this->withHeader('Authorization', 'Bearer '.$newToken->plainTextToken)
        ->getJson("/api/internal/v1/customers/{$target->id}")
        ->assertUnauthorized();
});

test('deleting a ServiceClient revokes every token it owns', function (): void {
    $admin = User::factory()->create()->assignRole(RoleName::CustomerAdmin);
    $client = ServiceClient::create(['label' => 'commercial-core prod']);
    $tokenA = $client->createToken('a', [ServiceAbility::IdentityAddressesRead->value]);
    $tokenB = $client->createToken('b', [ServiceAbility::IdentityCustomersRead->value]);

    Livewire::actingAs($admin)
        ->test(ServiceClientIndex::class)
        ->call('deleteClient', $client->id);

    expect(PersonalAccessToken::find($tokenA->accessToken->id))->toBeNull()
        ->and(PersonalAccessToken::find($tokenB->accessToken->id))->toBeNull()
        ->and(ServiceClient::find($client->id))->toBeNull()
        ->and(AuditLog::query()
            ->where('user_id', $admin->id)
            ->where('action', 'service_client.deleted')
            ->where('subject_type', ServiceClient::class)
            ->where('subject_id', $client->id)->exists())->toBeTrue();
});

test('a data_admin cannot mount ServiceClientCreate or ServiceClientIndex', function (): void {
    $dataAdmin = User::factory()->create()->assignRole(RoleName::DataAdmin);

    $this->actingAs($dataAdmin)->get(route('service-clients.index'))->assertForbidden();
    $this->actingAs($dataAdmin)->get(route('service-clients.create'))->assertForbidden();
});

test('rejects an ability not in this app\'s own ServiceAbility list', function (): void {
    $admin = User::factory()->create()->assignRole(RoleName::CustomerAdmin);

    Livewire::actingAs($admin)
        ->test(ServiceClientCreate::class)
        ->set('label', 'bad-client')
        ->set('ability', 'stock:movements:write')
        ->call('save')
        ->assertHasErrors(['ability']);

    expect(ServiceClient::where('label', 'bad-client')->exists())->toBeFalse();
});
