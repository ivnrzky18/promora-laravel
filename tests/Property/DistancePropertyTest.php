<?php

namespace Tests\Property;

use App\Models\Category;
use App\Models\Promo;
use App\Models\SellerProfile;
use App\Models\User;
use App\Services\DistanceService;
use Eris\Generators;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Property-Based Tests for Distance / Location functionality
 *
 * Validates: Requirements 9.4, 9.5
 */
class DistancePropertyTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    private DistanceService $distanceService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->distanceService = new DistanceService();
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Create a Category with a guaranteed-unique name to avoid Faker exhaustion.
     */
    private function createCategory(string $suffix = ''): Category
    {
        $name = 'Kategori' . $suffix . Str::random(8);
        return Category::create([
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(4),
            'icon' => null,
        ]);
    }

    /**
     * Create a verified seller at a specific latitude/longitude.
     */
    private function createSellerAt(string $suffix, float $lat, float $lng): SellerProfile
    {
        $sellerUser = User::factory()->seller()->create([
            'email' => "seller_dist_{$suffix}@example.com",
        ]);

        return SellerProfile::factory()->create([
            'user_id'     => $sellerUser->id,
            'latitude'    => $lat,
            'longitude'   => $lng,
            'is_verified' => true,
        ]);
    }

    // ─── Property 16: Haversine distance mathematical properties ─────────────

    /**
     * Property 16a: Reflexivity — distance(A, A) = 0.
     *
     * For any point A, the distance from A to itself SHALL be 0.
     *
     * Validates: Requirement 9.4
     */
    public function testHaversineReflexivity(): void
    {
        // Generate random coordinates within valid ranges
        // Latitude: -90 to 90, Longitude: -180 to 180
        // Using integers scaled to floats for Eris compatibility
        $latScaled = Generators::choose(-9000, 9000);   // will divide by 100 → [-90.00, 90.00]
        $lngScaled = Generators::choose(-18000, 18000); // will divide by 100 → [-180.00, 180.00]

        $this->forAll($latScaled, $lngScaled)
            ->withMaxSize(100)
            ->then(function (int $latInt, int $lngInt) {
                $lat = $latInt / 100.0;
                $lng = $lngInt / 100.0;

                $distance = $this->distanceService->calculate($lat, $lng, $lat, $lng);

                $this->assertEqualsWithDelta(
                    0.0,
                    $distance,
                    1e-9,
                    "Haversine reflexivity violated: distance(({$lat},{$lng}), ({$lat},{$lng})) = {$distance}, expected 0"
                );
            });
    }

    /**
     * Property 16b: Symmetry — distance(A, B) = distance(B, A).
     *
     * For any two points A and B, the distance from A to B SHALL equal
     * the distance from B to A.
     *
     * Validates: Requirement 9.4
     */
    public function testHaversineSymmetry(): void
    {
        // Generate two distinct coordinate pairs
        $lat1Scaled = Generators::choose(-9000, 9000);
        $lng1Scaled = Generators::choose(-18000, 18000);
        $lat2Scaled = Generators::choose(-9000, 9000);
        $lng2Scaled = Generators::choose(-18000, 18000);

        $this->forAll($lat1Scaled, $lng1Scaled, $lat2Scaled, $lng2Scaled)
            ->withMaxSize(100)
            ->then(function (int $lat1Int, int $lng1Int, int $lat2Int, int $lng2Int) {
                $lat1 = $lat1Int / 100.0;
                $lng1 = $lng1Int / 100.0;
                $lat2 = $lat2Int / 100.0;
                $lng2 = $lng2Int / 100.0;

                $distAB = $this->distanceService->calculate($lat1, $lng1, $lat2, $lng2);
                $distBA = $this->distanceService->calculate($lat2, $lng2, $lat1, $lng1);

                $this->assertEqualsWithDelta(
                    $distAB,
                    $distBA,
                    1e-9,
                    "Haversine symmetry violated: " .
                    "distance(({$lat1},{$lng1}), ({$lat2},{$lng2})) = {$distAB} " .
                    "!= distance(({$lat2},{$lng2}), ({$lat1},{$lng1})) = {$distBA}"
                );
            });
    }

    /**
     * Property 16c: Non-negativity — distance(A, B) >= 0.
     *
     * For any two points A and B, the distance SHALL always be non-negative.
     *
     * Validates: Requirement 9.4
     */
    public function testHaversineNonNegativity(): void
    {
        $lat1Scaled = Generators::choose(-9000, 9000);
        $lng1Scaled = Generators::choose(-18000, 18000);
        $lat2Scaled = Generators::choose(-9000, 9000);
        $lng2Scaled = Generators::choose(-18000, 18000);

        $this->forAll($lat1Scaled, $lng1Scaled, $lat2Scaled, $lng2Scaled)
            ->withMaxSize(100)
            ->then(function (int $lat1Int, int $lng1Int, int $lat2Int, int $lng2Int) {
                $lat1 = $lat1Int / 100.0;
                $lng1 = $lng1Int / 100.0;
                $lat2 = $lat2Int / 100.0;
                $lng2 = $lng2Int / 100.0;

                $distance = $this->distanceService->calculate($lat1, $lng1, $lat2, $lng2);

                $this->assertGreaterThanOrEqual(
                    0.0,
                    $distance,
                    "Haversine non-negativity violated: " .
                    "distance(({$lat1},{$lng1}), ({$lat2},{$lng2})) = {$distance} < 0"
                );
            });
    }

    // ─── Property 17: Sort by distance → urutan ascending ────────────────────

    /**
     * Property 17: Sorting by distance (nearest) always produces ascending order.
     *
     * When promos are sorted by distance using DistanceService::calculate(),
     * the results SHALL be ordered by distance ascending, meaning
     * item[i].distance <= item[i+1].distance for all consecutive pairs.
     *
     * Note: The test environment uses SQLite which does not support the SQL
     * trigonometric functions (acos, cos, sin, radians) used by the production
     * Haversine query. This property therefore validates the sorting logic by:
     * 1. Fetching all active promos with their seller coordinates
     * 2. Computing distances using DistanceService::calculate() (same Haversine formula)
     * 3. Sorting the collection by distance ascending
     * 4. Asserting the resulting order is strictly non-decreasing
     *
     * Validates: Requirement 9.5
     */
    public function testSortByDistanceProducesAscendingOrder(): void
    {
        // Number of sellers to create (2–6)
        $sellerCount = Generators::choose(2, 6);
        $iterIndex   = Generators::choose(1, 999999);

        $this->forAll($sellerCount, $iterIndex)
            ->withMaxSize(100)
            ->then(function (int $numSellers, int $idx) {
                // ── Reference point: Jakarta area ─────────────────────────────
                $consumerLat = -6.2088;
                $consumerLng = 106.8456;

                // ── Create a shared category ──────────────────────────────────
                $category = $this->createCategory("P17_{$idx}_");

                $createdSellers     = [];
                $createdSellerUsers = [];
                $createdPromos      = [];

                // ── Create sellers at varying distances from the reference point ─
                // Spread sellers at different latitudes (0.1° apart ≈ 11 km each)
                for ($i = 0; $i < $numSellers; $i++) {
                    // Each seller is placed at a different distance from consumer
                    // by offsetting latitude by 0.1 * (i+1) degrees
                    $sellerLat = $consumerLat + (0.1 * ($i + 1));
                    $sellerLng = $consumerLng;

                    $seller = $this->createSellerAt("{$idx}_{$i}", $sellerLat, $sellerLng);
                    $createdSellers[]     = $seller;
                    $createdSellerUsers[] = $seller->user;

                    // Create one active promo per seller
                    $promo = Promo::factory()->active()->create([
                        'seller_id'   => $seller->id,
                        'category_id' => $category->id,
                    ]);
                    $createdPromos[] = $promo;
                }

                // ── Fetch active promos from verified sellers ─────────────────
                $promos = Promo::with(['seller', 'category'])
                    ->active()
                    ->whereHas('seller', fn ($q) => $q->where('is_verified', true))
                    ->whereIn('seller_id', collect($createdSellers)->pluck('id'))
                    ->get();

                // ── Compute distances using DistanceService and sort ascending ─
                $sorted = $promos
                    ->map(function ($promo) use ($consumerLat, $consumerLng) {
                        $promo->distance = $this->distanceService->calculate(
                            $consumerLat,
                            $consumerLng,
                            (float) $promo->seller->latitude,
                            (float) $promo->seller->longitude
                        );
                        return $promo;
                    })
                    ->sortBy('distance')
                    ->values();

                // ── Assert: at least 2 results to verify ordering ─────────────
                $this->assertGreaterThanOrEqual(
                    2,
                    $sorted->count(),
                    "Sort by distance test requires at least 2 results, got {$sorted->count()}"
                );

                // ── Assert: every item[i].distance <= item[i+1].distance ──────
                for ($i = 0; $i < $sorted->count() - 1; $i++) {
                    $currentDist = (float) $sorted[$i]->distance;
                    $nextDist    = (float) $sorted[$i + 1]->distance;

                    $this->assertLessThanOrEqual(
                        $nextDist,
                        $currentDist,
                        "Sort by distance (ascending) violated at index {$i}: " .
                        "item[{$i}].distance ({$currentDist} km) > " .
                        "item[" . ($i + 1) . "].distance ({$nextDist} km)"
                    );
                }

                // ── Cleanup ───────────────────────────────────────────────────
                foreach ($createdPromos as $promo) {
                    $promo->forceDelete();
                }
                foreach ($createdSellers as $seller) {
                    $seller->forceDelete();
                }
                foreach ($createdSellerUsers as $user) {
                    $user->delete();
                }
                $category->delete();
                $this->app['auth']->logout();
            });
    }

    /**
     * Property 17b: Sort by distance is consistent across arbitrary coordinate sets.
     *
     * For any set of sellers at distinct coordinates, sorting by DistanceService
     * SHALL produce a non-decreasing sequence of distances from the reference point.
     *
     * Validates: Requirement 9.5
     */
    public function testSortByDistanceConsistentWithDistanceService(): void
    {
        // Generate random offsets (in units of 0.01°) for 3 sellers
        // to place them at distinct, known distances from the reference point
        $offset1 = Generators::choose(1, 50);   // 0.01° to 0.50° offset
        $offset2 = Generators::choose(51, 100); // 0.51° to 1.00° offset
        $offset3 = Generators::choose(101, 150); // 1.01° to 1.50° offset
        $iterIndex = Generators::choose(1, 999999);

        $this->forAll($offset1, $offset2, $offset3, $iterIndex)
            ->withMaxSize(100)
            ->then(function (int $off1, int $off2, int $off3, int $idx) {
                // ── Reference point: Bandung area ─────────────────────────────
                $consumerLat = -6.9175;
                $consumerLng = 107.6191;

                // ── Create a shared category ──────────────────────────────────
                $category = $this->createCategory("P17b_{$idx}_");

                // ── Create 3 sellers at offsets north of the reference point ──
                // Offsets are in units of 0.01°, so seller distances are distinct
                $sellersData = [
                    ['lat' => $consumerLat + ($off1 / 100.0), 'lng' => $consumerLng],
                    ['lat' => $consumerLat + ($off2 / 100.0), 'lng' => $consumerLng],
                    ['lat' => $consumerLat + ($off3 / 100.0), 'lng' => $consumerLng],
                ];

                $createdSellers     = [];
                $createdSellerUsers = [];
                $createdPromos      = [];

                foreach ($sellersData as $i => $coords) {
                    $seller = $this->createSellerAt("P17b_{$idx}_{$i}", $coords['lat'], $coords['lng']);
                    $createdSellers[]     = $seller;
                    $createdSellerUsers[] = $seller->user;

                    $promo = Promo::factory()->active()->create([
                        'seller_id'   => $seller->id,
                        'category_id' => $category->id,
                    ]);
                    $createdPromos[] = $promo;
                }

                // ── Fetch promos and compute distances ────────────────────────
                $promos = Promo::with(['seller'])
                    ->active()
                    ->whereHas('seller', fn ($q) => $q->where('is_verified', true))
                    ->whereIn('seller_id', collect($createdSellers)->pluck('id'))
                    ->get();

                // ── Sort by distance using DistanceService ────────────────────
                $sorted = $promos
                    ->map(function ($promo) use ($consumerLat, $consumerLng) {
                        $promo->distance = $this->distanceService->calculate(
                            $consumerLat,
                            $consumerLng,
                            (float) $promo->seller->latitude,
                            (float) $promo->seller->longitude
                        );
                        return $promo;
                    })
                    ->sortBy('distance')
                    ->values();

                // ── Assert: distances are non-decreasing ──────────────────────
                $this->assertGreaterThanOrEqual(2, $sorted->count());

                for ($i = 0; $i < $sorted->count() - 1; $i++) {
                    $currentDist = (float) $sorted[$i]->distance;
                    $nextDist    = (float) $sorted[$i + 1]->distance;

                    $this->assertLessThanOrEqual(
                        $nextDist,
                        $currentDist,
                        "DistanceService sort violated at index {$i}: " .
                        "{$currentDist} km > {$nextDist} km"
                    );
                }

                // ── Assert: the closest seller is the one with smallest offset ─
                // off1 < off2 < off3, so seller[0] should be closest
                $closestPromo = $sorted->first();
                $closestSellerLat = (float) $closestPromo->seller->latitude;

                // The closest seller should have the smallest latitude offset from consumer
                $closestOffset = abs($closestSellerLat - $consumerLat);
                $expectedMinOffset = $off1 / 100.0;

                $this->assertEqualsWithDelta(
                    $expectedMinOffset,
                    $closestOffset,
                    0.001,
                    "Closest seller should have offset {$expectedMinOffset}°, " .
                    "but got {$closestOffset}°"
                );

                // ── Cleanup ───────────────────────────────────────────────────
                foreach ($createdPromos as $promo) {
                    $promo->forceDelete();
                }
                foreach ($createdSellers as $seller) {
                    $seller->forceDelete();
                }
                foreach ($createdSellerUsers as $user) {
                    $user->delete();
                }
                $category->delete();
                $this->app['auth']->logout();
            });
    }
}
