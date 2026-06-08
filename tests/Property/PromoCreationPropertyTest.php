<?php

namespace Tests\Property;

use App\Models\Category;
use App\Models\Promo;
use App\Models\SellerProfile;
use App\Models\User;
use Eris\Generators;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Property-Based Tests for Promo Creation
 *
 * Validates: Requirements 3.3
 */
class PromoCreationPropertyTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    // ─── Property 8: Promo baru selalu dibuat dengan status = draft ───────────

    /**
     * Property 8: For any valid promo creation data submitted by a seller,
     * the newly created promo always has status=draft regardless of what
     * the seller submits.
     *
     * Validates: Requirements 3.3
     */
    public function testNewPromoAlwaysHasStatusDraft(): void
    {
        $titleIndex = Generators::choose(1, 999999);

        $titles = Generators::elements([
            'Diskon Besar Akhir Tahun',
            'Promo Spesial Lebaran',
            'Flash Sale Elektronik',
            'Cuci Gudang Fashion',
            'Promo Kuliner Spesial',
            'Diskon Jasa Kesehatan',
            'Penawaran Terbatas Pendidikan',
            'Promo Hiburan Keluarga',
            'Sale Akhir Bulan',
            'Promo Kemerdekaan',
        ]);

        $discountPercentages = Generators::choose(1, 90);

        $originalPrices = Generators::choose(10000, 1000000);

        $this->forAll($titleIndex, $titles, $discountPercentages, $originalPrices)
            ->withMaxSize(100)
            ->then(function (int $idx, string $title, int $discount, int $originalPrice) {
                // Create a seller with a verified SellerProfile
                $seller = User::factory()->seller()->create([
                    'email' => "seller_prop8_{$idx}@example.com",
                ]);
                $sellerProfile = SellerProfile::factory()->create([
                    'user_id' => $seller->id,
                ]);

                // Create a category for the promo
                $category = Category::factory()->create();

                $promoPrice = (int) round($originalPrice * (1 - $discount / 100));

                // Build valid promo creation payload
                $payload = [
                    'title'               => "{$title} #{$idx}",
                    'description'         => 'Deskripsi promo yang menarik untuk pelanggan setia kami.',
                    'discount_percentage' => $discount,
                    'original_price'      => $originalPrice,
                    'promo_price'         => $promoPrice,
                    'start_date'          => now()->addDay()->toDateString(),
                    'end_date'            => now()->addDays(7)->toDateString(),
                    'category_id'         => $category->id,
                ];

                // Submit the promo creation form as the seller
                $response = $this->actingAs($seller)
                                 ->post(route('seller.promos.store'), $payload);

                // Should redirect (successful creation)
                $response->assertRedirect();

                // The created promo must have status=draft
                $promo = Promo::where('seller_id', $sellerProfile->id)
                              ->where('title', "{$title} #{$idx}")
                              ->first();

                $this->assertNotNull(
                    $promo,
                    "Promo with title '{$title} #{$idx}' should exist in database after creation"
                );

                $this->assertEquals(
                    'draft',
                    $promo->status,
                    "Newly created promo should always have status='draft', but got '{$promo->status}'"
                );

                // Clean up for next iteration
                $promo->forceDelete();
                $category->delete();
                $sellerProfile->forceDelete();
                $seller->delete();
                $this->app['auth']->logout();
            });
    }

    /**
     * Property 8b: Even if a seller attempts to submit a status field in the payload,
     * the created promo always has status=draft (controller enforces draft status).
     *
     * Validates: Requirements 3.3
     */
    public function testNewPromoAlwaysHasStatusDraftEvenWhenStatusIsSubmitted(): void
    {
        $titleIndex = Generators::choose(1, 999999);

        $attemptedStatuses = Generators::elements([
            'active',
            'expired',
            'draft',
            'published',
            'approved',
        ]);

        $this->forAll($titleIndex, $attemptedStatuses)
            ->withMaxSize(100)
            ->then(function (int $idx, string $attemptedStatus) {
                // Create a seller with a verified SellerProfile
                $seller = User::factory()->seller()->create([
                    'email' => "seller_prop8b_{$idx}@example.com",
                ]);
                $sellerProfile = SellerProfile::factory()->create([
                    'user_id' => $seller->id,
                ]);

                // Create a category for the promo
                $category = Category::factory()->create();

                // Build payload that includes an attempted status override
                $payload = [
                    'title'               => "Promo Status Test #{$idx}",
                    'description'         => 'Mencoba mengubah status saat pembuatan promo.',
                    'discount_percentage' => 20,
                    'original_price'      => 100000,
                    'promo_price'         => 80000,
                    'start_date'          => now()->addDay()->toDateString(),
                    'end_date'            => now()->addDays(7)->toDateString(),
                    'category_id'         => $category->id,
                    'status'              => $attemptedStatus, // Seller tries to set status
                ];

                // Submit the promo creation form as the seller
                $response = $this->actingAs($seller)
                                 ->post(route('seller.promos.store'), $payload);

                // Should redirect (successful creation)
                $response->assertRedirect();

                // The created promo must ALWAYS have status=draft regardless of submitted status
                $promo = Promo::where('seller_id', $sellerProfile->id)
                              ->where('title', "Promo Status Test #{$idx}")
                              ->first();

                $this->assertNotNull(
                    $promo,
                    "Promo 'Promo Status Test #{$idx}' should exist in database after creation"
                );

                $this->assertEquals(
                    'draft',
                    $promo->status,
                    "Newly created promo should always have status='draft' even when seller submits status='{$attemptedStatus}', but got '{$promo->status}'"
                );

                // Clean up for next iteration
                $promo->forceDelete();
                $category->delete();
                $sellerProfile->forceDelete();
                $seller->delete();
                $this->app['auth']->logout();
            });
    }
}
