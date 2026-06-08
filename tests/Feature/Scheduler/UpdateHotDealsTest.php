<?php

namespace Tests\Feature\Scheduler;

use App\Models\Promo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateHotDealsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Active promo ending within 24h → is_hot_deal=true
     * (within the 48h window)
     */
    public function test_active_promo_ending_within_24h_is_marked_as_hot_deal(): void
    {
        $promo = Promo::factory()->active()->create([
            'end_date' => now()->addHours(24)->toDateString(),
            'is_hot_deal' => false,
        ]);

        $this->artisan('promos:update-hot-deals');

        $promo->refresh();
        $this->assertTrue($promo->is_hot_deal);
    }

    /**
     * Test 2: Active promo ending in 72h → is_hot_deal=false
     * (outside the 48h window)
     */
    public function test_active_promo_ending_in_72h_is_not_marked_as_hot_deal(): void
    {
        $promo = Promo::factory()->active()->create([
            'end_date' => now()->addHours(72)->toDateString(),
            'is_hot_deal' => false,
        ]);

        $this->artisan('promos:update-hot-deals');

        $promo->refresh();
        $this->assertFalse($promo->is_hot_deal);
    }

    /**
     * Test 3: Expired promo with end_date within 24h → is_hot_deal=false
     * (not active, so should not be marked as hot deal)
     */
    public function test_expired_promo_is_not_marked_as_hot_deal_even_if_end_date_is_soon(): void
    {
        $promo = Promo::factory()->create([
            'status' => 'expired',
            'end_date' => now()->addHours(24)->toDateString(),
            'is_hot_deal' => false,
        ]);

        $this->artisan('promos:update-hot-deals');

        $promo->refresh();
        $this->assertFalse($promo->is_hot_deal);
    }

    /**
     * Test 4: Active promo previously is_hot_deal=true but end_date now > 48h
     * → after running command, is_hot_deal=false (reset)
     */
    public function test_previously_hot_deal_promo_is_reset_when_end_date_exceeds_48h(): void
    {
        $promo = Promo::factory()->active()->create([
            'end_date' => now()->addHours(72)->toDateString(),
            'is_hot_deal' => true,
        ]);

        $this->artisan('promos:update-hot-deals');

        $promo->refresh();
        $this->assertFalse($promo->is_hot_deal);
    }
}
