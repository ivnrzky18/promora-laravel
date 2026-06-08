<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    /**
     * Test that an admin can create a category.
     */
    public function test_admin_can_create_category(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.categories.store'), [
            'name' => 'Kuliner',
            'slug' => 'kuliner',
            'icon' => '🍜',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'name' => 'Kuliner',
            'slug' => 'kuliner',
            'icon' => '🍜',
        ]);
    }

    /**
     * Test that an admin can update a category.
     */
    public function test_admin_can_update_category(): void
    {
        $category = Category::factory()->create([
            'name' => 'Kuliner',
            'slug' => 'kuliner',
        ]);

        $this->actingAs($this->admin);

        $response = $this->put(route('admin.categories.update', $category), [
            'name' => 'Kuliner & Minuman',
            'slug' => 'kuliner-minuman',
            'icon' => '🍜',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'id'   => $category->id,
            'name' => 'Kuliner & Minuman',
            'slug' => 'kuliner-minuman',
        ]);
    }

    /**
     * Test that an admin can delete a category.
     */
    public function test_admin_can_delete_category(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->admin);

        $response = $this->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /**
     * Test that creating a category with a duplicate name fails validation.
     */
    public function test_duplicate_category_name_fails_validation(): void
    {
        Category::factory()->create([
            'name' => 'Kuliner',
            'slug' => 'kuliner',
        ]);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.categories.store'), [
            'name' => 'Kuliner',
            'slug' => 'kuliner-baru',
            'icon' => null,
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('categories', 1);
    }

    /**
     * Test that creating a category with a duplicate slug fails validation.
     */
    public function test_duplicate_category_slug_fails_validation(): void
    {
        Category::factory()->create([
            'name' => 'Kuliner',
            'slug' => 'kuliner',
        ]);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.categories.store'), [
            'name' => 'Kuliner Baru',
            'slug' => 'kuliner',
            'icon' => null,
        ]);

        $response->assertSessionHasErrors('slug');
        $this->assertDatabaseCount('categories', 1);
    }

    /**
     * Test that updating a category with its own name does not fail validation.
     */
    public function test_update_category_with_same_name_passes_validation(): void
    {
        $category = Category::factory()->create([
            'name' => 'Kuliner',
            'slug' => 'kuliner',
        ]);

        $this->actingAs($this->admin);

        $response = $this->put(route('admin.categories.update', $category), [
            'name' => 'Kuliner',
            'slug' => 'kuliner',
            'icon' => '🍜',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');
    }

    /**
     * Test that the categories index page is accessible to admin.
     */
    public function test_admin_can_view_categories_index(): void
    {
        Category::factory()->count(3)->create();

        $this->actingAs($this->admin);

        $response = $this->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.index');
        $response->assertViewHas('categories');
    }

    /**
     * Test that a non-admin cannot create categories.
     */
    public function test_non_admin_cannot_create_category(): void
    {
        $consumer = User::factory()->consumer()->create();

        $this->actingAs($consumer);

        $response = $this->post(route('admin.categories.store'), [
            'name' => 'Kuliner',
            'slug' => 'kuliner',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('categories', ['name' => 'Kuliner']);
    }
}
