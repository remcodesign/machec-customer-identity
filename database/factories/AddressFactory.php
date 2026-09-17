<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'label' => fake()->randomElement(['Home', 'Work']),
            'line1' => fake()->streetAddress(),
            'line2' => null,
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'country_code' => fake()->countryCode(),
            'is_default_shipping' => false,
        ];
    }
}
