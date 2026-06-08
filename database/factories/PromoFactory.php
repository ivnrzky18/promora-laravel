<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Promo;
use App\Models\SellerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Promo>
 */
class PromoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $originalPrice = fake()->randomFloat(2, 50000, 500000);
        $discountPct = fake()->randomFloat(2, 5, 70);
        $promoPrice = round($originalPrice * (1 - $discountPct / 100), 2);

        return [
            'seller_id' => SellerProfile::factory(),
            'category_id' => Category::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'poster_image' => null,
            'discount_percentage' => $discountPct,
            'original_price' => $originalPrice,
            'promo_price' => $promoPrice,
            'start_date' => now()->subDays(3)->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'is_hot_deal' => false,
            'view_count' => 0,
            'status' => 'draft',
        ];
    }

    /**
     * Indicate that the promo is in draft status.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Indicate that the promo is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
        ]);
    }

    /**
     * Indicate that the promo is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'start_date' => now()->subDays(10)->toDateString(),
            'end_date' => now()->subDay()->toDateString(),
        ]);
    }

    /**
     * Indicate that the promo is a hot deal (active, ending within 24 hours).
     */
    public function hotDeal(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addHours(24)->toDateString(),
            'is_hot_deal' => true,
        ]);
    }
}
