<?php

namespace Tests\Feature\Seller;

use App\Models\Category;
use App\Models\Promo;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PromoManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;
    private SellerProfile $sellerProfile;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->seller()->create();
        $this->sellerProfile = SellerProfile::factory()->create(['user_id' => $this->seller->id]);
        $this->category = Category::factory()->create();
    }

    private function validPromoData(array $overrides = []): array
    {
        return array_merge([
            'title'       => 'Test Promo',
            'description' => 'Test description',
            'start_date'  => now()->toDateString(),
            'end_date'    => now()->addDays(7)->toDateString(),
            'category_id' => $this->category->id,
        ], $overrides);
    }

    public function test_seller_can_store_promo_without_image_and_status_is_draft(): void
    {
        Storage::fake('public');

        $this->actingAs($this->seller);

        $response = $this->post(route('seller.promos.store'), $this->validPromoData());

        $response->assertRedirect(route('seller.promos.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('promos', [
            'title'     => 'Test Promo',
            'seller_id' => $this->sellerProfile->id,
            'status'    => 'draft',
        ]);

        $promo = Promo::where('title', 'Test Promo')->first();
        $this->assertNull($promo->poster_image);
    }

    public function test_seller_can_store_promo_with_fake_image(): void
    {
        Storage::fake('public');

        $this->actingAs($this->seller);

        $file = UploadedFile::fake()->image('poster.jpg', 800, 600);

        $response = $this->post(route('seller.promos.store'), $this->validPromoData([
            'poster_image' => $file,
        ]));

        $response->assertRedirect(route('seller.promos.index'));

        $promo = Promo::where('title', 'Test Promo')->first();
        $this->assertNotNull($promo->poster_image);
        Storage::disk('public')->assertExists($promo->poster_image);
    }

    public function test_update_promo_deletes_old_image_when_new_image_uploaded(): void
    {
        Storage::fake('public');

        $this->actingAs($this->seller);

        // Create promo with an existing image
        $oldFile = UploadedFile::fake()->image('old_poster.jpg');
        $oldPath = $oldFile->store('promos', 'public');

        $promo = Promo::factory()->create([
            'seller_id'    => $this->sellerProfile->id,
            'category_id'  => $this->category->id,
            'poster_image' => $oldPath,
        ]);

        // Verify old file exists
        Storage::disk('public')->assertExists($oldPath);

        // Upload new image
        $newFile = UploadedFile::fake()->image('new_poster.jpg');

        $this->put(route('seller.promos.update', $promo), $this->validPromoData([
            'poster_image' => $newFile,
        ]));

        // Old file should be deleted
        Storage::disk('public')->assertMissing($oldPath);

        // New file should exist
        $promo->refresh();
        $this->assertNotNull($promo->poster_image);
        Storage::disk('public')->assertExists($promo->poster_image);
    }

    public function test_seller_can_soft_delete_promo(): void
    {
        Storage::fake('public');

        $this->actingAs($this->seller);

        $promo = Promo::factory()->create([
            'seller_id'   => $this->sellerProfile->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->delete(route('seller.promos.destroy', $promo));

        $response->assertRedirect(route('seller.promos.index'));
        $response->assertSessionHas('success');

        // Promo should be soft deleted (not hard deleted)
        $this->assertSoftDeleted('promos', ['id' => $promo->id]);
    }

    public function test_accessing_another_sellers_promo_returns_403(): void
    {
        Storage::fake('public');

        // Create another seller
        $otherSeller = User::factory()->seller()->create();
        $otherSellerProfile = SellerProfile::factory()->create(['user_id' => $otherSeller->id]);

        $promo = Promo::factory()->create([
            'seller_id'   => $otherSellerProfile->id,
            'category_id' => $this->category->id,
        ]);

        // Act as the first seller trying to edit another seller's promo
        $this->actingAs($this->seller);

        $response = $this->get(route('seller.promos.edit', $promo));
        $response->assertStatus(403);

        $response = $this->put(route('seller.promos.update', $promo), $this->validPromoData());
        $response->assertStatus(403);

        $response = $this->delete(route('seller.promos.destroy', $promo));
        $response->assertStatus(403);
    }

    public function test_store_promo_requires_title_and_dates_and_category(): void
    {
        $this->actingAs($this->seller);

        $response = $this->post(route('seller.promos.store'), []);

        $response->assertSessionHasErrors(['title', 'start_date', 'end_date', 'category_id']);
    }

    public function test_end_date_must_be_after_or_equal_to_start_date(): void
    {
        $this->actingAs($this->seller);

        $response = $this->post(route('seller.promos.store'), $this->validPromoData([
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date'   => now()->toDateString(),
        ]));

        $response->assertSessionHasErrors(['end_date']);
    }
}
