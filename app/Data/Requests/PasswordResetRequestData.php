<?php

namespace App\Data\Requests;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Data;

class PasswordResetRequestData extends Data
{
    public function __construct(
        #[Email]
        public string $email,
    ) {}
}
