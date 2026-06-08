<?php

namespace Tests\Property;

use App\Models\Bookmark;
use App\Models\Promo;
use App\Models\User;
use Eris\Generators;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Property-Based Tests for Bookmark Toggle
 *
 * Validates: Requirements 2.5, 11.1, 11.2, 11.3
 */
class BookmarkPropertyTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    // ─── Property 5: Toggle dua kali → kembali ke state awal ─────────────────

    /**
     * Property 5: Bookmark toggle adalah operasi round-trip.
     *
     * For any Promo dan Consumer yang sudah login, melakukan toggle bookmark
     * dua kali berturut-turut SHALL mengembalikan status bookmark ke kondisi
     * awal (bookmarked → unbookmarked → bookmarked, atau sebaliknya).
     *
     * Validates: Requirements 2.5, 11.1, 11.2
     */
    public function testBookmarkToggleTwiceAlwaysReturnsToOriginalState(): void
    {
        $promoIndex    = Generators::choose(1, 999999);
        $consumerIndex = Generators::choose(1, 999999);

        $this->forAll($promoIndex, $consumerIndex)
            ->withMaxSize(100)
            ->then(function (int $promoIdx, int $consumerIdx) {
                // Create a fresh consumer and promo for each iteration
                $consumer = User::factory()->consumer()->create([
                    'email' => "consumer_prop5_{$promoIdx}_{$consumerIdx}@example.com",
                ]);
                $promo = Promo::factory()->active()->create();

                // Record the initial bookmark state (should be false — no bookmark yet)
                $initialState = Bookmark::where('user_id', $consumer->id)
                                        ->where('promo_id', $promo->id)
                                        ->exists();

                // First toggle
                $this->actingAs($consumer)
                     ->postJson(route('consumer.bookmarks.toggle', $promo))
                     ->assertOk();

                // Second toggle
                $this->actingAs($consumer)
                     ->postJson(route('consumer.bookmarks.toggle', $promo))
                     ->assertOk();

                // Final state must equal initial state
                $finalState = Bookmark::where('user_id', $consumer->id)
                                      ->where('promo_id', $promo->id)
                                      ->exists();

                $this->assertEquals(
                    $initialState,
                    $finalState,
                    "After two toggles, bookmark state should return to initial state ({$initialState}), but got ({$finalState})"
                );

                // Clean up for next iteration
                Bookmark::where('user_id', $consumer->id)->delete();
                $consumer->delete();
                $promo->forceDelete();
                $this->app['auth']->logout();
            });
    }

    /**
     * Property 5b: Toggle dua kali dari state sudah-di-bookmark juga kembali ke state awal.
     *
     * Memastikan round-trip berlaku juga ketika Consumer sudah memiliki bookmark
     * sebelum dua toggle dilakukan.
     *
     * Validates: Requirements 2.5, 11.1, 11.2
     */
    public function testBookmarkToggleTwiceFromBookmarkedStateAlwaysReturnsToOriginalState(): void
    {
        $promoIndex    = Generators::choose(1, 999999);
        $consumerIndex = Generators::choose(1, 999999);

        $this->forAll($promoIndex, $consumerIndex)
            ->withMaxSize(100)
            ->then(function (int $promoIdx, int $consumerIdx) {
                // Create a fresh consumer and promo for each iteration
                $consumer = User::factory()->consumer()->create([
                    'email' => "consumer_prop5b_{$promoIdx}_{$consumerIdx}@example.com",
                ]);
                $promo = Promo::factory()->active()->create();

                // Pre-create a bookmark so initial state is "bookmarked = true"
                Bookmark::create([
                    'user_id'  => $consumer->id,
                    'promo_id' => $promo->id,
                ]);

                $initialState = Bookmark::where('user_id', $consumer->id)
                                        ->where('promo_id', $promo->id)
                                        ->exists(); // true

                // First toggle (removes bookmark)
                $this->actingAs($consumer)
                     ->postJson(route('consumer.bookmarks.toggle', $promo))
                     ->assertOk();

                // Second toggle (re-adds bookmark)
                $this->actingAs($consumer)
                     ->postJson(route('consumer.bookmarks.toggle', $promo))
                     ->assertOk();

                // Final state must equal initial state (true)
                $finalState = Bookmark::where('user_id', $consumer->id)
                                      ->where('promo_id', $promo->id)
                                      ->exists();

                $this->assertEquals(
                    $initialState,
                    $finalState,
                    "After two toggles from bookmarked state, bookmark state should return to initial state ({$initialState}), but got ({$finalState})"
                );

                // Clean up for next iteration
                Bookmark::where('user_id', $consumer->id)->delete();
                $consumer->delete();
                $promo->forceDelete();
                $this->app['auth']->logout();
            });
    }

    // ─── Property 20: Respons JSON berisi bookmarked dan count yang akurat ────

    /**
     * Property 20: Respons JSON bookmark toggle berisi status dan count yang akurat.
     *
     * For any operasi toggle bookmark pada Promo, respons JSON SHALL berisi
     * `bookmarked` yang mencerminkan status bookmark terkini Consumer tersebut,
     * dan `count` yang sama dengan jumlah total Bookmark untuk Promo tersebut
     * di database.
     *
     * Validates: Requirements 11.3
     */
    public function testBookmarkToggleJsonResponseContainsAccurateBookmarkedAndCount(): void
    {
        $promoIndex    = Generators::choose(1, 999999);
        $consumerIndex = Generators::choose(1, 999999);
        // Number of other consumers who have already bookmarked the promo (0–4)
        $otherBookmarks = Generators::choose(0, 4);

        $this->forAll($promoIndex, $consumerIndex, $otherBookmarks)
            ->withMaxSize(100)
            ->then(function (int $promoIdx, int $consumerIdx, int $otherCount) {
                // Create a fresh consumer and promo for each iteration
                $consumer = User::factory()->consumer()->create([
                    'email' => "consumer_prop20_{$promoIdx}_{$consumerIdx}@example.com",
                ]);
                $promo = Promo::factory()->active()->create();

                // Create bookmarks from other consumers to simulate a realistic count
                $otherConsumers = [];
                for ($i = 0; $i < $otherCount; $i++) {
                    $other = User::factory()->consumer()->create([
                        'email' => "other_prop20_{$promoIdx}_{$consumerIdx}_{$i}@example.com",
                    ]);
                    Bookmark::create([
                        'user_id'  => $other->id,
                        'promo_id' => $promo->id,
                    ]);
                    $otherConsumers[] = $other;
                }

                // ── First toggle: adds bookmark ──────────────────────────────
                $response1 = $this->actingAs($consumer)
                                  ->postJson(route('consumer.bookmarks.toggle', $promo));

                $response1->assertOk()
                          ->assertJsonStructure(['bookmarked', 'count']);

                $data1 = $response1->json();

                // bookmarked must be a boolean
                $this->assertIsBool($data1['bookmarked'], "bookmarked field must be a boolean");

                // bookmarked must reflect actual DB state
                $actualBookmarked1 = Bookmark::where('user_id', $consumer->id)
                                             ->where('promo_id', $promo->id)
                                             ->exists();
                $this->assertEquals(
                    $actualBookmarked1,
                    $data1['bookmarked'],
                    "bookmarked in JSON ({$data1['bookmarked']}) must match actual DB state ({$actualBookmarked1})"
                );

                // count must be an integer >= 0
                $this->assertIsInt($data1['count'], "count field must be an integer");
                $this->assertGreaterThanOrEqual(0, $data1['count'], "count must be >= 0");

                // count must match actual DB count
                $actualCount1 = Bookmark::where('promo_id', $promo->id)->count();
                $this->assertEquals(
                    $actualCount1,
                    $data1['count'],
                    "count in JSON ({$data1['count']}) must match actual DB count ({$actualCount1})"
                );

                // ── Second toggle: removes bookmark ──────────────────────────
                $response2 = $this->actingAs($consumer)
                                  ->postJson(route('consumer.bookmarks.toggle', $promo));

                $response2->assertOk()
                          ->assertJsonStructure(['bookmarked', 'count']);

                $data2 = $response2->json();

                // bookmarked must be a boolean
                $this->assertIsBool($data2['bookmarked'], "bookmarked field must be a boolean");

                // bookmarked must reflect actual DB state
                $actualBookmarked2 = Bookmark::where('user_id', $consumer->id)
                                             ->where('promo_id', $promo->id)
                                             ->exists();
                $this->assertEquals(
                    $actualBookmarked2,
                    $data2['bookmarked'],
                    "bookmarked in JSON ({$data2['bookmarked']}) must match actual DB state ({$actualBookmarked2})"
                );

                // count must be an integer >= 0
                $this->assertIsInt($data2['count'], "count field must be an integer");
                $this->assertGreaterThanOrEqual(0, $data2['count'], "count must be >= 0");

                // count must match actual DB count
                $actualCount2 = Bookmark::where('promo_id', $promo->id)->count();
                $this->assertEquals(
                    $actualCount2,
                    $data2['count'],
                    "count in JSON ({$data2['count']}) must match actual DB count ({$actualCount2})"
                );

                // Clean up for next iteration
                Bookmark::where('promo_id', $promo->id)->delete();
                foreach ($otherConsumers as $other) {
                    $other->delete();
                }
                $consumer->delete();
                $promo->forceDelete();
                $this->app['auth']->logout();
            });
    }
}
