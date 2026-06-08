<?php

namespace Tests\Feature\Seller;

use App\Models\Event;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;
    private SellerProfile $sellerProfile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->seller()->create();
        $this->sellerProfile = SellerProfile::factory()->create(['user_id' => $this->seller->id]);
    }

    private function validEventData(array $overrides = []): array
    {
        return array_merge([
            'title'      => 'Test Event',
            'description' => 'Test event description',
            'location'   => 'Jakarta',
            'event_date' => now()->addDays(3)->format('Y-m-d\TH:i'),
            'end_date'   => now()->addDays(4)->format('Y-m-d\TH:i'),
        ], $overrides);
    }

    public function test_seller_can_store_event(): void
    {
        Storage::fake('public');

        $this->actingAs($this->seller);

        $response = $this->post(route('seller.events.store'), $this->validEventData());

        $response->assertRedirect(route('seller.events.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('events', [
            'title'     => 'Test Event',
            'seller_id' => $this->sellerProfile->id,
        ]);
    }

    public function test_seller_can_store_event_with_poster_image(): void
    {
        Storage::fake('public');

        $this->actingAs($this->seller);

        $file = UploadedFile::fake()->image('event_poster.jpg', 800, 600);

        $response = $this->post(route('seller.events.store'), $this->validEventData([
            'poster_image' => $file,
        ]));

        $response->assertRedirect(route('seller.events.index'));

        $event = Event::where('title', 'Test Event')->first();
        $this->assertNotNull($event->poster_image);
        Storage::disk('public')->assertExists($event->poster_image);
    }

    public function test_seller_can_update_event(): void
    {
        Storage::fake('public');

        $this->actingAs($this->seller);

        $event = Event::factory()->create([
            'seller_id' => $this->sellerProfile->id,
        ]);

        $response = $this->put(route('seller.events.update', $event), $this->validEventData([
            'title' => 'Updated Event Title',
        ]));

        $response->assertRedirect(route('seller.events.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('events', [
            'id'    => $event->id,
            'title' => 'Updated Event Title',
        ]);
    }

    public function test_update_event_replaces_old_poster_image(): void
    {
        Storage::fake('public');

        $this->actingAs($this->seller);

        // Create event with existing poster
        $oldFile = UploadedFile::fake()->image('old_event.jpg');
        $oldPath = $oldFile->store('events', 'public');

        $event = Event::factory()->create([
            'seller_id'    => $this->sellerProfile->id,
            'poster_image' => $oldPath,
        ]);

        Storage::disk('public')->assertExists($oldPath);

        // Update with new poster
        $newFile = UploadedFile::fake()->image('new_event.jpg');

        $this->put(route('seller.events.update', $event), $this->validEventData([
            'poster_image' => $newFile,
        ]));

        // Old file should be deleted
        Storage::disk('public')->assertMissing($oldPath);

        // New file should exist
        $event->refresh();
        Storage::disk('public')->assertExists($event->poster_image);
    }

    public function test_seller_can_delete_event(): void
    {
        Storage::fake('public');

        $this->actingAs($this->seller);

        $event = Event::factory()->create([
            'seller_id' => $this->sellerProfile->id,
        ]);

        $response = $this->delete(route('seller.events.destroy', $event));

        $response->assertRedirect(route('seller.events.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function test_accessing_another_sellers_event_returns_403(): void
    {
        Storage::fake('public');

        // Create another seller
        $otherSeller = User::factory()->seller()->create();
        $otherSellerProfile = SellerProfile::factory()->create(['user_id' => $otherSeller->id]);

        $event = Event::factory()->create([
            'seller_id' => $otherSellerProfile->id,
        ]);

        $this->actingAs($this->seller);

        $response = $this->get(route('seller.events.edit', $event));
        $response->assertStatus(403);

        $response = $this->put(route('seller.events.update', $event), $this->validEventData());
        $response->assertStatus(403);

        $response = $this->delete(route('seller.events.destroy', $event));
        $response->assertStatus(403);
    }

    public function test_store_event_requires_title_and_event_date(): void
    {
        $this->actingAs($this->seller);

        $response = $this->post(route('seller.events.store'), []);

        $response->assertSessionHasErrors(['title', 'event_date']);
    }
}
