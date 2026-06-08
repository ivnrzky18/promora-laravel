<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsumerRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful consumer registration creates user with role=consumer.
     */
    public function test_successful_registration_creates_user_with_consumer_role(): void
    {
        $response = $this->post(route('consumer.register'), [
            'name'                  => 'Budi Santoso',
            'email'                 => 'budi@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'location'              => 'Bandung, Jawa Barat',
        ]);

        $response->assertRedirect(route('consumer.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'budi@example.com',
            'role'  => 'consumer',
            'name'  => 'Budi Santoso',
        ]);
    }

    /**
     * Test that duplicate email returns a validation error.
     */
    public function test_duplicate_email_returns_validation_error(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post(route('consumer.register'), [
            'name'                  => 'Siti Rahayu',
            'email'                 => 'existing@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'location'              => 'Jakarta',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 1);
    }

    /**
     * Test that after registration user is redirected to consumer dashboard.
     */
    public function test_redirect_to_consumer_dashboard_after_registration(): void
    {
        $response = $this->post(route('consumer.register'), [
            'name'                  => 'Andi Wijaya',
            'email'                 => 'andi@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'location'              => 'Surabaya',
        ]);

        $response->assertRedirect(route('consumer.dashboard'));
    }

    /**
     * Test that user is authenticated after registration.
     */
    public function test_user_is_authenticated_after_registration(): void
    {
        $this->post(route('consumer.register'), [
            'name'                  => 'Dewi Lestari',
            'email'                 => 'dewi@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'location'              => 'Yogyakarta',
        ]);

        $this->assertAuthenticated();
    }

    /**
     * Test that password shorter than 8 characters fails validation.
     */
    public function test_short_password_fails_validation(): void
    {
        $response = $this->post(route('consumer.register'), [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'short',
            'password_confirmation' => 'short',
            'location'              => 'Jakarta',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test that mismatched password confirmation fails validation.
     */
    public function test_mismatched_password_confirmation_fails_validation(): void
    {
        $response = $this->post(route('consumer.register'), [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'different123',
            'location'              => 'Jakarta',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test that missing required fields fail validation.
     */
    public function test_missing_required_fields_fail_validation(): void
    {
        $response = $this->post(route('consumer.register'), []);

        $response->assertSessionHasErrors(['name', 'email', 'password', 'location']);
    }

    /**
     * Test that the registration form page is accessible.
     */
    public function test_registration_form_is_accessible(): void
    {
        $response = $this->get(route('consumer.register'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.consumer.register');
    }
}
