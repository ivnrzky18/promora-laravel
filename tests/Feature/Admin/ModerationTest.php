<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Promo;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModerationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private SellerProfile $sellerProfile;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();

        $sellerUser = User::factory()->seller()->create();
        $this->sellerProfile = SellerProfile::factory()->create([
            'user_id'     => $sellerUser->id,
            'is_verified' => true,
        ]);
        $this->category = Category::factory()->create();
    }

    /**
     * Test that approving a promo sets its status to active.
     */
    public function test_approve_promo_sets_status_active(): void
    {
        $promo = Promo::factory()->draft()->create([
            'seller_id'   => $this->sellerProfile->id,
            'category_id' => $this->category->id,
        ]);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.promos.approve', $promo));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('promos', [
            'id'     => $promo->id,
            'status' => 'active',
        ]);
    }

    /**
     * Test that rejecting a promo force-deletes it (not soft delete).
     */
    public function test_reject_promo_deletes_promo(): void
    {
        $promo = Promo::factory()->draft()->create([
            'seller_id'   => $this->sellerProfile->id,
            'category_id' => $this->category->id,
        ]);

        $promoId = $promo->id;

        $this->actingAs($this->admin);

        $response = $this->delete(route('admin.promos.reject', $promo));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Promo should be completely gone (force deleted, not soft deleted)
        $this->assertDatabaseMissing('promos', ['id' => $promoId]);
    }

    /**
     * Test that the promos moderation page shows draft promos.
     */
    public function test_promos_index_shows_draft_promos(): void
    {
        Promo::factory()->draft()->create([
            'seller_id'   => $this->sellerProfile->id,
            'category_id' => $this->category->id,
        ]);

        $this->actingAs($this->admin);

        $response = $this->get(route('admin.promos.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.promos.index');
        $response->assertViewHas('promos');
    }

    /**
     * Test that a non-admin cannot approve promos.
     */
    public function test_non_admin_cannot_approve_promo(): void
    {
        $consumer = User::factory()->consumer()->create();
        $promo = Promo::factory()->draft()->create([
            'seller_id'   => $this->sellerProfile->id,
            'category_id' => $this->category->id,
        ]);

        $this->actingAs($consumer);

        $response = $this->post(route('admin.promos.approve', $promo));

        // Should be redirected away (middleware)
        $response->assertRedirect();

        // Promo should still be draft
        $this->assertDatabaseHas('promos', [
            'id'     => $promo->id,
            'status' => 'draft',
        ]);
    }
}
