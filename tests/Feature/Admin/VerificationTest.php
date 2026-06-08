<?php

namespace Tests\Feature\Admin;

use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    /**
     * Test that verifying a seller sets is_verified to true.
     */
    public function test_verify_seller_sets_is_verified_true(): void
    {
        $sellerUser = User::factory()->seller()->create();
        $seller = SellerProfile::factory()->create([
            'user_id'     => $sellerUser->id,
            'is_verified' => false,
        ]);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.sellers.verify', $seller));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('seller_profiles', [
            'id'          => $seller->id,
            'is_verified' => true,
        ]);
    }

    /**
     * Test that rejecting a seller deletes the user (and cascades to SellerProfile).
     */
    public function test_reject_seller_deletes_user(): void
    {
        $sellerUser = User::factory()->seller()->create();
        $seller = SellerProfile::factory()->create([
            'user_id'     => $sellerUser->id,
            'is_verified' => false,
        ]);

        $sellerId = $seller->id;
        $userId   = $sellerUser->id;

        $this->actingAs($this->admin);

        $response = $this->delete(route('admin.sellers.reject', $seller));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // User should be deleted
        $this->assertDatabaseMissing('users', ['id' => $userId]);

        // SellerProfile should also be gone (cascade)
        $this->assertDatabaseMissing('seller_profiles', ['id' => $sellerId]);
    }

    /**
     * Test that a non-admin cannot access the verify endpoint.
     */
    public function test_non_admin_cannot_verify_seller(): void
    {
        $consumer = User::factory()->consumer()->create();
        $sellerUser = User::factory()->seller()->create();
        $seller = SellerProfile::factory()->create([
            'user_id'     => $sellerUser->id,
            'is_verified' => false,
        ]);

        $this->actingAs($consumer);

        $response = $this->post(route('admin.sellers.verify', $seller));

        // Should be redirected away (middleware)
        $response->assertRedirect();

        // Seller should still be unverified
        $this->assertDatabaseHas('seller_profiles', [
            'id'          => $seller->id,
            'is_verified' => false,
        ]);
    }

    /**
     * Test that the admin dashboard shows pending sellers.
     */
    public function test_admin_dashboard_shows_pending_sellers(): void
    {
        $sellerUser = User::factory()->seller()->create();
        SellerProfile::factory()->create([
            'user_id'     => $sellerUser->id,
            'is_verified' => false,
        ]);

        $this->actingAs($this->admin);

        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHas('pendingSellers');
    }
}
