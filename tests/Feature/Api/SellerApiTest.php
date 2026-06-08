<?php

namespace Tests\Feature\Api;

use App\Models\SellerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SellerApiTest extends TestCase
{
    use RefreshDatabase;

    // ─── GET /api/sellers ─────────────────────────────────────────────────────

    #[Test]
    public function it_returns_200_with_json_array(): void
    {
        SellerProfile::factory()->count(2)->create();

        $response = $this->getJson('/api/sellers');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    #[Test]
    public function it_returns_only_verified_sellers(): void
    {
        $verified = SellerProfile::factory()->create(['is_verified' => true]);
        $unverified = SellerProfile::factory()->unverified()->create();

        $response = $this->getJson('/api/sellers');

        $response->assertStatus(200);

        $ids = collect($response->json('data'))->pluck('id');

        $this->assertTrue($ids->contains($verified->id), 'Verified seller should appear');
        $this->assertFalse($ids->contains($unverified->id), 'Unverified seller should NOT appear');
    }

    #[Test]
    public function unverified_sellers_do_not_appear_in_response(): void
    {
        SellerProfile::factory()->unverified()->count(3)->create();

        $response = $this->getJson('/api/sellers');

        $response->assertStatus(200)
            ->assertJsonPath('data', []);
    }

    #[Test]
    public function response_contains_all_required_fields(): void
    {
        SellerProfile::factory()->create(['is_verified' => true]);

        $response = $this->getJson('/api/sellers');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'business_name',
                        'business_category',
                        'description',
                        'address',
                        'logo',
                        'average_rating',
                    ],
                ],
            ]);
    }
}
