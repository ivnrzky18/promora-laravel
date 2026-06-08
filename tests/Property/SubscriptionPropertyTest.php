<?php

namespace Tests\Property;

use App\Models\Promo;
use App\Models\SellerProfile;
use App\Models\Subscription;
use App\Models\User;
use Eris\Generators;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Property-Based Tests for Subscription and Notification
 *
 * Validates: Requirements 7.2, 7.3, 7.4
 */
class SubscriptionPropertyTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    // ─── Property 11: Subscribe lalu unsubscribe → tidak ada entri Subscription ─

    /**
     * Property 11: Subscribe then unsubscribe always results in no Subscription entry.
     *
     * For any Consumer and Seller, calling the subscription toggle twice
     * (subscribe then unsubscribe) SHALL always leave no Subscription entry
     * in the database for that Consumer–Seller pair.
     *
     * Validates: Requirements 7.2, 7.3
     */
    public function testSubscribeThenUnsubscribeAlwaysLeavesNoSubscriptionEntry(): void
    {
        $consumerIndex = Generators::choose(1, 999999);
        $sellerIndex   = Generators::choose(1, 999999);

        $this->forAll($consumerIndex, $sellerIndex)
            ->withMaxSize(100)
            ->then(function (int $consumerIdx, int $sellerIdx) {
                // Create a fresh consumer and seller for each iteration
                $consumer = User::factory()->consumer()->create([
                    'email' => "consumer_prop11_{$consumerIdx}_{$sellerIdx}@example.com",
                ]);

                $seller = SellerProfile::factory()->create();

                // Precondition: no subscription exists
                $this->assertFalse(
                    Subscription::where('user_id', $consumer->id)
                                ->where('seller_id', $seller->id)
                                ->exists(),
                    "No subscription should exist before the test"
                );

                // First toggle: subscribe
                $response1 = $this->actingAs($consumer)
                                  ->postJson(route('consumer.subscriptions.toggle', $seller));

                $response1->assertOk()
                          ->assertJson(['subscribed' => true]);

                // Verify subscription was created
                $this->assertTrue(
                    Subscription::where('user_id', $consumer->id)
                                ->where('seller_id', $seller->id)
                                ->exists(),
                    "Subscription should exist after first toggle (subscribe)"
                );

                // Second toggle: unsubscribe
                $response2 = $this->actingAs($consumer)
                                  ->postJson(route('consumer.subscriptions.toggle', $seller));

                $response2->assertOk()
                          ->assertJson(['subscribed' => false]);

                // Property: no subscription entry should remain
                $this->assertFalse(
                    Subscription::where('user_id', $consumer->id)
                                ->where('seller_id', $seller->id)
                                ->exists(),
                    "After subscribe then unsubscribe, no Subscription entry should remain in the database"
                );

                // Clean up for next iteration
                $seller->user->delete(); // cascades to SellerProfile
                $consumer->delete();
                $this->app['auth']->logout();
            });
    }

    // ─── Property 12: Approve promo dengan N subscriber → tepat N notifikasi ──

    /**
     * Property 12: Approving a promo with N subscribers always creates exactly N notifications.
     *
     * For any number N of subscribers to a Seller, when an Admin approves a Promo
     * belonging to that Seller, THE System SHALL create exactly N database notifications
     * (one per subscriber).
     *
     * Validates: Requirements 7.4
     */
    public function testApprovePromoWithNSubscribersAlwaysCreatesExactlyNNotifications(): void
    {
        // N ranges from 0 to 5 subscribers to keep tests fast while covering edge cases
        $subscriberCount = Generators::choose(0, 5);
        $iterationIndex  = Generators::choose(1, 999999);

        $this->forAll($subscriberCount, $iterationIndex)
            ->withMaxSize(100)
            ->then(function (int $n, int $idx) {
                // Create a seller with a draft promo
                $seller = SellerProfile::factory()->create();
                $promo  = Promo::factory()->draft()->create([
                    'seller_id' => $seller->id,
                ]);

                // Create N consumers and subscribe each to the seller
                $consumers = [];
                for ($i = 0; $i < $n; $i++) {
                    $consumer = User::factory()->consumer()->create([
                        'email' => "sub_prop12_{$idx}_{$i}@example.com",
                    ]);
                    Subscription::create([
                        'user_id'   => $consumer->id,
                        'seller_id' => $seller->id,
                    ]);
                    $consumers[] = $consumer;
                }

                // Verify N subscriptions exist
                $this->assertEquals(
                    $n,
                    Subscription::where('seller_id', $seller->id)->count(),
                    "Should have exactly {$n} subscriptions before approval"
                );

                // Create an admin and approve the promo
                $admin = User::factory()->admin()->create([
                    'email' => "admin_prop12_{$idx}@example.com",
                ]);

                $this->actingAs($admin)
                     ->post(route('admin.promos.approve', $promo))
                     ->assertRedirect();

                // Verify promo status is now active
                $this->assertEquals(
                    'active',
                    $promo->fresh()->status,
                    "Promo status should be 'active' after approval"
                );

                // Property: exactly N notifications should have been created
                $totalNotifications = 0;
                foreach ($consumers as $consumer) {
                    $totalNotifications += $consumer->notifications()->count();
                }

                $this->assertEquals(
                    $n,
                    $totalNotifications,
                    "Approving a promo with {$n} subscriber(s) should create exactly {$n} notification(s), but got {$totalNotifications}"
                );

                // Also verify each subscriber received exactly 1 notification
                foreach ($consumers as $consumer) {
                    $count = $consumer->notifications()->count();
                    $this->assertEquals(
                        1,
                        $count,
                        "Each subscriber should receive exactly 1 notification, but consumer {$consumer->id} received {$count}"
                    );
                }

                // Clean up for next iteration
                foreach ($consumers as $consumer) {
                    $consumer->notifications()->delete();
                    $consumer->delete();
                }
                $admin->delete();
                $promo->forceDelete();
                $seller->user->delete(); // cascades to SellerProfile
                $this->app['auth']->logout();
            });
    }
}
