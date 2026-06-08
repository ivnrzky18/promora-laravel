<?php

namespace Tests\Feature\Auth;

use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Valid seller registration data.
     */
    private function validSellerData(array $overrides = []): array
    {
        return array_merge([
            'name'                  => 'Warung Makan Pak Budi',
            'email'                 => 'pakbudi@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'business_name'         => 'Warung Makan Pak Budi',
            'business_category'     => 'Kuliner',
            'address'               => 'Jl. Sudirman No. 10, Bandung',
            'description'           => 'Warung makan dengan menu masakan Sunda.',
        ], $overrides);
    }

    /**
     * Test successful registration creates user with role=seller.
     */
    public function test_successful_registration_creates_user_with_seller_role(): void
    {
        $response = $this->post(route('seller.register'), $this->validSellerData());

        $response->assertRedirect(route('seller.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'pakbudi@example.com',
            'role'  => 'seller',
        ]);
    }

    /**
     * Test successful registration also creates a SellerProfile.
     */
    public function test_successful_registration_creates_seller_profile(): void
    {
        $this->post(route('seller.register'), $this->validSellerData());

        $user = User::where('email', 'pakbudi@example.com')->first();

        $this->assertNotNull($user);
        $this->assertDatabaseHas('seller_profiles', [
            'user_id'           => $user->id,
            'business_name'     => 'Warung Makan Pak Budi',
            'business_category' => 'Kuliner',
            'address'           => 'Jl. Sudirman No. 10, Bandung',
        ]);
    }

    /**
     * Test redirect to seller dashboard after registration.
     */
    public function test_redirect_to_seller_dashboard_after_registration(): void
    {
        $response = $this->post(route('seller.register'), $this->validSellerData());

        $response->assertRedirect(route('seller.dashboard'));
    }

    /**
     * Test that user is authenticated after registration.
     */
    public function test_user_is_authenticated_after_registration(): void
    {
        $this->post(route('seller.register'), $this->validSellerData());

        $this->assertAuthenticated();
    }

    /**
     * Test that duplicate email returns a validation error.
     */
    public function test_duplicate_email_returns_validation_error(): void
    {
        User::factory()->create(['email' => 'pakbudi@example.com']);

        $response = $this->post(route('seller.register'), $this->validSellerData());

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test that missing required business fields fail validation.
     */
    public function test_missing_business_fields_fail_validation(): void
    {
        $response = $this->post(route('seller.register'), [
            'name'                  => 'Test Seller',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            // Missing: business_name, business_category, address
        ]);

        $response->assertSessionHasErrors(['business_name', 'business_category', 'address']);
    }

    /**
     * Test that description is optional.
     */
    public function test_description_is_optional(): void
    {
        $data = $this->validSellerData();
        unset($data['description']);

        $response = $this->post(route('seller.register'), $data);

        $response->assertRedirect(route('seller.dashboard'));
        $this->assertDatabaseHas('users', ['email' => 'pakbudi@example.com', 'role' => 'seller']);
    }

    /**
     * Test that the seller registration form page is accessible.
     */
    public function test_registration_form_is_accessible(): void
    {
        $response = $this->get(route('seller.register'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.seller.register');
    }

    /**
     * Test that SellerProfile is_verified defaults to false on registration.
     */
    public function test_seller_profile_is_not_verified_by_default(): void
    {
        $this->post(route('seller.register'), $this->validSellerData());

        $user = User::where('email', 'pakbudi@example.com')->first();
        $profile = SellerProfile::where('user_id', $user->id)->first();

        $this->assertNotNull($profile);
        $this->assertFalse((bool) $profile->is_verified);
    }
}
