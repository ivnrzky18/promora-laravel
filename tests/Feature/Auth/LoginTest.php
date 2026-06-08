<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test consumer login redirects to consumer dashboard.
     */
    public function test_consumer_login_redirects_to_consumer_dashboard(): void
    {
        $user = User::factory()->create([
            'email'    => 'consumer@example.com',
            'password' => bcrypt('password123'),
            'role'     => 'consumer',
        ]);

        $response = $this->post(route('consumer.login'), [
            'email'    => 'consumer@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('consumer.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test seller login redirects to seller dashboard.
     */
    public function test_seller_login_redirects_to_seller_dashboard(): void
    {
        $user = User::factory()->create([
            'email'    => 'seller@example.com',
            'password' => bcrypt('password123'),
            'role'     => 'seller',
        ]);

        $response = $this->post(route('seller.login'), [
            'email'    => 'seller@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('seller.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test admin login redirects to admin dashboard.
     */
    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'email'    => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        $response = $this->post(route('admin.login'), [
            'email'    => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test invalid credentials return error "Email atau kata sandi salah".
     */
    public function test_invalid_credentials_return_error_message(): void
    {
        User::factory()->create([
            'email'    => 'consumer@example.com',
            'password' => bcrypt('correctpassword'),
            'role'     => 'consumer',
        ]);

        $response = $this->post(route('consumer.login'), [
            'email'    => 'consumer@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertStringContainsString(
            'Email atau kata sandi salah',
            $response->getSession()->get('errors')->first('email')
        );
        $this->assertGuest();
    }

    /**
     * Test invalid seller credentials return error.
     */
    public function test_invalid_seller_credentials_return_error_message(): void
    {
        User::factory()->create([
            'email'    => 'seller@example.com',
            'password' => bcrypt('correctpassword'),
            'role'     => 'seller',
        ]);

        $response = $this->post(route('seller.login'), [
            'email'    => 'seller@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Test invalid admin credentials return error.
     */
    public function test_invalid_admin_credentials_return_error_message(): void
    {
        User::factory()->create([
            'email'    => 'admin@example.com',
            'password' => bcrypt('correctpassword'),
            'role'     => 'admin',
        ]);

        $response = $this->post(route('admin.login'), [
            'email'    => 'admin@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Test consumer logout redirects to homepage (public).
     */
    public function test_consumer_logout_redirects_to_homepage(): void
    {
        $user = User::factory()->create(['role' => 'consumer']);

        $response = $this->actingAs($user)->post(route('consumer.logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /**
     * Test seller logout redirects to homepage (public).
     */
    public function test_seller_logout_redirects_to_homepage(): void
    {
        $user = User::factory()->create(['role' => 'seller']);

        $response = $this->actingAs($user)->post(route('seller.logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /**
     * Test consumer login form is accessible.
     */
    public function test_consumer_login_form_is_accessible(): void
    {
        $response = $this->get(route('consumer.login'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.consumer.login');
    }

    /**
     * Test seller login form is accessible.
     */
    public function test_seller_login_form_is_accessible(): void
    {
        $response = $this->get(route('seller.login'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.seller.login');
    }

    /**
     * Test admin login form is accessible.
     */
    public function test_admin_login_form_is_accessible(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.admin.login');
    }

    /**
     * Test that consumer middleware blocks non-consumer users.
     */
    public function test_consumer_middleware_blocks_non_consumer(): void
    {
        $seller = User::factory()->create(['role' => 'seller']);

        $response = $this->actingAs($seller)->get(route('consumer.dashboard'));

        $response->assertRedirect(route('consumer.login'));
    }

    /**
     * Test that seller middleware blocks non-seller users.
     */
    public function test_seller_middleware_blocks_non_seller(): void
    {
        $consumer = User::factory()->create(['role' => 'consumer']);

        $response = $this->actingAs($consumer)->get(route('seller.dashboard'));

        $response->assertRedirect(route('seller.login'));
    }

    /**
     * Test that admin middleware blocks non-admin users.
     */
    public function test_admin_middleware_blocks_non_admin(): void
    {
        $consumer = User::factory()->create(['role' => 'consumer']);

        $response = $this->actingAs($consumer)->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }
}
