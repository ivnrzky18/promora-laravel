<?php

namespace Tests\Property;

use App\Models\Category;
use App\Models\Promo;
use App\Models\SellerProfile;
use App\Models\User;
use Eris\Generators;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Property-Based Tests for API JSON Endpoints
 *
 * Validates: Requirements 14.1, 14.3
 */
class ApiPropertyTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    // ─── Required fields for PromoResource ───────────────────────────────────

    private const REQUIRED_FIELDS = [
        'id',
        'title',
        'description',
        'discount_percentage',
        'promo_price',
        'start_date',
        'end_date',
        'poster_image',
        'seller',
        'category',
    ];

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Create a Category with a guaranteed-unique name to avoid Faker exhaustion.
     */
    private function createCategory(string $suffix = ''): Category
    {
        $name = 'KategoriApi' . $suffix . Str::random(8);
        return Category::create([
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(4),
            'icon' => null,
        ]);
    }

    /**
     * Create a verified seller for this iteration.
     */
    private function createVerifiedSeller(string $suffix): SellerProfile
    {
        $sellerUser = User::factory()->seller()->create([
            'email' => "seller_api_{$suffix}_" . Str::random(6) . '@example.com',
        ]);

        return SellerProfile::factory()->create([
            'user_id'     => $sellerUser->id,
            'is_verified' => true,
        ]);
    }

    /**
     * Assert that a single promo item from the JSON response contains all required fields
     * and that nested objects have the expected sub-fields.
     */
    private function assertPromoItemHasRequiredFields(array $item, int $promoId): void
    {
        foreach (self::REQUIRED_FIELDS as $field) {
            $this->assertArrayHasKey(
                $field,
                $item,
                "Promo #{$promoId} in API response is missing required field '{$field}'"
            );
        }

        // seller must be an object/array with a 'name' key
        $this->assertIsArray(
            $item['seller'],
            "Promo #{$promoId}: 'seller' field must be an object/array"
        );
        $this->assertArrayHasKey(
            'name',
            $item['seller'],
            "Promo #{$promoId}: 'seller' object must contain 'name' (business_name)"
        );

        // category must be an object/array with a 'name' key
        $this->assertIsArray(
            $item['category'],
            "Promo #{$promoId}: 'category' field must be an object/array"
        );
        $this->assertArrayHasKey(
            'name',
            $item['category'],
            "Promo #{$promoId}: 'category' object must contain 'name'"
        );
    }

    // ─── Property 22a: GET /api/promos always returns all required fields ─────

    /**
     * Property 22a: GET /api/promos always returns JSON where every item
     * contains all required fields.
     *
     * For any number of active promos (1–5), the GET /api/promos endpoint
     * SHALL return a JSON response where every item in the 'data' array
     * contains: id, title, description, discount_percentage, promo_price,
     * start_date, end_date, poster_image, seller.name, category.name.
     *
     * Validates: Requirements 14.1
     */
    public function testGetPromosAlwaysContainsAllRequiredFields(): void
    {
        $promoCount = Generators::choose(1, 5);
        $iterIndex  = Generators::choose(1, 999999);

        $this->forAll($promoCount, $iterIndex)
            ->withMaxSize(100)
            ->then(function (int $numPromos, int $idx) {
                // ── Setup ─────────────────────────────────────────────────────
                $category = $this->createCategory("P22a_{$idx}_");
                $seller   = $this->createVerifiedSeller("P22a_{$idx}");

                $createdPromos = [];
                for ($i = 0; $i < $numPromos; $i++) {
                    $createdPromos[] = Promo::factory()->active()->create([
                        'seller_id'   => $seller->id,
                        'category_id' => $category->id,
                    ]);
                }

                // ── Execute ───────────────────────────────────────────────────
                $response = $this->getJson('/api/promos');

                // ── Assert HTTP 200 ───────────────────────────────────────────
                $response->assertStatus(200);

                $responseData = $response->json('data');

                $this->assertIsArray(
                    $responseData,
                    "GET /api/promos must return a JSON object with a 'data' array"
                );

                $this->assertNotEmpty(
                    $responseData,
                    "GET /api/promos must return at least one item when active promos exist"
                );

                // ── Property: every item has all required fields ───────────────
                foreach ($responseData as $item) {
                    $this->assertPromoItemHasRequiredFields($item, $item['id'] ?? 0);
                }

                // ── Verify all created promos appear in the response ──────────
                $responseIds = array_column($responseData, 'id');
                foreach ($createdPromos as $promo) {
                    $this->assertContains(
                        $promo->id,
                        $responseIds,
                        "Active promo #{$promo->id} must appear in GET /api/promos response"
                    );
                }

                // ── Cleanup ───────────────────────────────────────────────────
                foreach ($createdPromos as $promo) {
                    $promo->forceDelete();
                }
                $seller->forceDelete();
                $seller->user->delete();
                $category->delete();
            });
    }

    // ─── Property 22b: GET /api/promos/{id} always returns all required fields ─

    /**
     * Property 22b: GET /api/promos/{id} for any valid active promo always
     * returns JSON with all required fields.
     *
     * For any active promo, the GET /api/promos/{id} endpoint SHALL return
     * HTTP 200 with a JSON response containing: id, title, description,
     * discount_percentage, promo_price, start_date, end_date, poster_image,
     * seller.name, category.name.
     *
     * Validates: Requirements 14.3
     */
    public function testGetPromoByIdAlwaysContainsAllRequiredFields(): void
    {
        $iterIndex = Generators::choose(1, 999999);

        $this->forAll($iterIndex)
            ->withMaxSize(100)
            ->then(function (int $idx) {
                // ── Setup ─────────────────────────────────────────────────────
                $category = $this->createCategory("P22b_{$idx}_");
                $seller   = $this->createVerifiedSeller("P22b_{$idx}");

                $promo = Promo::factory()->active()->create([
                    'seller_id'   => $seller->id,
                    'category_id' => $category->id,
                ]);

                // ── Execute ───────────────────────────────────────────────────
                $response = $this->getJson("/api/promos/{$promo->id}");

                // ── Assert HTTP 200 ───────────────────────────────────────────
                $response->assertStatus(200);

                $item = $response->json('data');

                $this->assertIsArray(
                    $item,
                    "GET /api/promos/{$promo->id} must return a JSON object with a 'data' key"
                );

                // ── Property: item has all required fields ────────────────────
                $this->assertPromoItemHasRequiredFields($item, $promo->id);

                // ── Verify the correct promo is returned ──────────────────────
                $this->assertEquals(
                    $promo->id,
                    $item['id'],
                    "GET /api/promos/{$promo->id} must return the promo with id = {$promo->id}"
                );

                // ── Verify seller.name matches the actual business_name ────────
                $this->assertEquals(
                    $seller->business_name,
                    $item['seller']['name'],
                    "seller.name must match the seller's business_name"
                );

                // ── Verify category.name matches the actual category name ──────
                $this->assertEquals(
                    $category->name,
                    $item['category']['name'],
                    "category.name must match the category's name"
                );

                // ── Cleanup ───────────────────────────────────────────────────
                $promo->forceDelete();
                $seller->forceDelete();
                $seller->user->delete();
                $category->delete();
            });
    }
}
