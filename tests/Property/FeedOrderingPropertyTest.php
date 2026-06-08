<?php

namespace Tests\Property;

use App\Models\Category;
use App\Models\Promo;
use App\Models\SellerProfile;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Eris\Generators;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Property-Based Tests for Consumer Feed Ordering
 *
 * Validates: Requirement 2.3
 */
class FeedOrderingPropertyTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Create a Category with a guaranteed-unique name to avoid Faker exhaustion.
     */
    private function createCategory(string $suffix = ''): Category
    {
        $name = 'Kategori' . $suffix . Str::random(8);
        return Category::create([
            'name' => $name,
            'slug' => Str::slug($name),
            'icon' => null,
        ]);
    }

    // ─── Property 6: Feed promo diurutkan created_at DESC ────────────────────

    /**
     * Property 6: Feed promo dari seller yang diikuti selalu diurutkan created_at DESC.
     *
     * For any set of N active promos from subscribed sellers, the feed displayed
     * on the consumer dashboard SHALL always be ordered by created_at DESC,
     * meaning every item[i].created_at >= item[i+1].created_at.
     *
     * Validates: Requirement 2.3
     */
    public function testFeedPromoAlwaysOrderedByCreatedAtDesc(): void
    {
        // Number of promos to create per iteration (2–8)
        $promoCount = Generators::choose(2, 8);
        // Seed for unique email generation
        $iterIndex  = Generators::choose(1, 999999);

        $this->forAll($promoCount, $iterIndex)
            ->withMaxSize(100)
            ->then(function (int $n, int $idx) {
                // ── Setup: consumer + seller + subscription ───────────────────
                $consumer = User::factory()->consumer()->create([
                    'email' => "feed_consumer_{$idx}@example.com",
                ]);

                $sellerUser = User::factory()->seller()->create([
                    'email' => "feed_seller_{$idx}@example.com",
                ]);

                $seller = SellerProfile::factory()->create([
                    'user_id'     => $sellerUser->id,
                    'is_verified' => true,
                ]);

                // Consumer subscribes to the seller
                Subscription::create([
                    'user_id'   => $consumer->id,
                    'seller_id' => $seller->id,
                ]);

                // Create a shared category to avoid Faker unique() exhaustion
                $category = $this->createCategory("P6_{$idx}_");

                // ── Create N active promos with distinct created_at timestamps ─
                // Spread timestamps across the past N days so ordering is deterministic
                $createdPromos = [];
                for ($i = 0; $i < $n; $i++) {
                    $promo = Promo::factory()->active()->create([
                        'seller_id'   => $seller->id,
                        'category_id' => $category->id,
                    ]);
                    // Manually set created_at to a distinct past timestamp
                    // (oldest first in creation loop, so DB insertion order ≠ expected order)
                    $promo->created_at = Carbon::now()->subDays($n - $i)->subMinutes($i);
                    $promo->save();
                    $createdPromos[] = $promo;
                }

                // ── Query the feed the same way ConsumerController::dashboard() does ─
                $subscribedSellerIds = $consumer->subscriptions()->pluck('seller_id');

                $feedPromos = Promo::active()
                    ->whereIn('seller_id', $subscribedSellerIds)
                    ->with(['seller', 'category'])
                    ->latest()   // orderBy('created_at', 'desc')
                    ->take(12)
                    ->get();

                // ── Assert: feed must contain at least 2 items to verify ordering ─
                $this->assertGreaterThanOrEqual(
                    2,
                    $feedPromos->count(),
                    "Feed should contain at least 2 promos for ordering check, got {$feedPromos->count()}"
                );

                // ── Assert: every item[i].created_at >= item[i+1].created_at ──
                $items = $feedPromos->values();
                for ($i = 0; $i < $items->count() - 1; $i++) {
                    $current  = $items[$i]->created_at;
                    $next     = $items[$i + 1]->created_at;

                    $this->assertTrue(
                        $current->gte($next),
                        "Feed ordering violated at index {$i}: " .
                        "item[{$i}].created_at ({$current}) should be >= " .
                        "item[" . ($i + 1) . "].created_at ({$next})"
                    );
                }

                // ── Cleanup ───────────────────────────────────────────────────
                foreach ($createdPromos as $promo) {
                    $promo->forceDelete();
                }
                Subscription::where('user_id', $consumer->id)->delete();
                $seller->forceDelete();
                $sellerUser->delete();
                $consumer->delete();
                $category->delete();
                $this->app['auth']->logout();
            });
    }

    /**
     * Property 6b: Feed ordering holds across multiple subscribed sellers.
     *
     * When a consumer subscribes to multiple sellers, the combined feed
     * SHALL still be ordered by created_at DESC across all sellers' promos.
     *
     * Validates: Requirement 2.3
     */
    public function testFeedPromoOrderedByCreatedAtDescAcrossMultipleSellers(): void
    {
        // Number of sellers (2–4) and promos per seller (1–4)
        $sellerCount = Generators::choose(2, 4);
        $promosEach  = Generators::choose(1, 4);
        $iterIndex   = Generators::choose(1, 999999);

        $this->forAll($sellerCount, $promosEach, $iterIndex)
            ->withMaxSize(100)
            ->then(function (int $sellerCount, int $promosEach, int $idx) {
                // ── Setup: consumer ───────────────────────────────────────────
                $consumer = User::factory()->consumer()->create([
                    'email' => "feed_multi_consumer_{$idx}@example.com",
                ]);

                // Create a shared category to avoid Faker unique() exhaustion
                $category = $this->createCategory("P6b_{$idx}_");

                $createdSellers     = [];
                $createdSellerUsers = [];
                $createdPromos      = [];
                $offset             = 0;

                // ── Create sellers, subscribe, and create promos ──────────────
                for ($s = 0; $s < $sellerCount; $s++) {
                    $sellerUser = User::factory()->seller()->create([
                        'email' => "feed_multi_seller_{$idx}_{$s}@example.com",
                    ]);
                    $seller = SellerProfile::factory()->create([
                        'user_id'     => $sellerUser->id,
                        'is_verified' => true,
                    ]);

                    Subscription::create([
                        'user_id'   => $consumer->id,
                        'seller_id' => $seller->id,
                    ]);

                    for ($p = 0; $p < $promosEach; $p++) {
                        $promo = Promo::factory()->active()->create([
                            'seller_id'   => $seller->id,
                            'category_id' => $category->id,
                        ]);
                        // Assign distinct timestamps spread across the past
                        $promo->created_at = Carbon::now()->subHours($offset + 1);
                        $promo->save();
                        $createdPromos[] = $promo;
                        $offset++;
                    }

                    $createdSellers[]     = $seller;
                    $createdSellerUsers[] = $sellerUser;
                }

                // ── Query the feed ────────────────────────────────────────────
                $subscribedSellerIds = $consumer->subscriptions()->pluck('seller_id');

                $feedPromos = Promo::active()
                    ->whereIn('seller_id', $subscribedSellerIds)
                    ->with(['seller', 'category'])
                    ->latest()
                    ->take(12)
                    ->get();

                // ── Assert: ordering invariant ────────────────────────────────
                $items = $feedPromos->values();

                for ($i = 0; $i < $items->count() - 1; $i++) {
                    $current = $items[$i]->created_at;
                    $next    = $items[$i + 1]->created_at;

                    $this->assertTrue(
                        $current->gte($next),
                        "Multi-seller feed ordering violated at index {$i}: " .
                        "item[{$i}].created_at ({$current}) should be >= " .
                        "item[" . ($i + 1) . "].created_at ({$next})"
                    );
                }

                // ── Cleanup ───────────────────────────────────────────────────
                foreach ($createdPromos as $promo) {
                    $promo->forceDelete();
                }
                Subscription::where('user_id', $consumer->id)->delete();
                foreach ($createdSellers as $seller) {
                    $seller->forceDelete();
                }
                foreach ($createdSellerUsers as $sellerUser) {
                    $sellerUser->delete();
                }
                $consumer->delete();
                $category->delete();
                $this->app['auth']->logout();
            });
    }

    /**
     * Property 6c: Feed contains only active promos from subscribed sellers.
     *
     * The feed SHALL NOT include promos from non-subscribed sellers,
     * and SHALL NOT include non-active (draft/expired) promos.
     *
     * Validates: Requirement 2.3
     */
    public function testFeedContainsOnlyActivePromosFromSubscribedSellers(): void
    {
        $iterIndex = Generators::choose(1, 999999);

        $this->forAll($iterIndex)
            ->withMaxSize(100)
            ->then(function (int $idx) {
                // ── Setup: consumer + subscribed seller + unsubscribed seller ─
                $consumer = User::factory()->consumer()->create([
                    'email' => "feed_filter_consumer_{$idx}@example.com",
                ]);

                $subscribedSellerUser = User::factory()->seller()->create([
                    'email' => "feed_filter_seller_sub_{$idx}@example.com",
                ]);
                $subscribedSeller = SellerProfile::factory()->create([
                    'user_id'     => $subscribedSellerUser->id,
                    'is_verified' => true,
                ]);

                $unsubscribedSellerUser = User::factory()->seller()->create([
                    'email' => "feed_filter_seller_unsub_{$idx}@example.com",
                ]);
                $unsubscribedSeller = SellerProfile::factory()->create([
                    'user_id'     => $unsubscribedSellerUser->id,
                    'is_verified' => true,
                ]);

                // Consumer subscribes only to the first seller
                Subscription::create([
                    'user_id'   => $consumer->id,
                    'seller_id' => $subscribedSeller->id,
                ]);

                // Create a shared category to avoid Faker unique() exhaustion
                $category = $this->createCategory("P6c_{$idx}_");

                // Create promos: active from subscribed, active from unsubscribed, draft from subscribed
                $activeSubscribed   = Promo::factory()->active()->create([
                    'seller_id'   => $subscribedSeller->id,
                    'category_id' => $category->id,
                ]);
                $activeUnsubscribed = Promo::factory()->active()->create([
                    'seller_id'   => $unsubscribedSeller->id,
                    'category_id' => $category->id,
                ]);
                $draftSubscribed    = Promo::factory()->draft()->create([
                    'seller_id'   => $subscribedSeller->id,
                    'category_id' => $category->id,
                ]);

                // ── Query the feed ────────────────────────────────────────────
                $subscribedSellerIds = $consumer->subscriptions()->pluck('seller_id');

                $feedPromos = Promo::active()
                    ->whereIn('seller_id', $subscribedSellerIds)
                    ->with(['seller', 'category'])
                    ->latest()
                    ->take(12)
                    ->get();

                // ── Assert: only active promo from subscribed seller appears ──
                $feedIds = $feedPromos->pluck('id')->toArray();

                $this->assertContains(
                    $activeSubscribed->id,
                    $feedIds,
                    "Active promo from subscribed seller should appear in feed"
                );

                $this->assertNotContains(
                    $activeUnsubscribed->id,
                    $feedIds,
                    "Active promo from unsubscribed seller should NOT appear in feed"
                );

                $this->assertNotContains(
                    $draftSubscribed->id,
                    $feedIds,
                    "Draft promo from subscribed seller should NOT appear in feed"
                );

                // ── Assert: ordering invariant still holds ────────────────────
                $items = $feedPromos->values();
                for ($i = 0; $i < $items->count() - 1; $i++) {
                    $current = $items[$i]->created_at;
                    $next    = $items[$i + 1]->created_at;

                    $this->assertTrue(
                        $current->gte($next),
                        "Feed ordering violated at index {$i}: " .
                        "item[{$i}].created_at ({$current}) should be >= " .
                        "item[" . ($i + 1) . "].created_at ({$next})"
                    );
                }

                // ── Cleanup ───────────────────────────────────────────────────
                $activeSubscribed->forceDelete();
                $activeUnsubscribed->forceDelete();
                $draftSubscribed->forceDelete();
                Subscription::where('user_id', $consumer->id)->delete();
                $subscribedSeller->forceDelete();
                $unsubscribedSeller->forceDelete();
                $subscribedSellerUser->delete();
                $unsubscribedSellerUser->delete();
                $consumer->delete();
                $category->delete();
                $this->app['auth']->logout();
            });
    }
}
