<?php

namespace App\Data\Responses;

use App\Models\User;
use Spatie\LaravelData\Data;

class CustomerContactData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
    ) {}

    public static function fromUser(User $user): self
    {
        return new self(
            name: $user->name,
            email: $user->email,
        );
    }
}
