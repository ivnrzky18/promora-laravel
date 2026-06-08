<?php

namespace Tests\Property;

use App\Models\Category;
use App\Models\Promo;
use App\Models\SellerProfile;
use App\Models\User;
use Eris\Generators;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Property-Based Tests for Search and Filter functionality
 *
 * Validates: Requirements 8.2, 8.4
 */
class SearchPropertyTest extends TestCase
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
            'slug' => Str::slug($name) . '-' . Str::random(4),
            'icon' => null,
        ]);
    }

    /**
     * Create a verified seller with a specific business_name.
     */
    private function createVerifiedSeller(string $suffix, string $businessName): SellerProfile
    {
        $sellerUser = User::factory()->seller()->create([
            'email' => "seller_search_{$suffix}@example.com",
        ]);

        return SellerProfile::factory()->create([
            'user_id'       => $sellerUser->id,
            'business_name' => $businessName,
            'is_verified'   => true,
        ]);
    }

    // ─── Property 14: Category filter → semua hasil memiliki category_id yang sama ─

    /**
     * Property 14: Category filter always returns only promos with the selected category_id.
     *
     * For any category_id filter applied on the Explore page, all Promos returned
     * SHALL have a category_id equal to the selected filter.
     *
     * Validates: Requirements 8.2
     */
    public function testCategoryFilterAlwaysReturnsPromosWithMatchingCategoryId(): void
    {
        // Number of matching promos (1–5) and non-matching promos (1–5)
        $matchingCount    = Generators::choose(1, 5);
        $nonMatchingCount = Generators::choose(1, 5);
        $iterIndex        = Generators::choose(1, 999999);

        $this->forAll($matchingCount, $nonMatchingCount, $iterIndex)
            ->withMaxSize(100)
            ->then(function (int $numMatching, int $numNonMatching, int $idx) {
                // ── Setup: two categories ─────────────────────────────────────
                $targetCategory = $this->createCategory("P14target_{$idx}_");
                $otherCategory  = $this->createCategory("P14other_{$idx}_");

                // Create a verified seller for this iteration
                $seller = $this->createVerifiedSeller("{$idx}", "Toko Search P14 {$idx}");

                // ── Create matching promos (target category, active) ──────────
                $matchingPromos = [];
                for ($i = 0; $i < $numMatching; $i++) {
                    $matchingPromos[] = Promo::factory()->active()->create([
                        'seller_id'   => $seller->id,
                        'category_id' => $targetCategory->id,
                    ]);
                }

                // ── Create non-matching promos (other category, active) ───────
                $nonMatchingPromos = [];
                for ($i = 0; $i < $numNonMatching; $i++) {
                    $nonMatchingPromos[] = Promo::factory()->active()->create([
                        'seller_id'   => $seller->id,
                        'category_id' => $otherCategory->id,
                    ]);
                }

                // ── Execute the same query as SearchController::index() ───────
                $query = Promo::with(['seller', 'category'])
                    ->active()
                    ->whereHas('seller', fn ($q) => $q->where('is_verified', true))
                    ->where('category_id', $targetCategory->id);

                $results = $query->get();

                // ── Property assertion ────────────────────────────────────────

                // All results must have the target category_id
                $this->assertGreaterThanOrEqual(
                    1,
                    $results->count(),
                    "Category filter should return at least 1 result when matching promos exist"
                );

                foreach ($results as $promo) {
                    $this->assertEquals(
                        $targetCategory->id,
                        $promo->category_id,
                        "All results from category filter must have category_id = {$targetCategory->id}, " .
                        "but found promo #{$promo->id} with category_id = {$promo->category_id}"
                    );
                }

                // Verify matching promos are included
                $resultIds = $results->pluck('id')->toArray();
                foreach ($matchingPromos as $promo) {
                    $this->assertContains(
                        $promo->id,
                        $resultIds,
                        "Promo #{$promo->id} with target category_id should appear in filtered results"
                    );
                }

                // Verify non-matching promos are excluded
                foreach ($nonMatchingPromos as $promo) {
                    $this->assertNotContains(
                        $promo->id,
                        $resultIds,
                        "Promo #{$promo->id} with other category_id should NOT appear in filtered results"
                    );
                }

                // ── Cleanup ───────────────────────────────────────────────────
                foreach ($matchingPromos as $promo) {
                    $promo->forceDelete();
                }
                foreach ($nonMatchingPromos as $promo) {
                    $promo->forceDelete();
                }
                $seller->forceDelete();
                $seller->user->delete();
                $targetCategory->delete();
                $otherCategory->delete();
                $this->app['auth']->logout();
            });
    }

    // ─── Property 15: Keyword search → semua hasil mengandung keyword ─────────

    /**
     * Property 15: Keyword search always returns only promos containing the keyword.
     *
     * For any search keyword, all Promos returned SHALL contain the keyword
     * in at least one of: promos.title, promos.description, or
     * seller_profiles.business_name (case-insensitive).
     *
     * Validates: Requirements 8.4
     */
    public function testKeywordSearchAlwaysReturnsPromosContainingKeyword(): void
    {
        // Use a fixed set of unique keywords to avoid collisions
        $keywordIndex = Generators::choose(0, 4);
        $iterIndex    = Generators::choose(1, 999999);

        $this->forAll($keywordIndex, $iterIndex)
            ->withMaxSize(100)
            ->then(function (int $keywordIdx, int $idx) {
                // ── Choose a unique keyword for this iteration ────────────────
                $keywords = [
                    'UNIQUEKW_ALPHA',
                    'UNIQUEKW_BETA',
                    'UNIQUEKW_GAMMA',
                    'UNIQUEKW_DELTA',
                    'UNIQUEKW_EPSILON',
                ];
                $keyword = $keywords[$keywordIdx];

                // ── Setup: shared category ────────────────────────────────────
                $category = $this->createCategory("P15_{$idx}_");

                // ── Create seller whose business_name contains the keyword ─────
                $sellerWithKeyword = $this->createVerifiedSeller(
                    "P15kw_{$idx}",
                    "Toko {$keyword} Bisnis {$idx}"
                );

                // ── Create seller whose business_name does NOT contain keyword ─
                $sellerWithoutKeyword = $this->createVerifiedSeller(
                    "P15nokw_{$idx}",
                    "Toko Biasa Tanpa Kata {$idx}"
                );

                // ── Create promos that match via title ────────────────────────
                $promoMatchTitle = Promo::factory()->active()->create([
                    'seller_id'   => $sellerWithoutKeyword->id,
                    'category_id' => $category->id,
                    'title'       => "Promo {$keyword} Spesial {$idx}",
                    'description' => 'Deskripsi biasa tanpa kata kunci',
                ]);

                // ── Create promo that matches via description ──────────────────
                $promoMatchDesc = Promo::factory()->active()->create([
                    'seller_id'   => $sellerWithoutKeyword->id,
                    'category_id' => $category->id,
                    'title'       => "Promo Biasa {$idx}",
                    'description' => "Dapatkan penawaran {$keyword} terbaik hari ini",
                ]);

                // ── Create promo that matches via seller business_name ─────────
                $promoMatchSeller = Promo::factory()->active()->create([
                    'seller_id'   => $sellerWithKeyword->id,
                    'category_id' => $category->id,
                    'title'       => "Promo Reguler {$idx}",
                    'description' => 'Deskripsi tanpa kata kunci',
                ]);

                // ── Create promo that does NOT match ──────────────────────────
                $promoNoMatch = Promo::factory()->active()->create([
                    'seller_id'   => $sellerWithoutKeyword->id,
                    'category_id' => $category->id,
                    'title'       => "Promo Tidak Cocok {$idx}",
                    'description' => 'Deskripsi tidak mengandung kata yang dicari',
                ]);

                // ── Execute the same query as SearchController::index() ───────
                $query = Promo::with(['seller', 'category'])
                    ->active()
                    ->whereHas('seller', fn ($q) => $q->where('is_verified', true))
                    ->where(function ($q) use ($keyword) {
                        $q->where('title', 'LIKE', "%{$keyword}%")
                          ->orWhere('description', 'LIKE', "%{$keyword}%")
                          ->orWhereHas('seller', fn ($sq) =>
                              $sq->where('business_name', 'LIKE', "%{$keyword}%")
                          );
                    });

                $results = $query->get();

                // ── Property assertion ────────────────────────────────────────

                // All results must contain the keyword in title, description, or business_name
                foreach ($results as $promo) {
                    $titleContains       = stripos($promo->title, $keyword) !== false;
                    $descContains        = stripos($promo->description ?? '', $keyword) !== false;
                    $businessNameContains = stripos($promo->seller->business_name, $keyword) !== false;

                    $this->assertTrue(
                        $titleContains || $descContains || $businessNameContains,
                        "Promo #{$promo->id} returned by keyword search for '{$keyword}' " .
                        "must contain the keyword in title ('{$promo->title}'), " .
                        "description ('{$promo->description}'), or " .
                        "seller business_name ('{$promo->seller->business_name}')"
                    );
                }

                // Verify matching promos are included in results
                $resultIds = $results->pluck('id')->toArray();

                $this->assertContains(
                    $promoMatchTitle->id,
                    $resultIds,
                    "Promo matching via title should appear in keyword search results"
                );

                $this->assertContains(
                    $promoMatchDesc->id,
                    $resultIds,
                    "Promo matching via description should appear in keyword search results"
                );

                $this->assertContains(
                    $promoMatchSeller->id,
                    $resultIds,
                    "Promo matching via seller business_name should appear in keyword search results"
                );

                // Verify non-matching promo is excluded
                $this->assertNotContains(
                    $promoNoMatch->id,
                    $resultIds,
                    "Promo not containing keyword should NOT appear in keyword search results"
                );

                // ── Cleanup ───────────────────────────────────────────────────
                $promoMatchTitle->forceDelete();
                $promoMatchDesc->forceDelete();
                $promoMatchSeller->forceDelete();
                $promoNoMatch->forceDelete();
                $sellerWithKeyword->forceDelete();
                $sellerWithKeyword->user->delete();
                $sellerWithoutKeyword->forceDelete();
                $sellerWithoutKeyword->user->delete();
                $category->delete();
                $this->app['auth']->logout();
            });
    }
}
