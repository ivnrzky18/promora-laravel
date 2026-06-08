<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\SellerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $eventDate = fake()->dateTimeBetween('now', '+30 days');

        return [
            'seller_id'    => SellerProfile::factory(),
            'title'        => fake()->sentence(4),
            'description'  => fake()->paragraph(),
            'location'     => fake()->address(),
            'event_date'   => $eventDate,
            'end_date'     => fake()->dateTimeBetween($eventDate, '+35 days'),
            'poster_image' => null,
            'status'       => 'draft',
        ];
    }

    /**
     * Indicate that the event is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the event is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
