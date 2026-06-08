<?php

namespace Tests\Feature\Public;

use App\Models\Review;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a consumer can store a review successfully.
     */
    public function test_store_review_successfully(): void
    {
        $seller   = SellerProfile::factory()->create(['is_verified' => true]);
        $consumer = User::factory()->consumer()->create();

        $response = $this->actingAs($consumer)->post(route('reviews.store'), [
            'seller_id' => $seller->id,
            'rating'    => 4,
            'comment'   => 'Produk bagus dan pelayanan memuaskan.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Ulasan berhasil dikirim.');

        $this->assertDatabaseHas('reviews', [
            'user_id'   => $consumer->id,
            'seller_id' => $seller->id,
            'rating'    => 4,
            'comment'   => 'Produk bagus dan pelayanan memuaskan.',
        ]);
    }

    /**
     * Test that a duplicate review returns the Indonesian error message.
     */
    public function test_duplicate_review_returns_error(): void
    {
        $seller   = SellerProfile::factory()->create(['is_verified' => true]);
        $consumer = User::factory()->consumer()->create();

        // Create first review
        Review::factory()->create([
            'user_id'   => $consumer->id,
            'seller_id' => $seller->id,
            'rating'    => 5,
        ]);

        // Attempt to submit a second review
        $response = $this->actingAs($consumer)->post(route('reviews.store'), [
            'seller_id' => $seller->id,
            'rating'    => 3,
            'comment'   => 'Ulasan kedua.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['review' => 'Anda sudah memberikan ulasan untuk seller ini.']);

        // Ensure only one review exists
        $this->assertDatabaseCount('reviews', 1);
    }

    /**
     * Test that an unauthenticated user cannot submit a review.
     */
    public function test_unauthenticated_user_cannot_submit_review(): void
    {
        $seller = SellerProfile::factory()->create(['is_verified' => true]);

        $response = $this->post(route('reviews.store'), [
            'seller_id' => $seller->id,
            'rating'    => 4,
            'comment'   => 'Ulasan tanpa login.',
        ]);

        // Should redirect to consumer login
        $response->assertRedirect(route('consumer.login'));

        $this->assertDatabaseCount('reviews', 0);
    }

    /**
     * Test that rating validation rejects values outside 1-5.
     */
    public function test_rating_validation_rejects_invalid_values(): void
    {
        $seller   = SellerProfile::factory()->create(['is_verified' => true]);
        $consumer = User::factory()->consumer()->create();

        $response = $this->actingAs($consumer)->post(route('reviews.store'), [
            'seller_id' => $seller->id,
            'rating'    => 6,
            'comment'   => 'Rating tidak valid.',
        ]);

        $response->assertSessionHasErrors('rating');
        $this->assertDatabaseCount('reviews', 0);
    }

    /**
     * Test that seller_id validation rejects non-existent sellers.
     */
    public function test_seller_id_validation_rejects_nonexistent_seller(): void
    {
        $consumer = User::factory()->consumer()->create();

        $response = $this->actingAs($consumer)->post(route('reviews.store'), [
            'seller_id' => 99999,
            'rating'    => 4,
            'comment'   => 'Seller tidak ada.',
        ]);

        $response->assertSessionHasErrors('seller_id');
        $this->assertDatabaseCount('reviews', 0);
    }

    /**
     * Test that a review without a comment is accepted (comment is nullable).
     */
    public function test_review_without_comment_is_accepted(): void
    {
        $seller   = SellerProfile::factory()->create(['is_verified' => true]);
        $consumer = User::factory()->consumer()->create();

        $response = $this->actingAs($consumer)->post(route('reviews.store'), [
            'seller_id' => $seller->id,
            'rating'    => 5,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'user_id'   => $consumer->id,
            'seller_id' => $seller->id,
            'rating'    => 5,
            'comment'   => null,
        ]);
    }
}
