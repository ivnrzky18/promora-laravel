<?php

namespace Tests\Feature\Public;

use App\Models\Category;
use App\Models\Event;
use App\Models\Promo;
use App\Models\SellerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that GET /api/calendar-events returns a JSON array.
     */
    public function test_calendar_events_endpoint_returns_json_array(): void
    {
        $response = $this->getJson(route('calendar.events'));

        $response->assertStatus(200);
        $response->assertJsonIsArray();
    }

    /**
     * Test that an active promo appears in the response with orange color (#f97316).
     */
    public function test_active_promo_appears_with_orange_color(): void
    {
        $category = Category::factory()->create();
        $seller   = SellerProfile::factory()->create();

        $promo = Promo::factory()->active()->create([
            'seller_id'   => $seller->id,
            'category_id' => $category->id,
            'title'       => 'Promo Aktif Test',
        ]);

        $response = $this->getJson(route('calendar.events'));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id'    => 'promo-' . $promo->id,
            'title' => 'Promo Aktif Test',
            'color' => '#f97316',
        ]);
    }

    /**
     * Test that an active event appears in the response with blue color (#3b82f6).
     */
    public function test_active_event_appears_with_blue_color(): void
    {
        $seller = SellerProfile::factory()->create();

        $event = Event::factory()->active()->create([
            'seller_id' => $seller->id,
            'title'     => 'Event Aktif Test',
        ]);

        $response = $this->getJson(route('calendar.events'));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id'    => 'event-' . $event->id,
            'title' => 'Event Aktif Test',
            'color' => '#3b82f6',
        ]);
    }

    /**
     * Test that draft promos do NOT appear in the calendar events.
     */
    public function test_draft_promo_does_not_appear(): void
    {
        $category = Category::factory()->create();
        $seller   = SellerProfile::factory()->create();

        $draftPromo = Promo::factory()->draft()->create([
            'seller_id'   => $seller->id,
            'category_id' => $category->id,
            'title'       => 'Promo Draft Tersembunyi',
        ]);

        $response = $this->getJson(route('calendar.events'));

        $response->assertStatus(200);
        $response->assertJsonMissing([
            'id' => 'promo-' . $draftPromo->id,
        ]);
    }

    /**
     * Test that expired promos do NOT appear in the calendar events.
     */
    public function test_expired_promo_does_not_appear(): void
    {
        $category = Category::factory()->create();
        $seller   = SellerProfile::factory()->create();

        $expiredPromo = Promo::factory()->expired()->create([
            'seller_id'   => $seller->id,
            'category_id' => $category->id,
            'title'       => 'Promo Kadaluarsa Tersembunyi',
        ]);

        $response = $this->getJson(route('calendar.events'));

        $response->assertStatus(200);
        $response->assertJsonMissing([
            'id' => 'promo-' . $expiredPromo->id,
        ]);
    }

    /**
     * Test that filter by category_id returns only promos from that category.
     */
    public function test_filter_by_category_id_returns_only_promos_from_that_category(): void
    {
        $categoryA = Category::factory()->create(['name' => 'Kuliner', 'slug' => 'kuliner']);
        $categoryB = Category::factory()->create(['name' => 'Fashion', 'slug' => 'fashion']);
        $seller    = SellerProfile::factory()->create();

        $promoA = Promo::factory()->active()->create([
            'seller_id'   => $seller->id,
            'category_id' => $categoryA->id,
            'title'       => 'Promo Kuliner',
        ]);
        $promoB = Promo::factory()->active()->create([
            'seller_id'   => $seller->id,
            'category_id' => $categoryB->id,
            'title'       => 'Promo Fashion',
        ]);

        $response = $this->getJson(route('calendar.events', ['category_id' => $categoryA->id]));

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => 'promo-' . $promoA->id]);
        $response->assertJsonMissing(['id' => 'promo-' . $promoB->id]);
    }

    /**
     * Test that filter by category_id filters events by seller's business_category matching category name.
     */
    public function test_filter_by_category_id_filters_events_by_seller_business_category(): void
    {
        $categoryKuliner = Category::factory()->create(['name' => 'Kuliner', 'slug' => 'kuliner']);
        $categoryFashion = Category::factory()->create(['name' => 'Fashion', 'slug' => 'fashion']);

        $sellerKuliner = SellerProfile::factory()->create(['business_category' => 'Kuliner']);
        $sellerFashion = SellerProfile::factory()->create(['business_category' => 'Fashion']);

        $eventKuliner = Event::factory()->active()->create([
            'seller_id' => $sellerKuliner->id,
            'title'     => 'Event Kuliner',
        ]);
        $eventFashion = Event::factory()->active()->create([
            'seller_id' => $sellerFashion->id,
            'title'     => 'Event Fashion',
        ]);

        $response = $this->getJson(route('calendar.events', ['category_id' => $categoryKuliner->id]));

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => 'event-' . $eventKuliner->id]);
        $response->assertJsonMissing(['id' => 'event-' . $eventFashion->id]);
    }
}
