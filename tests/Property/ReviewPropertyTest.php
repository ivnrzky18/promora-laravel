<?php

namespace Tests\Property;

use App\Models\Review;
use App\Models\SellerProfile;
use App\Models\User;
use Eris\Generators;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Property-Based Tests for Review System
 *
 * Validates: Requirements 10.3, 10.4
 */
class ReviewPropertyTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    // ─── Property 18: Review duplikat ditolak → count review tidak bertambah ──

    /**
     * Property 18: Duplicate review submission is always rejected.
     *
     * For any Consumer and Seller, if a Consumer has already submitted a Review
     * for that Seller, any subsequent Review submission by the same Consumer for
     * the same Seller SHALL be rejected and the total review count for that Seller
     * SHALL NOT increase.
     *
     * Validates: Requirements 10.3
     */
    public function testDuplicateReviewIsAlwaysRejectedAndCountDoesNotIncrease(): void
    {
        $consumerIndex = Generators::choose(1, 999999);
        $sellerIndex   = Generators::choose(1, 999999);
        $rating        = Generators::choose(1, 5);

        $this->forAll($consumerIndex, $sellerIndex, $rating)
            ->withMaxSize(100)
            ->then(function (int $consumerIdx, int $sellerIdx, int $rating) {
                // Create a fresh consumer and seller for each iteration
                $consumer = User::factory()->consumer()->create([
                    'email' => "consumer_prop18_{$consumerIdx}_{$sellerIdx}@example.com",
                ]);
                $seller = SellerProfile::factory()->create();

                // Submit the first review (should succeed)
                $this->actingAs($consumer)
                     ->post(route('reviews.store'), [
                         'seller_id' => $seller->id,
                         'rating'    => $rating,
                         'comment'   => 'Ulasan pertama.',
                     ]);

                // Verify the first review was stored
                $this->assertEquals(
                    1,
                    Review::where('user_id', $consumer->id)
                          ->where('seller_id', $seller->id)
                          ->count(),
                    "First review should be stored successfully"
                );

                // Record count before duplicate attempt
                $countBefore = Review::where('seller_id', $seller->id)->count();

                // Submit a duplicate review (should be rejected)
                $duplicateRating = ($rating % 5) + 1; // different rating, same seller
                $response = $this->actingAs($consumer)
                                 ->post(route('reviews.store'), [
                                     'seller_id' => $seller->id,
                                     'rating'    => $duplicateRating,
                                     'comment'   => 'Ulasan duplikat.',
                                 ]);

                // The response should redirect back with an error
                $response->assertRedirect();
                $response->assertSessionHasErrors('review');

                // Property: review count must NOT have increased
                $countAfter = Review::where('seller_id', $seller->id)->count();

                $this->assertEquals(
                    $countBefore,
                    $countAfter,
                    "After a duplicate review submission, the review count for seller {$seller->id} "
                    . "should remain {$countBefore}, but got {$countAfter}"
                );

                // Clean up for next iteration
                Review::where('seller_id', $seller->id)->delete();
                $seller->user->delete(); // cascades to SellerProfile
                $consumer->delete();
                $this->app['auth']->logout();
            });
    }

    // ─── Property 19: averageRating = sum(rating) / count(reviews) ───────────

    /**
     * Property 19: averageRating() always equals sum(rating) / count(reviews).
     *
     * For any SellerProfile with any set of Reviews, the value returned by
     * averageRating() SHALL always equal the arithmetic mean of all ratings:
     * sum(rating) / count(reviews). When there are no reviews, averageRating()
     * SHALL return 0.0.
     *
     * Validates: Requirements 10.4
     */
    public function testAverageRatingAlwaysEqualsSumDividedByCount(): void
    {
        // Number of reviews: 0 to 10 to cover edge cases including empty set
        $reviewCount   = Generators::choose(0, 10);
        $sellerIndex   = Generators::choose(1, 999999);

        $this->forAll($reviewCount, $sellerIndex)
            ->withMaxSize(100)
            ->then(function (int $n, int $sellerIdx) {
                // Create a fresh seller for each iteration
                $seller = SellerProfile::factory()->create();

                // Create N reviews with random ratings (1–5)
                $ratings = [];
                for ($i = 0; $i < $n; $i++) {
                    $rating = rand(1, 5);
                    $ratings[] = $rating;

                    $consumer = User::factory()->consumer()->create([
                        'email' => "consumer_prop19_{$sellerIdx}_{$i}@example.com",
                    ]);

                    Review::create([
                        'user_id'   => $consumer->id,
                        'seller_id' => $seller->id,
                        'rating'    => $rating,
                        'comment'   => null,
                    ]);
                }

                // Calculate expected average
                if ($n === 0) {
                    $expectedAverage = 0.0;
                } else {
                    $expectedAverage = array_sum($ratings) / count($ratings);
                }

                // Get actual average from the model method
                $actualAverage = $seller->fresh()->averageRating();

                // Property: averageRating() must equal sum/count (within float precision)
                $this->assertEqualsWithDelta(
                    $expectedAverage,
                    $actualAverage,
                    0.0001,
                    "averageRating() for seller {$seller->id} with {$n} review(s) "
                    . "should be {$expectedAverage} (sum=" . array_sum($ratings) . ", count={$n}), "
                    . "but got {$actualAverage}"
                );

                // Also verify: when no reviews, must return exactly 0.0
                if ($n === 0) {
                    $this->assertSame(
                        0.0,
                        $actualAverage,
                        "averageRating() with no reviews should return exactly 0.0"
                    );
                }

                // Clean up for next iteration
                Review::where('seller_id', $seller->id)->delete();
                $seller->user->delete(); // cascades to SellerProfile
            });
    }
}
