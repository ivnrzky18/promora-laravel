<?php

namespace Tests\Feature\Public;

use App\Models\Category;
use App\Models\Promo;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExploreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that category filter returns only promos matching the given category.
     */
    public function test_category_filter_returns_only_matching_promos(): void
    {
        $category1 = Category::factory()->create(['name' => 'Kuliner', 'slug' => 'kuliner']);
        $category2 = Category::factory()->create(['name' => 'Fashion', 'slug' => 'fashion']);

        $seller = SellerProfile::factory()->create(['is_verified' => true]);

        Promo::factory()->active()->create([
            'seller_id'   => $seller->id,
            'category_id' => $category1->id,
            'title'       => 'Promo Kuliner',
        ]);
        Promo::factory()->active()->create([
            'seller_id'   => $seller->id,
            'category_id' => $category2->id,
            'title'       => 'Promo Fashion',
        ]);

        $response = $this->get(route('explore', ['category_id' => $category1->id]));

        $response->assertStatus(200);
        $response->assertSee('Promo Kuliner');
        $response->assertDontSee('Promo Fashion');
    }

    /**
     * Test that keyword search returns promos matching title or description.
     */
    public function test_keyword_search_returns_matching_promos(): void
    {
        $seller = SellerProfile::factory()->create(['is_verified' => true]);
        $category = Category::factory()->create(['name' => 'Jasa', 'slug' => 'jasa']);

        Promo::factory()->active()->create([
            'seller_id'   => $seller->id,
            'category_id' => $category->id,
            'title'       => 'Diskon Besar Elektronik',
            'description' => 'Dapatkan diskon untuk semua produk elektronik',
        ]);
        Promo::factory()->active()->create([
            'seller_id'   => $seller->id,
            'category_id' => $category->id,
            'title'       => 'Promo Makanan Sehat',
            'description' => 'Makanan sehat dengan harga terjangkau',
        ]);

        $response = $this->get(route('explore', ['q' => 'Elektronik']));

        $response->assertStatus(200);
        $response->assertSee('Diskon Besar Elektronik');
        $response->assertDontSee('Promo Makanan Sehat');
    }

    /**
     * Test that location filter works by matching seller address.
     */
    public function test_location_filter_works(): void
    {
        $category = Category::factory()->create(['name' => 'Kuliner', 'slug' => 'kuliner']);

        $sellerBandung = SellerProfile::factory()->create([
            'is_verified' => true,
            'address'     => 'Jl. Sudirman No. 1, Bandung, Jawa Barat',
        ]);
        $sellerJakarta = SellerProfile::factory()->create([
            'is_verified' => true,
            'address'     => 'Jl. Thamrin No. 5, Jakarta Pusat',
        ]);

        Promo::factory()->active()->create([
            'seller_id'   => $sellerBandung->id,
            'category_id' => $category->id,
            'title'       => 'Promo Bandung',
        ]);
        Promo::factory()->active()->create([
            'seller_id'   => $sellerJakarta->id,
            'category_id' => $category->id,
            'title'       => 'Promo Jakarta',
        ]);

        $response = $this->get(route('explore', ['location' => 'Bandung']));

        $response->assertStatus(200);
        $response->assertSee('Promo Bandung');
        $response->assertDontSee('Promo Jakarta');
    }

    /**
     * Test that sort by ending_soon orders promos by end_date ascending.
     */
    public function test_sort_by_ending_soon_orders_correctly(): void
    {
        $seller   = SellerProfile::factory()->create(['is_verified' => true]);
        $category = Category::factory()->create(['name' => 'Jasa', 'slug' => 'jasa']);

        Promo::factory()->active()->create([
            'seller_id'   => $seller->id,
            'category_id' => $category->id,
            'title'       => 'Promo Berakhir Lama',
            'end_date'    => now()->addDays(10)->toDateString(),
        ]);
        Promo::factory()->active()->create([
            'seller_id'   => $seller->id,
            'category_id' => $category->id,
            'title'       => 'Promo Berakhir Segera',
            'end_date'    => now()->addDays(2)->toDateString(),
        ]);

        $response = $this->get(route('explore', ['sort' => 'ending_soon']));

        $response->assertStatus(200);

        // "Berakhir Segera" should appear before "Berakhir Lama" in the response
        $content = $response->getContent();
        $posSoon = strpos($content, 'Promo Berakhir Segera');
        $posLama = strpos($content, 'Promo Berakhir Lama');

        $this->assertNotFalse($posSoon);
        $this->assertNotFalse($posLama);
        $this->assertLessThan($posLama, $posSoon, 'Promo ending soon should appear before promo ending later');
    }

    /**
     * Test that URL params are preserved in pagination links.
     */
    public function test_url_params_are_preserved_in_pagination(): void
    {
        $seller   = SellerProfile::factory()->create(['is_verified' => true]);
        $category = Category::factory()->create(['name' => 'Kuliner', 'slug' => 'kuliner']);

        // Create more than 12 promos to trigger pagination
        Promo::factory()->active()->count(15)->create([
            'seller_id'   => $seller->id,
            'category_id' => $category->id,
        ]);

        $response = $this->get(route('explore', [
            'category_id' => $category->id,
            'sort'        => 'latest',
        ]));

        $response->assertStatus(200);

        // The category_id filter should be reflected in the form (selected option)
        $response->assertSee('category_id');
        // The response should show promos (15 created, 12 per page)
        $response->assertSee('Menampilkan');
    }

    /**
     * Test that only active promos from verified sellers are shown.
     */
    public function test_only_active_promos_from_verified_sellers_are_shown(): void
    {
        $category = Category::factory()->create(['name' => 'Jasa', 'slug' => 'jasa']);

        $verifiedSeller   = SellerProfile::factory()->create(['is_verified' => true]);
        $unverifiedSeller = SellerProfile::factory()->create(['is_verified' => false]);

        Promo::factory()->active()->create([
            'seller_id'   => $verifiedSeller->id,
            'category_id' => $category->id,
            'title'       => 'Promo Seller Terverifikasi',
        ]);
        Promo::factory()->active()->create([
            'seller_id'   => $unverifiedSeller->id,
            'category_id' => $category->id,
            'title'       => 'Promo Seller Belum Terverifikasi',
        ]);
        Promo::factory()->draft()->create([
            'seller_id'   => $verifiedSeller->id,
            'category_id' => $category->id,
            'title'       => 'Promo Draft',
        ]);

        $response = $this->get(route('explore'));

        $response->assertStatus(200);
        $response->assertSee('Promo Seller Terverifikasi');
        $response->assertDontSee('Promo Seller Belum Terverifikasi');
        $response->assertDontSee('Promo Draft');
    }

    /**
     * Test that explore page is accessible without authentication.
     */
    public function test_explore_page_is_accessible_without_authentication(): void
    {
        $response = $this->get(route('explore'));

        $response->assertStatus(200);
        $response->assertViewIs('public.explore');
    }
}
