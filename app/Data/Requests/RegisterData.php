<?php

namespace App\Data\Requests;

use Illuminate\Validation\Rules\Password;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class RegisterData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $name,
        #[Email, Max(255)]
        public string $email,
        public string $password,
    ) {}

    /**
     * @return array<string, array<int, mixed>>
     */
    public static function rules(?ValidationContext $context = null): array
    {
        return [
            'password' => ['confirmed', Password::default()],
        ];
    }
}
