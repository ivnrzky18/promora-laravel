<?php

namespace Tests\Feature\Consumer;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Promo;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookmarkTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that toggling bookmark creates a new bookmark (returns bookmarked=true).
     */
    public function test_toggle_creates_bookmark_returns_bookmarked_true(): void
    {
        $consumer = User::factory()->consumer()->create();
        $promo    = Promo::factory()->active()->create();

        $response = $this->actingAs($consumer)
            ->postJson(route('consumer.bookmarks.toggle', $promo));

        $response->assertStatus(200);
        $response->assertJson(['bookmarked' => true]);

        $this->assertDatabaseHas('bookmarks', [
            'user_id'  => $consumer->id,
            'promo_id' => $promo->id,
        ]);
    }

    /**
     * Test that toggling bookmark again deletes it (returns bookmarked=false).
     */
    public function test_toggle_again_deletes_bookmark_returns_bookmarked_false(): void
    {
        $consumer = User::factory()->consumer()->create();
        $promo    = Promo::factory()->active()->create();

        // Create the bookmark first
        Bookmark::create(['user_id' => $consumer->id, 'promo_id' => $promo->id]);

        $response = $this->actingAs($consumer)
            ->postJson(route('consumer.bookmarks.toggle', $promo));

        $response->assertStatus(200);
        $response->assertJson(['bookmarked' => false]);

        $this->assertDatabaseMissing('bookmarks', [
            'user_id'  => $consumer->id,
            'promo_id' => $promo->id,
        ]);
    }

    /**
     * Test that JSON response contains both bookmarked and count fields.
     */
    public function test_json_response_contains_bookmarked_and_count(): void
    {
        $consumer = User::factory()->consumer()->create();
        $promo    = Promo::factory()->active()->create();

        // Add a bookmark from another user to verify count
        $otherConsumer = User::factory()->consumer()->create();
        Bookmark::create(['user_id' => $otherConsumer->id, 'promo_id' => $promo->id]);

        $response = $this->actingAs($consumer)
            ->postJson(route('consumer.bookmarks.toggle', $promo));

        $response->assertStatus(200);
        $response->assertJsonStructure(['bookmarked', 'count']);
        $response->assertJson([
            'bookmarked' => true,
            'count'      => 2, // 1 existing + 1 new
        ]);
    }

    /**
     * Test that the bookmarks page shows saved promos.
     */
    public function test_bookmarks_page_shows_saved_promos(): void
    {
        $consumer = User::factory()->consumer()->create();
        $category = Category::factory()->create();
        $seller   = SellerProfile::factory()->create();

        $promo = Promo::factory()->active()->create([
            'seller_id'   => $seller->id,
            'category_id' => $category->id,
            'title'       => 'Promo Tersimpan',
        ]);

        Bookmark::create(['user_id' => $consumer->id, 'promo_id' => $promo->id]);

        $response = $this->actingAs($consumer)->get(route('consumer.bookmarks'));

        $response->assertStatus(200);
        $response->assertViewHas('bookmarks', function ($bookmarks) use ($promo) {
            return $bookmarks->contains(function ($bookmark) use ($promo) {
                return $bookmark->promo && $bookmark->promo->id === $promo->id;
            });
        });
    }

    /**
     * Test that soft-deleted promo shows "Tidak Tersedia" on bookmarks page.
     */
    public function test_soft_deleted_promo_shows_tidak_tersedia(): void
    {
        $consumer = User::factory()->consumer()->create();
        $promo    = Promo::factory()->active()->create();

        Bookmark::create(['user_id' => $consumer->id, 'promo_id' => $promo->id]);

        // Soft delete the promo
        $promo->delete();

        $response = $this->actingAs($consumer)->get(route('consumer.bookmarks'));

        $response->assertStatus(200);
        $response->assertSee('Tidak Tersedia');

        // Verify the bookmark still loads the soft-deleted promo
        $response->assertViewHas('bookmarks', function ($bookmarks) use ($promo) {
            return $bookmarks->contains(function ($bookmark) use ($promo) {
                return $bookmark->promo && $bookmark->promo->id === $promo->id;
            });
        });
    }

    /**
     * Test that unauthenticated users are redirected when trying to toggle bookmark.
     */
    public function test_unauthenticated_user_cannot_toggle_bookmark(): void
    {
        $promo = Promo::factory()->active()->create();

        $response = $this->postJson(route('consumer.bookmarks.toggle', $promo));

        // Should redirect (middleware redirects to login)
        $response->assertStatus(302);
    }

    /**
     * Test that expired promo shows "Tidak Tersedia" on bookmarks page.
     */
    public function test_expired_promo_shows_tidak_tersedia(): void
    {
        $consumer = User::factory()->consumer()->create();
        $promo    = Promo::factory()->expired()->create();

        Bookmark::create(['user_id' => $consumer->id, 'promo_id' => $promo->id]);

        $response = $this->actingAs($consumer)->get(route('consumer.bookmarks'));

        $response->assertStatus(200);
        $response->assertSee('Tidak Tersedia');
    }
}
