<?php

namespace Tests\Feature\Public;

use App\Models\Category;
use App\Models\Promo;
use App\Models\Review;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that seller profile page shows seller info.
     */
    public function test_seller_profile_page_shows_seller_info(): void
    {
        $seller = SellerProfile::factory()->create([
            'is_verified'       => true,
            'business_name'     => 'Warung Makan Enak',
            'business_category' => 'Kuliner',
            'address'           => 'Jl. Merdeka No. 10, Bandung',
        ]);

        $response = $this->get(route('sellers.show', $seller));

        $response->assertStatus(200);
        $response->assertSee('Warung Makan Enak');
        $response->assertSee('Kuliner');
        $response->assertSee('Jl. Merdeka No. 10, Bandung');
    }

    /**
     * Test that seller profile page shows active promos.
     */
    public function test_seller_profile_shows_active_promos(): void
    {
        $seller   = SellerProfile::factory()->create(['is_verified' => true]);
        $category = Category::factory()->create(['name' => 'Kuliner', 'slug' => 'kuliner']);

        Promo::factory()->active()->create([
            'seller_id'   => $seller->id,
            'category_id' => $category->id,
            'title'       => 'Promo Aktif Seller',
        ]);
        Promo::factory()->expired()->create([
            'seller_id'   => $seller->id,
            'category_id' => $category->id,
            'title'       => 'Promo Kadaluarsa Seller',
        ]);

        $response = $this->get(route('sellers.show', $seller));

        $response->assertStatus(200);
        $response->assertSee('Promo Aktif Seller');
        $response->assertDontSee('Promo Kadaluarsa Seller');
    }

    /**
     * Test that seller profile page shows reviews.
     */
    public function test_seller_profile_shows_reviews(): void
    {
        $seller   = SellerProfile::factory()->create(['is_verified' => true]);
        $consumer = User::factory()->consumer()->create(['name' => 'Budi Reviewer']);

        Review::factory()->create([
            'user_id'   => $consumer->id,
            'seller_id' => $seller->id,
            'rating'    => 5,
            'comment'   => 'Seller sangat ramah dan produknya bagus!',
        ]);

        $response = $this->get(route('sellers.show', $seller));

        $response->assertStatus(200);
        $response->assertSee('Budi Reviewer');
        $response->assertSee('Seller sangat ramah dan produknya bagus!');
    }

    /**
     * Test that subscribe button is visible for logged-in consumer.
     */
    public function test_subscribe_button_visible_for_logged_in_consumer(): void
    {
        $seller   = SellerProfile::factory()->create(['is_verified' => true]);
        $consumer = User::factory()->consumer()->create();

        $response = $this->actingAs($consumer)->get(route('sellers.show', $seller));

        $response->assertStatus(200);
        $response->assertSee('Ikuti Seller');
    }

    /**
     * Test that subscribe button shows "Berhenti Ikuti" when already subscribed.
     */
    public function test_subscribe_button_shows_unsubscribe_when_already_subscribed(): void
    {
        $seller   = SellerProfile::factory()->create(['is_verified' => true]);
        $consumer = User::factory()->consumer()->create();

        // Create subscription
        \App\Models\Subscription::create([
            'user_id'   => $consumer->id,
            'seller_id' => $seller->id,
        ]);

        $response = $this->actingAs($consumer)->get(route('sellers.show', $seller));

        $response->assertStatus(200);
        // The Alpine.js data will have subscribed: true
        $response->assertSee('subscribed: true', false);
    }

    /**
     * Test that review form is visible for logged-in consumer.
     */
    public function test_review_form_visible_for_logged_in_consumer(): void
    {
        $seller   = SellerProfile::factory()->create(['is_verified' => true]);
        $consumer = User::factory()->consumer()->create();

        $response = $this->actingAs($consumer)->get(route('sellers.show', $seller));

        $response->assertStatus(200);
        $response->assertSee('Tulis Ulasan');
    }

    /**
     * Test that review form is NOT visible for guests.
     */
    public function test_review_form_not_visible_for_guests(): void
    {
        $seller = SellerProfile::factory()->create(['is_verified' => true]);

        $response = $this->get(route('sellers.show', $seller));

        $response->assertStatus(200);
        $response->assertDontSee('Tulis Ulasan');
    }
}
