<?php

namespace Tests\Feature\Consumer;

use App\Models\Category;
use App\Models\Promo;
use App\Models\SellerProfile;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\NewPromoNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that approving a promo creates N notifications for N subscribers.
     */
    public function test_approving_promo_creates_notifications_for_all_subscribers(): void
    {
        Notification::fake();

        $admin    = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $sellerUser = User::factory()->seller()->create();
        $seller     = SellerProfile::factory()->create(['user_id' => $sellerUser->id]);

        $promo = Promo::factory()->draft()->create([
            'seller_id'   => $seller->id,
            'category_id' => $category->id,
        ]);

        // Create 3 subscribers
        $subscribers = User::factory()->consumer()->count(3)->create();
        foreach ($subscribers as $subscriber) {
            Subscription::create(['user_id' => $subscriber->id, 'seller_id' => $seller->id]);
        }

        $this->actingAs($admin)
            ->post(route('admin.promos.approve', $promo));

        // Each subscriber should receive exactly one notification
        foreach ($subscribers as $subscriber) {
            Notification::assertSentTo($subscriber, NewPromoNotification::class);
        }

        Notification::assertCount(3);
    }

    /**
     * Test that approving a promo with N subscribers creates exactly N database notifications.
     */
    public function test_approving_promo_creates_n_database_notifications_for_n_subscribers(): void
    {
        $admin    = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $sellerUser = User::factory()->seller()->create();
        $seller     = SellerProfile::factory()->create(['user_id' => $sellerUser->id]);

        $promo = Promo::factory()->draft()->create([
            'seller_id'   => $seller->id,
            'category_id' => $category->id,
        ]);

        // Create 4 subscribers
        $subscribers = User::factory()->consumer()->count(4)->create();
        foreach ($subscribers as $subscriber) {
            Subscription::create(['user_id' => $subscriber->id, 'seller_id' => $seller->id]);
        }

        $this->actingAs($admin)
            ->post(route('admin.promos.approve', $promo));

        // Exactly 4 notifications should be in the database
        $this->assertDatabaseCount('notifications', 4);
    }

    /**
     * Test that marking a notification as read sets read_at.
     */
    public function test_mark_read_sets_read_at(): void
    {
        $consumer = User::factory()->consumer()->create();

        // Manually create a database notification
        $consumer->notify(new NewPromoNotification(
            Promo::factory()->active()->create()
        ));

        $notification = $consumer->notifications()->first();

        $this->assertNull($notification->read_at);

        $response = $this->actingAs($consumer)
            ->postJson(route('consumer.notifications.read', $notification->id));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertNotNull($consumer->notifications()->find($notification->id)->read_at);
    }

    /**
     * Test that unread count equals count of notifications with read_at = null.
     */
    public function test_unread_count_equals_notifications_with_null_read_at(): void
    {
        $consumer = User::factory()->consumer()->create();

        // Create 5 notifications
        for ($i = 0; $i < 5; $i++) {
            $consumer->notify(new NewPromoNotification(
                Promo::factory()->active()->create()
            ));
        }

        // Mark 2 as read
        $notifications = $consumer->notifications()->take(2)->get();
        foreach ($notifications as $notification) {
            $notification->markAsRead();
        }

        $unreadCount = $consumer->unreadNotifications()->count();
        $nullReadAtCount = $consumer->notifications()->whereNull('read_at')->count();

        $this->assertEquals(3, $unreadCount);
        $this->assertEquals($nullReadAtCount, $unreadCount);
    }

    /**
     * Test that the notifications page is accessible to authenticated consumers.
     */
    public function test_notifications_page_is_accessible(): void
    {
        $consumer = User::factory()->consumer()->create();

        $response = $this->actingAs($consumer)
            ->get(route('consumer.notifications'));

        $response->assertStatus(200);
        $response->assertViewIs('consumer.notifications');
    }

    /**
     * Test that the notifications page shows the correct notifications.
     */
    public function test_notifications_page_shows_notifications(): void
    {
        $consumer = User::factory()->consumer()->create();

        $promo = Promo::factory()->active()->create();
        $consumer->notify(new NewPromoNotification($promo));

        $response = $this->actingAs($consumer)
            ->get(route('consumer.notifications'));

        $response->assertStatus(200);
        $response->assertViewHas('notifications', function ($notifications) {
            return $notifications->count() === 1;
        });
    }

    /**
     * Test that unauthenticated users cannot access notifications page.
     */
    public function test_unauthenticated_user_cannot_access_notifications(): void
    {
        $response = $this->get(route('consumer.notifications'));

        $response->assertRedirect(route('consumer.login'));
    }

    /**
     * Test that approving a promo with no subscribers creates no notifications.
     */
    public function test_approving_promo_with_no_subscribers_creates_no_notifications(): void
    {
        Notification::fake();

        $admin    = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $sellerUser = User::factory()->seller()->create();
        $seller     = SellerProfile::factory()->create(['user_id' => $sellerUser->id]);

        $promo = Promo::factory()->draft()->create([
            'seller_id'   => $seller->id,
            'category_id' => $category->id,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.promos.approve', $promo));

        Notification::assertNothingSent();
    }
}
