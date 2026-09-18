<?php

namespace App\Data\Responses;

use App\Models\Address;
use Spatie\LaravelData\Data;

class AddressData extends Data
{
    public function __construct(
        public string $label,
        public string $line1,
        public ?string $line2,
        public string $city,
        public string $postal_code,
        public string $country_code,
    ) {}

    public static function fromAddress(Address $address): self
    {
        return new self(
            label: $address->label,
            line1: $address->line1,
            line2: $address->line2,
            city: $address->city,
            postal_code: $address->postal_code,
            country_code: $address->country_code,
        );
    }
}
