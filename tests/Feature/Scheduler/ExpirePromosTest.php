<?php

namespace Tests\Feature\Scheduler;

use App\Models\Promo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpirePromosTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Active promo with end_date = yesterday → status=expired
     */
    public function test_active_promo_with_past_end_date_is_expired(): void
    {
        $promo = Promo::factory()->active()->create([
            'end_date' => now()->subDay()->toDateString(),
        ]);

        $this->artisan('promos:expire');

        $promo->refresh();
        $this->assertEquals('expired', $promo->status);
    }

    /**
     * Test 2: Active promo with end_date = tomorrow → status remains active
     */
    public function test_active_promo_with_future_end_date_remains_active(): void
    {
        $promo = Promo::factory()->active()->create([
            'end_date' => now()->addDay()->toDateString(),
        ]);

        $this->artisan('promos:expire');

        $promo->refresh();
        $this->assertEquals('active', $promo->status);
    }

    /**
     * Test 3: Active promo with end_date = yesterday and is_hot_deal=true
     * → after running command, is_hot_deal=false
     */
    public function test_expired_hot_deal_promo_has_hot_deal_flag_cleared(): void
    {
        $promo = Promo::factory()->active()->create([
            'end_date' => now()->subDay()->toDateString(),
            'is_hot_deal' => true,
        ]);

        $this->artisan('promos:expire');

        $promo->refresh();
        $this->assertEquals('expired', $promo->status);
        $this->assertFalse($promo->is_hot_deal);
    }
}
