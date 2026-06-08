<?php

namespace Tests\Property;

use App\Console\Commands\ExpirePromos;
use App\Console\Commands\UpdateHotDeals;
use App\Models\Category;
use App\Models\Promo;
use App\Models\SellerProfile;
use App\Models\User;
use Carbon\Carbon;
use Eris\Generators;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Property-Based Tests for Hot Deals
 *
 * Validates: Requirements 2.4, 6.1, 6.4, 6.5
 */
class HotDealPropertyTest extends TestCase
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

    /**
     * Create a SellerProfile with a unique seller user.
     */
    private function createSeller(string $suffix = ''): SellerProfile
    {
        $sellerUser = User::factory()->seller()->create([
            'email' => 'hotdeal_seller_' . $suffix . Str::random(8) . '@example.com',
        ]);
        return SellerProfile::factory()->create([
            'user_id'     => $sellerUser->id,
            'is_verified' => true,
        ]);
    }

    // ─── Property 7: Hot deals section berisi tepat promo active dengan end_date dalam [now, now+48h] ─

    /**
     * Property 7: Hot deals section berisi tepat promo active dengan end_date dalam [now, now+48h].
     *
     * For any set of promos with varying statuses and end_dates, the hot deals
     * section (scopeHotDeals) SHALL contain exactly those promos that have
     * status=active AND end_date >= now() AND end_date <= now()+48h.
     * No more, no less.
     *
     * Validates: Requirements 2.4, 6.1
     */
    public function testHotDealsSectionContainsExactlyActivePromosWithEndDateInWindow(): void
    {
        $countInWindow  = Generators::choose(1, 4);
        $countOutWindow = Generators::choose(0, 3);
        $countNonActive = Generators::choose(0, 3);
        $iterIndex      = Generators::choose(1, 999999);

        $this->forAll($countInWindow, $countOutWindow, $countNonActive, $iterIndex)
            ->withMaxSize(100)
            ->then(function (int $inWindow, int $outWindow, int $nonActive, int $idx) {
                $seller   = $this->createSeller("p7_{$idx}_");
                $category = $this->createCategory("P7_{$idx}_");

                $inWindowPromos  = [];
                $outWindowPromos = [];
                $nonActivePromos = [];

                // Create promos INSIDE the hot deal window: end_date in [now, now+48h], status=active
                for ($i = 0; $i < $inWindow; $i++) {
                    $hoursFromNow = ($i + 1) * (47.0 / max($inWindow, 1));
                    $endDate      = Carbon::now()->addHours($hoursFromNow)->toDateString();

                    $promo = Promo::factory()->create([
                        'seller_id'   => $seller->id,
                        'category_id' => $category->id,
                        'status'      => 'active',
                        'start_date'  => Carbon::now()->subDay()->toDateString(),
                        'end_date'    => $endDate,
                        'is_hot_deal' => false,
                    ]);
                    $inWindowPromos[] = $promo;
                }

                // Create promos OUTSIDE the hot deal window: end_date > now+48h, status=active
                for ($i = 0; $i < $outWindow; $i++) {
                    $promo = Promo::factory()->create([
                        'seller_id'   => $seller->id,
                        'category_id' => $category->id,
                        'status'      => 'active',
                        'start_date'  => Carbon::now()->subDay()->toDateString(),
                        'end_date'    => Carbon::now()->addDays(5 + $i)->toDateString(),
                        'is_hot_deal' => false,
                    ]);
                    $outWindowPromos[] = $promo;
                }

                // Create NON-ACTIVE promos (draft/expired) with end_date in window
                for ($i = 0; $i < $nonActive; $i++) {
                    $status = ($i % 2 === 0) ? 'draft' : 'expired';
                    $promo  = Promo::factory()->create([
                        'seller_id'   => $seller->id,
                        'category_id' => $category->id,
                        'status'      => $status,
                        'start_date'  => Carbon::now()->subDay()->toDateString(),
                        'end_date'    => Carbon::now()->addHours(12)->toDateString(),
                        'is_hot_deal' => false,
                    ]);
                    $nonActivePromos[] = $promo;
                }

                // Query hot deals using the same scope as the application
                $hotDeals    = Promo::hotDeals()->get();
                $hotDealIds  = $hotDeals->pluck('id')->toArray();

                // Assert: all in-window active promos appear in hot deals
                foreach ($inWindowPromos as $promo) {
                    $this->assertContains(
                        $promo->id,
                        $hotDealIds,
                        "Active promo with end_date in [now, now+48h] (id={$promo->id}, end_date={$promo->end_date}) should appear in hot deals"
                    );
                }

                // Assert: out-of-window promos do NOT appear in hot deals
                foreach ($outWindowPromos as $promo) {
                    $this->assertNotContains(
                        $promo->id,
                        $hotDealIds,
                        "Active promo with end_date > now+48h (id={$promo->id}, end_date={$promo->end_date}) should NOT appear in hot deals"
                    );
                }

                // Assert: non-active promos do NOT appear in hot deals
                foreach ($nonActivePromos as $promo) {
                    $this->assertNotContains(
                        $promo->id,
                        $hotDealIds,
                        "Non-active promo (id={$promo->id}, status={$promo->status}) should NOT appear in hot deals"
                    );
                }

                // Assert: every result in hot deals satisfies the invariant
                foreach ($hotDeals as $promo) {
                    $this->assertEquals(
                        'active',
                        $promo->status,
                        "Hot deal promo (id={$promo->id}) must have status=active, got {$promo->status}"
                    );

                    $endDate = Carbon::parse($promo->end_date);

                    $this->assertTrue(
                        $endDate->gte(Carbon::now()->startOfDay()),
                        "Hot deal promo (id={$promo->id}) end_date ({$promo->end_date}) must be >= now"
                    );

                    $this->assertTrue(
                        $endDate->lte(Carbon::now()->addHours(48)->endOfDay()),
                        "Hot deal promo (id={$promo->id}) end_date ({$promo->end_date}) must be <= now+48h"
                    );
                }

                // Cleanup
                foreach (array_merge($inWindowPromos, $outWindowPromos, $nonActivePromos) as $promo) {
                    $promo->forceDelete();
                }
                $sellerUser = $seller->user;
                $seller->forceDelete();
                $sellerUser->delete();
                $category->delete();
            });
    }

    // ─── Property 9: UpdateHotDeals command mempertahankan invariant is_hot_deal ─

    /**
     * Property 9: UpdateHotDeals command mempertahankan invariant is_hot_deal.
     *
     * After running the UpdateHotDeals command:
     * - Every active promo with end_date in [now, now+48h] SHALL have is_hot_deal=true
     * - Every promo outside that window (wrong status or end_date out of range)
     *   SHALL have is_hot_deal=false
     *
     * Validates: Requirement 6.4
     */
    public function testUpdateHotDealsCommandMaintainsIsHotDealInvariant(): void
    {
        $countInWindow  = Generators::choose(1, 4);
        $countOutWindow = Generators::choose(1, 4);
        $countExpired   = Generators::choose(0, 3);
        $iterIndex      = Generators::choose(1, 999999);

        $this->forAll($countInWindow, $countOutWindow, $countExpired, $iterIndex)
            ->withMaxSize(100)
            ->then(function (int $inWindow, int $outWindow, int $expiredCount, int $idx) {
                $seller   = $this->createSeller("p9_{$idx}_");
                $category = $this->createCategory("P9_{$idx}_");

                $inWindowPromos  = [];
                $outWindowPromos = [];
                $expiredPromos   = [];

                // Create promos INSIDE the hot deal window: end_date in [now, now+48h], status=active
                // Start with is_hot_deal=false to verify the command sets it to true
                for ($i = 0; $i < $inWindow; $i++) {
                    $hoursFromNow = ($i + 1) * (47.0 / max($inWindow, 1));
                    $endDate      = Carbon::now()->addHours($hoursFromNow)->toDateString();

                    $promo = Promo::factory()->create([
                        'seller_id'   => $seller->id,
                        'category_id' => $category->id,
                        'status'      => 'active',
                        'start_date'  => Carbon::now()->subDay()->toDateString(),
                        'end_date'    => $endDate,
                        'is_hot_deal' => false,
                    ]);
                    $inWindowPromos[] = $promo;
                }

                // Create promos OUTSIDE the hot deal window: end_date > now+48h, status=active
                // Start with is_hot_deal=true to verify the command resets it to false
                for ($i = 0; $i < $outWindow; $i++) {
                    $promo = Promo::factory()->create([
                        'seller_id'   => $seller->id,
                        'category_id' => $category->id,
                        'status'      => 'active',
                        'start_date'  => Carbon::now()->subDay()->toDateString(),
                        'end_date'    => Carbon::now()->addDays(5 + $i)->toDateString(),
                        'is_hot_deal' => true,
                    ]);
                    $outWindowPromos[] = $promo;
                }

                // Create expired promos with is_hot_deal=true to verify the command resets them
                for ($i = 0; $i < $expiredCount; $i++) {
                    $promo = Promo::factory()->create([
                        'seller_id'   => $seller->id,
                        'category_id' => $category->id,
                        'status'      => 'expired',
                        'start_date'  => Carbon::now()->subDays(10)->toDateString(),
                        'end_date'    => Carbon::now()->subDay()->toDateString(),
                        'is_hot_deal' => true,
                    ]);
                    $expiredPromos[] = $promo;
                }

                // Run the UpdateHotDeals command
                $this->artisan('promos:update-hot-deals')->assertSuccessful();

                // Assert: all in-window active promos now have is_hot_deal=true
                foreach ($inWindowPromos as $promo) {
                    $fresh = $promo->fresh();
                    $this->assertTrue(
                        $fresh->is_hot_deal,
                        "Active promo with end_date in [now, now+48h] (id={$promo->id}, end_date={$promo->end_date}) should have is_hot_deal=true after UpdateHotDeals"
                    );
                }

                // Assert: out-of-window active promos now have is_hot_deal=false
                foreach ($outWindowPromos as $promo) {
                    $fresh = $promo->fresh();
                    $this->assertFalse(
                        $fresh->is_hot_deal,
                        "Active promo with end_date > now+48h (id={$promo->id}, end_date={$promo->end_date}) should have is_hot_deal=false after UpdateHotDeals"
                    );
                }

                // Assert: expired promos now have is_hot_deal=false
                foreach ($expiredPromos as $promo) {
                    $fresh = $promo->fresh();
                    $this->assertFalse(
                        $fresh->is_hot_deal,
                        "Expired promo (id={$promo->id}) should have is_hot_deal=false after UpdateHotDeals"
                    );
                }

                // Cleanup
                foreach (array_merge($inWindowPromos, $outWindowPromos, $expiredPromos) as $promo) {
                    $promo->forceDelete();
                }
                $sellerUser = $seller->user;
                $seller->forceDelete();
                $sellerUser->delete();
                $category->delete();
            });
    }

    // ─── Property 10: ExpirePromos command mempertahankan invariant status=expired ─

    /**
     * Property 10: ExpirePromos command mempertahankan invariant status=expired.
     *
     * After running the ExpirePromos command:
     * - Every active promo with end_date < today SHALL have status=expired and is_hot_deal=false
     * - Every active promo with end_date >= today SHALL remain active
     *
     * Validates: Requirement 6.5
     */
    public function testExpirePromosCommandMaintainsExpiredStatusInvariant(): void
    {
        $countPastEnd    = Generators::choose(1, 4);
        $countFutureEnd  = Generators::choose(1, 4);
        $countTodayEnd   = Generators::choose(0, 3);
        $iterIndex       = Generators::choose(1, 999999);

        $this->forAll($countPastEnd, $countFutureEnd, $countTodayEnd, $iterIndex)
            ->withMaxSize(100)
            ->then(function (int $pastEnd, int $futureEnd, int $todayEnd, int $idx) {
                $seller   = $this->createSeller("p10_{$idx}_");
                $category = $this->createCategory("P10_{$idx}_");

                $pastEndPromos   = [];
                $futureEndPromos = [];
                $todayEndPromos  = [];

                // Create active promos with end_date < today (should be expired after command)
                for ($i = 0; $i < $pastEnd; $i++) {
                    $promo = Promo::factory()->create([
                        'seller_id'   => $seller->id,
                        'category_id' => $category->id,
                        'status'      => 'active',
                        'start_date'  => Carbon::now()->subDays(10 + $i)->toDateString(),
                        'end_date'    => Carbon::now()->subDays(1 + $i)->toDateString(),
                        'is_hot_deal' => false,
                    ]);
                    $pastEndPromos[] = $promo;
                }

                // Create active promos with end_date > today (should remain active after command)
                for ($i = 0; $i < $futureEnd; $i++) {
                    $promo = Promo::factory()->create([
                        'seller_id'   => $seller->id,
                        'category_id' => $category->id,
                        'status'      => 'active',
                        'start_date'  => Carbon::now()->subDay()->toDateString(),
                        'end_date'    => Carbon::now()->addDays(2 + $i)->toDateString(),
                        'is_hot_deal' => false,
                    ]);
                    $futureEndPromos[] = $promo;
                }

                // Create active promos with end_date = today (should remain active — command uses < today)
                for ($i = 0; $i < $todayEnd; $i++) {
                    $promo = Promo::factory()->create([
                        'seller_id'   => $seller->id,
                        'category_id' => $category->id,
                        'status'      => 'active',
                        'start_date'  => Carbon::now()->subDay()->toDateString(),
                        'end_date'    => Carbon::now()->toDateString(),
                        'is_hot_deal' => false,
                    ]);
                    $todayEndPromos[] = $promo;
                }

                // Run the ExpirePromos command
                $this->artisan('promos:expire')->assertSuccessful();

                // Assert: promos with end_date < today now have status=expired and is_hot_deal=false
                foreach ($pastEndPromos as $promo) {
                    $fresh = $promo->fresh();
                    $this->assertEquals(
                        'expired',
                        $fresh->status,
                        "Active promo with end_date < today (id={$promo->id}, end_date={$promo->end_date}) should have status=expired after ExpirePromos"
                    );
                    $this->assertFalse(
                        $fresh->is_hot_deal,
                        "Expired promo (id={$promo->id}) should have is_hot_deal=false after ExpirePromos"
                    );
                }

                // Assert: promos with end_date > today remain active
                foreach ($futureEndPromos as $promo) {
                    $fresh = $promo->fresh();
                    $this->assertEquals(
                        'active',
                        $fresh->status,
                        "Active promo with end_date > today (id={$promo->id}, end_date={$promo->end_date}) should remain active after ExpirePromos"
                    );
                }

                // Assert: promos with end_date = today remain active (command uses strict <)
                foreach ($todayEndPromos as $promo) {
                    $fresh = $promo->fresh();
                    $this->assertEquals(
                        'active',
                        $fresh->status,
                        "Active promo with end_date = today (id={$promo->id}, end_date={$promo->end_date}) should remain active after ExpirePromos (command uses end_date < today)"
                    );
                }

                // Cleanup
                foreach (array_merge($pastEndPromos, $futureEndPromos, $todayEndPromos) as $promo) {
                    $promo->forceDelete();
                }
                $sellerUser = $seller->user;
                $seller->forceDelete();
                $sellerUser->delete();
                $category->delete();
            });
    }
}
