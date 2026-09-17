<?php

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;

test('creates users, usr_addresses and usr_audit_log tables', function (): void {
    expect(Schema::hasTable('users'))->toBeTrue()
        ->and(Schema::hasColumn('users', 'deleted_at'))->toBeTrue()
        ->and(Schema::hasTable('usr_addresses'))->toBeTrue()
        ->and(Schema::hasColumns('usr_addresses', [
            'user_id', 'label', 'line1', 'line2', 'city', 'postal_code', 'country_code', 'is_default_shipping',
        ]))->toBeTrue()
        ->and(Schema::hasTable('usr_audit_log'))->toBeTrue()
        ->and(Schema::hasColumns('usr_audit_log', [
            'user_id', 'action', 'subject_type', 'subject_id', 'ip_address', 'created_at',
        ]))->toBeTrue();
});

test('rejects a duplicate email at the database level', function (): void {
    User::factory()->create(['email' => 'duplicate@example.com']);

    expect(fn () => User::factory()->create(['email' => 'duplicate@example.com']))
        ->toThrow(QueryException::class);
});
