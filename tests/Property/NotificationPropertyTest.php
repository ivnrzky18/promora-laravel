<?php

namespace Tests\Property;

use App\Models\User;
use App\Notifications\NewPromoNotification;
use App\Models\Promo;
use App\Models\SellerProfile;
use Eris\Generators;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Property-Based Tests for Notification Unread Count
 *
 * Validates: Requirements 7.7
 */
class NotificationPropertyTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    // ─── Property 13: unread count = count(notifications WHERE read_at IS NULL) ─

    /**
     * Property 13: Unread notification count always equals the count of
     * notifications where read_at IS NULL.
     *
     * For any Consumer with any mix of read and unread notifications,
     * $user->unreadNotifications()->count() SHALL always equal the number
     * of rows in the notifications table for that user where read_at IS NULL.
     *
     * Validates: Requirements 7.7
     */
    public function testUnreadCountAlwaysEqualsNotificationsWhereReadAtIsNull(): void
    {
        // Number of unread notifications (0–5)
        $unreadCount = Generators::choose(0, 5);
        // Number of read notifications (0–5)
        $readCount = Generators::choose(0, 5);
        // Iteration index for unique emails
        $iterationIndex = Generators::choose(1, 999999);

        $this->forAll($unreadCount, $readCount, $iterationIndex)
            ->withMaxSize(100)
            ->then(function (int $numUnread, int $numRead, int $idx) {
                // Create a consumer user for this iteration
                $consumer = User::factory()->consumer()->create([
                    'email' => "consumer_prop13_{$idx}_{$numUnread}_{$numRead}@example.com",
                ]);

                // Create a seller with a promo to use for notifications
                $seller = SellerProfile::factory()->create();
                $promo  = Promo::factory()->active()->create([
                    'seller_id' => $seller->id,
                ]);

                // Send $numUnread notifications (unread — read_at stays NULL)
                for ($i = 0; $i < $numUnread; $i++) {
                    $consumer->notify(new NewPromoNotification($promo));
                }

                // Send $numRead notifications and mark them as read
                for ($i = 0; $i < $numRead; $i++) {
                    $consumer->notify(new NewPromoNotification($promo));
                }

                // Mark the last $numRead notifications as read
                $consumer->unreadNotifications()
                         ->latest()
                         ->take($numRead)
                         ->get()
                         ->each(fn ($n) => $n->markAsRead());

                // ── Property assertion ────────────────────────────────────────

                // Count via Eloquent relationship (the method under test)
                $eloquentUnreadCount = $consumer->unreadNotifications()->count();

                // Count directly from DB where read_at IS NULL
                $dbUnreadCount = DB::table('notifications')
                    ->where('notifiable_type', User::class)
                    ->where('notifiable_id', $consumer->id)
                    ->whereNull('read_at')
                    ->count();

                $this->assertEquals(
                    $dbUnreadCount,
                    $eloquentUnreadCount,
                    "unreadNotifications()->count() ({$eloquentUnreadCount}) must equal " .
                    "count(notifications WHERE read_at IS NULL) ({$dbUnreadCount}) " .
                    "for user with {$numUnread} unread and {$numRead} read notifications"
                );

                // Also verify the unread count equals the expected number of unread notifications
                $this->assertEquals(
                    $numUnread,
                    $eloquentUnreadCount,
                    "Expected {$numUnread} unread notifications, but unreadNotifications()->count() returned {$eloquentUnreadCount}"
                );

                // Also verify total notification count is correct
                $totalCount = $consumer->notifications()->count();
                $this->assertEquals(
                    $numUnread + $numRead,
                    $totalCount,
                    "Total notifications should be {$numUnread} + {$numRead} = " . ($numUnread + $numRead) . ", but got {$totalCount}"
                );

                // Clean up for next iteration
                $consumer->notifications()->delete();
                $consumer->delete();
                $promo->forceDelete();
                $seller->user->delete(); // cascades to SellerProfile
                $this->app['auth']->logout();
            });
    }
}
