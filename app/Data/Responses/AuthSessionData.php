<?php

namespace App\Data\Responses;

use App\Models\User;
use Spatie\LaravelData\Data;

class AuthSessionData extends Data
{
    public function __construct(
        public int $customer_id,
        public string $name,
        public string $email,
    ) {}

    public static function fromUser(User $user): self
    {
        return new self(
            customer_id: $user->id,
            name: $user->name,
            email: $user->email,
        );
    }
}
