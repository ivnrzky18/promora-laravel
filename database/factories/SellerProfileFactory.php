<?php

namespace Database\Factories;

use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SellerProfile>
 */
class SellerProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['Kuliner', 'Fashion', 'Jasa', 'Kesehatan', 'Pendidikan', 'Hiburan'];

        return [
            'user_id' => User::factory()->seller(),
            'business_name' => fake()->company(),
            'business_category' => fake()->randomElement($categories),
            'description' => fake()->paragraph(),
            'address' => fake()->address(),
            'latitude' => fake()->latitude(-8.5, -6.0),   // Indonesia range
            'longitude' => fake()->longitude(106.0, 112.0), // Indonesia range
            'logo' => null,
            'is_verified' => true,
        ];
    }

    /**
     * Indicate that the seller profile is not verified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => false,
        ]);
    }
}
