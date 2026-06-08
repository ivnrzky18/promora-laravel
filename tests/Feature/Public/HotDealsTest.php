<?php

namespace Tests\Feature\Public;

use App\Models\Category;
use App\Models\Promo;
use App\Models\SellerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HotDealsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that GET /hot-deals returns HTTP 200.
     */
    public function test_hot_deals_page_returns_200(): void
    {
        $response = $this->get(route('hot-deals'));

        $response->assertStatus(200);
    }

    /**
     * Test that only active promos with end_date within 48 hours appear.
     */
    public function test_active_promo_ending_within_48_hours_appears(): void
    {
        $category = Category::factory()->create();
        $seller   = SellerProfile::factory()->create();

        // Active promo ending in 24 hours — should appear
        Promo::factory()->active()->create([
            'seller_id'   => $seller->id,
            'category_id' => $category->id,
            'title'       => 'Promo Berakhir 24 Jam',
            'end_date'    => now()->addHours(24)->toDateString(),
        ]);

        $response = $this->get(route('hot-deals'));

        $response->assertStatus(200);
        $response->assertSee('Promo Berakhir 24 Jam');
    }

    /**
     * Test that active promos with end_date more than 48 hours away do NOT appear.
     */
    public function test_active_promo_ending_after_48_hours_does_not_appear(): void
    {
        $category = Category::factory()->create();
        $seller   = SellerProfile::factory()->create();

        // Active promo ending in 72 hours — should NOT appear
        Promo::factory()->active()->create([
            'seller_id'   => $seller->id,
            'category_id' => $category->id,
            'title'       => 'Promo Berakhir 72 Jam',
            'end_date'    => now()->addHours(72)->toDateString(),
        ]);

        $response = $this->get(route('hot-deals'));

        $response->assertStatus(200);
        $response->assertDontSee('Promo Berakhir 72 Jam');
    }

    /**
     * Test that expired promos do NOT appear on the hot-deals page.
     */
    public function test_expired_promo_does_not_appear(): void
    {
        $category = Category::factory()->create();
        $seller   = SellerProfile::factory()->create();

        Promo::factory()->expired()->create([
            'seller_id'   => $seller->id,
            'category_id' => $category->id,
            'title'       => 'Promo Kadaluarsa',
        ]);

        $response = $this->get(route('hot-deals'));

        $response->assertStatus(200);
        $response->assertDontSee('Promo Kadaluarsa');
    }

    /**
     * Test that results are ordered by end_date ASC (earliest ending first).
     */
    public function test_hot_deals_are_ordered_by_end_date_ascending(): void
    {
        $category = Category::factory()->create();
        $seller   = SellerProfile::factory()->create();

        // Create promo ending in 2 days (later, still within 48h boundary) first
        Promo::factory()->active()->create([
            'seller_id'   => $seller->id,
            'category_id' => $category->id,
            'title'       => 'Promo Berakhir Besok Lusa',
            'end_date'    => now()->addDays(2)->toDateString(),
        ]);

        // Create promo ending tomorrow (sooner) second
        Promo::factory()->active()->create([
            'seller_id'   => $seller->id,
            'category_id' => $category->id,
            'title'       => 'Promo Berakhir Besok',
            'end_date'    => now()->addDay()->toDateString(),
        ]);

        $response = $this->get(route('hot-deals'));

        $response->assertStatus(200);

        $content    = $response->getContent();
        $posTomorrow = strpos($content, 'Promo Berakhir Besok');
        $posLater    = strpos($content, 'Promo Berakhir Besok Lusa');

        $this->assertNotFalse($posTomorrow, 'Promo ending tomorrow should be visible');
        $this->assertNotFalse($posLater, 'Promo ending in 2 days should be visible');
        $this->assertLessThan($posLater, $posTomorrow, 'Promo ending sooner should appear before promo ending later');
    }
}
