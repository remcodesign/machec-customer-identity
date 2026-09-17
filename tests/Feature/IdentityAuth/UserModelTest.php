<?php

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\QueryException;

test("a user's addresses relationship only returns that user's own addresses", function (): void {
    $user = User::factory()->create();
    $addresses = Address::factory()->count(2)->for($user)->create();
    Address::factory()->count(2)->for(User::factory())->create();

    expect($user->addresses()->pluck('id')->all())->toEqualCanonicalizing($addresses->pluck('id')->all());
});

test('rejects creating an address for a user_id that does not exist', function (): void {
    expect(fn () => Address::factory()->create(['user_id' => 999_999]))
        ->toThrow(QueryException::class);
});
