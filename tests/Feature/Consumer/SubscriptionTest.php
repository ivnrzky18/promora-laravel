<?php

namespace Tests\Feature\Consumer;

use App\Models\SellerProfile;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that toggling subscription creates a Subscription entry.
     */
    public function test_toggle_subscribe_creates_subscription_entry(): void
    {
        $consumer = User::factory()->consumer()->create();
        $seller   = SellerProfile::factory()->create();

        $response = $this->actingAs($consumer)
            ->postJson(route('consumer.subscriptions.toggle', $seller));

        $response->assertStatus(200);
        $response->assertJson(['subscribed' => true]);

        $this->assertDatabaseHas('subscriptions', [
            'user_id'   => $consumer->id,
            'seller_id' => $seller->id,
        ]);
    }

    /**
     * Test that toggling subscription again deletes the Subscription entry.
     */
    public function test_toggle_unsubscribe_deletes_subscription_entry(): void
    {
        $consumer = User::factory()->consumer()->create();
        $seller   = SellerProfile::factory()->create();

        // Create the subscription first
        Subscription::create(['user_id' => $consumer->id, 'seller_id' => $seller->id]);

        $response = $this->actingAs($consumer)
            ->postJson(route('consumer.subscriptions.toggle', $seller));

        $response->assertStatus(200);
        $response->assertJson(['subscribed' => false]);

        $this->assertDatabaseMissing('subscriptions', [
            'user_id'   => $consumer->id,
            'seller_id' => $seller->id,
        ]);
    }

    /**
     * Test that JSON response contains the subscribed field.
     */
    public function test_json_response_contains_subscribed_field(): void
    {
        $consumer = User::factory()->consumer()->create();
        $seller   = SellerProfile::factory()->create();

        $response = $this->actingAs($consumer)
            ->postJson(route('consumer.subscriptions.toggle', $seller));

        $response->assertStatus(200);
        $response->assertJsonStructure(['subscribed']);
    }

    /**
     * Test that unauthenticated users cannot toggle subscription.
     */
    public function test_unauthenticated_user_cannot_toggle_subscription(): void
    {
        $seller = SellerProfile::factory()->create();

        $response = $this->postJson(route('consumer.subscriptions.toggle', $seller));

        $response->assertStatus(302);
    }

    /**
     * Test that double-toggling returns to original state (no subscription).
     */
    public function test_double_toggle_returns_to_original_state(): void
    {
        $consumer = User::factory()->consumer()->create();
        $seller   = SellerProfile::factory()->create();

        // Subscribe
        $this->actingAs($consumer)
            ->postJson(route('consumer.subscriptions.toggle', $seller));

        // Unsubscribe
        $this->actingAs($consumer)
            ->postJson(route('consumer.subscriptions.toggle', $seller));

        $this->assertDatabaseMissing('subscriptions', [
            'user_id'   => $consumer->id,
            'seller_id' => $seller->id,
        ]);
    }
}
