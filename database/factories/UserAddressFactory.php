<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAddress>
 */
class UserAddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'recipient_name' => $this->faker->name(),
            'phone_number' => $this->faker->phoneNumber(),
            'label' => 'Rumah',
            'city_id' => $this->faker->numberBetween(1, 500), // Random ID Kota
            'full_address' => $this->faker->address(),
            'postal_code' => $this->faker->postcode(),
            'is_primary' => true,
        ];
    }
}
