<?php

namespace Tests\Feature\Consumer;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Promo;
use App\Models\SellerProfile;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the dashboard shows promos from subscribed sellers in the feed.
     */
    public function test_dashboard_shows_feed_from_subscriptions(): void
    {
        $consumer = User::factory()->consumer()->create();

        // Create a seller and subscribe to them
        $sellerUser = User::factory()->seller()->create();
        $seller = SellerProfile::factory()->create(['user_id' => $sellerUser->id]);
        Subscription::create(['user_id' => $consumer->id, 'seller_id' => $seller->id]);

        // Create an active promo for the subscribed seller
        $category = Category::factory()->create();
        $promo = Promo::factory()->active()->create([
            'seller_id'   => $seller->id,
            'category_id' => $category->id,
            'title'       => 'Promo Spesial Langganan',
        ]);

        // Create a promo from a non-subscribed seller (should NOT appear in feed)
        $otherSeller = SellerProfile::factory()->create();
        Promo::factory()->active()->create([
            'seller_id'   => $otherSeller->id,
            'category_id' => $category->id,
            'title'       => 'Promo Seller Lain',
        ]);

        $response = $this->actingAs($consumer)->get(route('consumer.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('promoFeed', function ($promoFeed) use ($promo) {
            return $promoFeed->contains('id', $promo->id);
        });
        $response->assertViewHas('promoFeed', function ($promoFeed) {
            // Should not contain promo from non-subscribed seller
            return !$promoFeed->contains('title', 'Promo Seller Lain');
        });
    }

    /**
     * Test that the hot deals section shows promos ending within 48 hours.
     */
    public function test_hot_deals_section_shows_promos_ending_within_48h(): void
    {
        $consumer = User::factory()->consumer()->create();
        $category = Category::factory()->create();

        // Create a hot deal promo (ending within 48h — use end_date as tomorrow to avoid
        // SQLite date vs datetime comparison edge cases at midnight)
        $hotDealPromo = Promo::factory()->create([
            'category_id' => $category->id,
            'status'      => 'active',
            'start_date'  => now()->subDay()->toDateString(),
            'end_date'    => now()->addDay()->toDateString(),
            'is_hot_deal' => true,
            'title'       => 'Hot Deal Segera Berakhir',
        ]);

        // Create a regular active promo (ending in 7 days — NOT a hot deal)
        Promo::factory()->create([
            'category_id' => $category->id,
            'status'      => 'active',
            'start_date'  => now()->subDay()->toDateString(),
            'end_date'    => now()->addDays(7)->toDateString(),
            'is_hot_deal' => false,
            'title'       => 'Promo Biasa',
        ]);

        $response = $this->actingAs($consumer)->get(route('consumer.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('hotDeals', function ($hotDeals) use ($hotDealPromo) {
            return $hotDeals->contains('id', $hotDealPromo->id);
        });
        $response->assertViewHas('hotDeals', function ($hotDeals) {
            return !$hotDeals->contains('title', 'Promo Biasa');
        });
    }

    /**
     * Test that stats show correct bookmark and subscription counts.
     */
    public function test_stats_show_correct_counts(): void
    {
        $consumer = User::factory()->consumer()->create();
        $category = Category::factory()->create();

        // Create 2 subscriptions
        $seller1 = SellerProfile::factory()->create();
        $seller2 = SellerProfile::factory()->create();
        Subscription::create(['user_id' => $consumer->id, 'seller_id' => $seller1->id]);
        Subscription::create(['user_id' => $consumer->id, 'seller_id' => $seller2->id]);

        // Create 3 bookmarks
        $promo1 = Promo::factory()->active()->create(['seller_id' => $seller1->id, 'category_id' => $category->id]);
        $promo2 = Promo::factory()->active()->create(['seller_id' => $seller1->id, 'category_id' => $category->id]);
        $promo3 = Promo::factory()->active()->create(['seller_id' => $seller2->id, 'category_id' => $category->id]);
        Bookmark::create(['user_id' => $consumer->id, 'promo_id' => $promo1->id]);
        Bookmark::create(['user_id' => $consumer->id, 'promo_id' => $promo2->id]);
        Bookmark::create(['user_id' => $consumer->id, 'promo_id' => $promo3->id]);

        $response = $this->actingAs($consumer)->get(route('consumer.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('bookmarkCount', 3);
        $response->assertViewHas('subscriptionCount', 2);
    }

    /**
     * Test that unauthenticated users are redirected to login.
     */
    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get(route('consumer.dashboard'));

        $response->assertRedirect(route('consumer.login'));
    }

    /**
     * Test that feed is empty when consumer has no subscriptions.
     */
    public function test_feed_is_empty_when_no_subscriptions(): void
    {
        $consumer = User::factory()->consumer()->create();

        $response = $this->actingAs($consumer)->get(route('consumer.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('promoFeed', function ($promoFeed) {
            return $promoFeed->isEmpty();
        });
    }
}
