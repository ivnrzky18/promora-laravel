<?php

namespace Tests\Feature\Api;

use App\Models\Promo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PromoApiTest extends TestCase
{
    use RefreshDatabase;

    // ─── GET /api/promos ──────────────────────────────────────────────────────

    #[Test]
    public function it_returns_200_with_all_required_fields(): void
    {
        Promo::factory()->active()->create();

        $response = $this->getJson('/api/promos');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'discount_percentage',
                        'promo_price',
                        'start_date',
                        'end_date',
                        'poster_image',
                        'seller' => ['name'],
                        'category' => ['name'],
                    ],
                ],
            ]);
    }

    #[Test]
    public function it_returns_only_active_promos_in_index(): void
    {
        $active = Promo::factory()->active()->create();
        $draft = Promo::factory()->draft()->create();
        $expired = Promo::factory()->expired()->create();

        $response = $this->getJson('/api/promos');

        $response->assertStatus(200);

        $ids = collect($response->json('data'))->pluck('id');

        $this->assertTrue($ids->contains($active->id), 'Active promo should appear');
        $this->assertFalse($ids->contains($draft->id), 'Draft promo should NOT appear');
        $this->assertFalse($ids->contains($expired->id), 'Expired promo should NOT appear');
    }

    // ─── GET /api/promos/{id} ─────────────────────────────────────────────────

    #[Test]
    public function it_returns_200_with_correct_data_for_valid_active_promo(): void
    {
        $promo = Promo::factory()->active()->create();

        $response = $this->getJson("/api/promos/{$promo->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $promo->id)
            ->assertJsonPath('data.title', $promo->title)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'discount_percentage',
                    'promo_price',
                    'start_date',
                    'end_date',
                    'poster_image',
                    'seller' => ['name'],
                    'category' => ['name'],
                ],
            ]);
    }

    #[Test]
    public function it_returns_404_with_message_for_nonexistent_promo(): void
    {
        $response = $this->getJson('/api/promos/999999');

        $response->assertStatus(404)
            ->assertJsonPath('message', 'Promo tidak ditemukan');
    }

    #[Test]
    public function draft_and_expired_promos_do_not_appear_in_index(): void
    {
        Promo::factory()->draft()->create();
        Promo::factory()->expired()->create();

        $response = $this->getJson('/api/promos');

        $response->assertStatus(200)
            ->assertJsonPath('data', []);
    }
}
