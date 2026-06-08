<?php

namespace Tests\Feature\Foundation;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Event;
use App\Models\Promo;
use App\Models\Review;
use App\Models\SellerProfile;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_table_has_role_column(): void
    {
        $user = User::factory()->consumer()->create();

        $this->assertEquals('consumer', $user->role);
        $this->assertTrue($user->isConsumer());
        $this->assertFalse($user->isSeller());
        $this->assertFalse($user->isAdmin());
    }

    public function test_user_role_states(): void
    {
        $consumer = User::factory()->consumer()->create();
        $seller = User::factory()->seller()->create();
        $admin = User::factory()->admin()->create();

        $this->assertEquals('consumer', $consumer->role);
        $this->assertEquals('seller', $seller->role);
        $this->assertEquals('admin', $admin->role);
    }

    public function test_seller_profile_can_be_created(): void
    {
        $user = User::factory()->seller()->create();

        $profile = SellerProfile::create([
            'user_id' => $user->id,
            'business_name' => 'Test Business',
            'business_category' => 'Kuliner',
            'address' => 'Jl. Test No. 1',
            'is_verified' => true,
        ]);

        $this->assertDatabaseHas('seller_profiles', [
            'user_id' => $user->id,
            'business_name' => 'Test Business',
        ]);

        $this->assertEquals($user->id, $profile->user->id);
    }

    public function test_category_can_be_created(): void
    {
        $category = Category::create([
            'name' => 'Kuliner',
            'slug' => 'kuliner',
            'icon' => '🍜',
        ]);

        $this->assertDatabaseHas('categories', ['slug' => 'kuliner']);
    }

    public function test_promo_can_be_created_with_relations(): void
    {
        $user = User::factory()->seller()->create();
        $profile = SellerProfile::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create();

        $promo = Promo::create([
            'seller_id' => $profile->id,
            'category_id' => $category->id,
            'title' => 'Test Promo',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
            'status' => 'draft',
        ]);

        $this->assertEquals('draft', $promo->status);
        $this->assertEquals($profile->id, $promo->seller->id);
        $this->assertEquals($category->id, $promo->category->id);
    }

    public function test_promo_active_scope(): void
    {
        $user = User::factory()->seller()->create();
        $profile = SellerProfile::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create();

        Promo::factory()->active()->create(['seller_id' => $profile->id, 'category_id' => $category->id]);
        Promo::factory()->draft()->create(['seller_id' => $profile->id, 'category_id' => $category->id]);
        Promo::factory()->expired()->create(['seller_id' => $profile->id, 'category_id' => $category->id]);

        $activePromos = Promo::active()->get();

        $this->assertCount(1, $activePromos);
        $this->assertEquals('active', $activePromos->first()->status);
    }

    public function test_promo_hot_deals_scope(): void
    {
        $user = User::factory()->seller()->create();
        $profile = SellerProfile::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create();

        // Hot deal: active, ending within 48 hours
        Promo::factory()->hotDeal()->create(['seller_id' => $profile->id, 'category_id' => $category->id]);
        // Active but not ending soon
        Promo::factory()->active()->create([
            'seller_id' => $profile->id,
            'category_id' => $category->id,
            'end_date' => now()->addDays(10),
        ]);

        $hotDeals = Promo::hotDeals()->get();

        $this->assertCount(1, $hotDeals);
    }

    public function test_seller_profile_average_rating(): void
    {
        $user = User::factory()->seller()->create();
        $profile = SellerProfile::factory()->create(['user_id' => $user->id]);

        $this->assertEquals(0.0, $profile->averageRating());

        $consumer1 = User::factory()->consumer()->create();
        $consumer2 = User::factory()->consumer()->create();

        Review::create([
            'user_id' => $consumer1->id,
            'seller_id' => $profile->id,
            'rating' => 4,
            'comment' => 'Bagus',
        ]);

        Review::create([
            'user_id' => $consumer2->id,
            'seller_id' => $profile->id,
            'rating' => 2,
            'comment' => 'Kurang',
        ]);

        $profile->refresh();
        $this->assertEquals(3.0, $profile->averageRating());
    }

    public function test_bookmark_unique_constraint(): void
    {
        $user = User::factory()->consumer()->create();
        $profile = SellerProfile::factory()->create();
        $category = Category::factory()->create();
        $promo = Promo::factory()->active()->create([
            'seller_id' => $profile->id,
            'category_id' => $category->id,
        ]);

        Bookmark::create(['user_id' => $user->id, 'promo_id' => $promo->id]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        Bookmark::create(['user_id' => $user->id, 'promo_id' => $promo->id]);
    }

    public function test_subscription_unique_constraint(): void
    {
        $user = User::factory()->consumer()->create();
        $profile = SellerProfile::factory()->create();

        Subscription::create(['user_id' => $user->id, 'seller_id' => $profile->id]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        Subscription::create(['user_id' => $user->id, 'seller_id' => $profile->id]);
    }

    public function test_review_unique_constraint(): void
    {
        $user = User::factory()->consumer()->create();
        $profile = SellerProfile::factory()->create();

        Review::create([
            'user_id' => $user->id,
            'seller_id' => $profile->id,
            'rating' => 5,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        Review::create([
            'user_id' => $user->id,
            'seller_id' => $profile->id,
            'rating' => 3,
        ]);
    }

    public function test_event_can_be_created(): void
    {
        $profile = SellerProfile::factory()->create();

        $event = Event::create([
            'seller_id' => $profile->id,
            'title' => 'Test Event',
            'event_date' => now()->addDays(7),
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('events', ['title' => 'Test Event']);
        $this->assertEquals($profile->id, $event->seller->id);
    }

    public function test_user_helper_methods(): void
    {
        $consumer = User::factory()->consumer()->create();
        $seller = User::factory()->seller()->create();
        $admin = User::factory()->admin()->create();

        $this->assertTrue($consumer->isConsumer());
        $this->assertFalse($consumer->isSeller());
        $this->assertFalse($consumer->isAdmin());

        $this->assertFalse($seller->isConsumer());
        $this->assertTrue($seller->isSeller());
        $this->assertFalse($seller->isAdmin());

        $this->assertFalse($admin->isConsumer());
        $this->assertFalse($admin->isSeller());
        $this->assertTrue($admin->isAdmin());
    }
}
