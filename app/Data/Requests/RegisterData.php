<?php

namespace App\Data\Requests;

use Illuminate\Validation\Rules\Password;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

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
    public static function rules(): array
    {
        return [
            'password' => ['confirmed', Password::default()],
        ];
    }
}
