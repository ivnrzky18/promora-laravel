<?php

namespace Tests\Property;

use App\Models\SellerProfile;
use App\Models\User;
use Eris\Generators;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Property-Based Tests for Registration and Authentication
 *
 * Validates: Requirements 1.3, 1.4, 1.5, 1.9
 */
class RegistrationPropertyTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Generate a valid consumer registration payload.
     */
    private function consumerPayload(string $name, string $email, string $location): array
    {
        return [
            'name'                  => $name,
            'email'                 => $email,
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
            'location'              => $location,
        ];
    }

    /**
     * Generate a valid seller registration payload.
     */
    private function sellerPayload(
        string $name,
        string $email,
        string $businessName,
        string $businessCategory,
        string $address
    ): array {
        return [
            'name'                  => $name,
            'email'                 => $email,
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
            'business_name'         => $businessName,
            'business_category'     => $businessCategory,
            'address'               => $address,
        ];
    }

    // ─── Property 1: Consumer registration → role=consumer ───────────────────

    /**
     * Property 1: For any valid consumer registration data,
     * the created user always has role=consumer.
     *
     * Validates: Requirements 1.3
     */
    public function testConsumerRegistrationAlwaysCreatesUserWithRoleConsumer(): void
    {
        $names = Generators::elements([
            'Budi Santoso', 'Siti Rahayu', 'Andi Wijaya', 'Dewi Lestari',
            'Rudi Hartono', 'Ani Kusuma', 'Joko Susilo', 'Maya Putri',
        ]);

        $locations = Generators::elements([
            'Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta',
            'Semarang', 'Medan', 'Makassar', 'Palembang',
        ]);

        $emailIndex = Generators::choose(1, 999999);

        $this->forAll($names, $locations, $emailIndex)
            ->withMaxSize(100)
            ->then(function (string $name, string $location, int $idx) {
                // Use unique email per iteration to avoid duplicate constraint
                $email = "consumer{$idx}@example.com";

                $response = $this->post(route('consumer.register'), $this->consumerPayload($name, $email, $location));

                // Registration should succeed (redirect to dashboard)
                $response->assertRedirect(route('consumer.dashboard'));

                // The created user must have role=consumer
                $user = User::where('email', $email)->first();
                $this->assertNotNull($user, "User with email {$email} should exist in database");
                $this->assertEquals('consumer', $user->role, "User role should be 'consumer', got '{$user->role}'");

                // Clean up for next iteration
                $user->delete();
                $this->app['auth']->logout();
            });
    }

    // ─── Property 2: Seller registration → role=seller + SellerProfile ───────

    /**
     * Property 2: For any valid seller registration data,
     * the created user always has role=seller AND a SellerProfile is created.
     *
     * Validates: Requirements 1.4
     */
    public function testSellerRegistrationAlwaysCreatesUserWithRoleSellerAndProfile(): void
    {
        $names = Generators::elements([
            'Toko Maju', 'Warung Barokah', 'Usaha Jaya', 'Dagang Makmur',
            'Bisnis Sukses', 'Karya Mandiri', 'Usaha Bersama', 'Toko Sejahtera',
        ]);

        $categories = Generators::elements([
            'Kuliner', 'Fashion', 'Jasa', 'Kesehatan',
            'Pendidikan', 'Hiburan', 'Elektronik', 'Otomotif',
        ]);

        $addresses = Generators::elements([
            'Jl. Sudirman No. 1, Jakarta',
            'Jl. Braga No. 10, Bandung',
            'Jl. Malioboro No. 5, Yogyakarta',
            'Jl. Pemuda No. 20, Semarang',
            'Jl. Thamrin No. 15, Jakarta',
        ]);

        $emailIndex = Generators::choose(1, 999999);

        $this->forAll($names, $categories, $addresses, $emailIndex)
            ->withMaxSize(100)
            ->then(function (string $name, string $category, string $address, int $idx) {
                $email = "seller{$idx}@example.com";

                $response = $this->post(route('seller.register'), $this->sellerPayload(
                    $name,
                    $email,
                    "Toko {$name}",
                    $category,
                    $address
                ));

                // Registration should succeed (redirect to seller dashboard)
                $response->assertRedirect(route('seller.dashboard'));

                // The created user must have role=seller
                $user = User::where('email', $email)->first();
                $this->assertNotNull($user, "User with email {$email} should exist in database");
                $this->assertEquals('seller', $user->role, "User role should be 'seller', got '{$user->role}'");

                // A SellerProfile must be created for this user
                $profile = SellerProfile::where('user_id', $user->id)->first();
                $this->assertNotNull($profile, "SellerProfile should be created for user {$user->id}");

                // Clean up for next iteration
                $profile->forceDelete();
                $user->delete();
                $this->app['auth']->logout();
            });
    }

    // ─── Property 3: Duplicate email → rejected ───────────────────────────────

    /**
     * Property 3: For any email that is already registered,
     * a second registration attempt with the same email is always rejected.
     *
     * Validates: Requirements 1.5
     */
    public function testDuplicateEmailRegistrationIsAlwaysRejected(): void
    {
        $emailIndex = Generators::choose(1, 999999);

        $names = Generators::elements([
            'Budi Santoso', 'Siti Rahayu', 'Andi Wijaya', 'Dewi Lestari',
        ]);

        $locations = Generators::elements([
            'Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta',
        ]);

        $this->forAll($emailIndex, $names, $locations)
            ->withMaxSize(100)
            ->then(function (int $idx, string $name, string $location) {
                $email = "duplicate{$idx}@example.com";

                // First registration — should succeed
                $this->post(route('consumer.register'), $this->consumerPayload($name, $email, $location));
                $this->app['auth']->logout();

                $countBefore = User::where('email', $email)->count();
                $this->assertEquals(1, $countBefore, "First registration should create exactly one user");

                // Second registration with same email — should be rejected
                $response = $this->post(route('consumer.register'), $this->consumerPayload($name, $email, $location));
                $response->assertSessionHasErrors('email');

                // No new user should be created
                $countAfter = User::where('email', $email)->count();
                $this->assertEquals(1, $countAfter, "Duplicate email registration should not create a new user");

                // Clean up for next iteration
                User::where('email', $email)->delete();
                $this->app['auth']->logout();
            });
    }

    // ─── Property 4: Wrong credentials → no session ───────────────────────────

    /**
     * Property 4: For any wrong credentials (non-existent email or wrong password),
     * the login attempt never creates an authenticated session.
     *
     * Validates: Requirements 1.9
     */
    public function testWrongCredentialsLoginNeverCreatesSession(): void
    {
        $emailIndex = Generators::choose(1, 999999);

        $wrongPasswords = Generators::elements([
            'wrongpassword',
            'incorrect123',
            'notmypassword',
            'badpass456',
            'wrongcreds789',
        ]);

        $this->forAll($emailIndex, $wrongPasswords)
            ->withMaxSize(100)
            ->then(function (int $idx, string $wrongPassword) {
                $email = "logintest{$idx}@example.com";

                // Attempt login with non-existent email
                $response = $this->post('/consumer/login', [
                    'email'    => $email,
                    'password' => $wrongPassword,
                ]);

                // Should not be authenticated
                $this->assertGuest(null, "Login with non-existent email should not create a session");

                // Should have validation/auth errors
                $response->assertSessionHasErrors('email');

                $this->app['auth']->logout();
            });
    }

    /**
     * Property 4b: For an existing user, login with wrong password never creates a session.
     *
     * Validates: Requirements 1.9
     */
    public function testWrongPasswordForExistingUserNeverCreatesSession(): void
    {
        $emailIndex = Generators::choose(1, 999999);

        $wrongPasswords = Generators::elements([
            'wrongpassword',
            'incorrect123',
            'notmypassword',
            'badpass456',
            'wrongcreds789',
        ]);

        $this->forAll($emailIndex, $wrongPasswords)
            ->withMaxSize(100)
            ->then(function (int $idx, string $wrongPassword) {
                $email = "existinglogin{$idx}@example.com";

                // Create a real user with a known password
                $user = User::factory()->create([
                    'email'    => $email,
                    'password' => bcrypt('CorrectPassword123!'),
                    'role'     => 'consumer',
                ]);

                // Attempt login with wrong password
                $response = $this->post('/consumer/login', [
                    'email'    => $email,
                    'password' => $wrongPassword,
                ]);

                // Should not be authenticated
                $this->assertGuest(null, "Login with wrong password should not create a session");

                // Should have auth errors
                $response->assertSessionHasErrors('email');

                // Clean up
                $user->delete();
                $this->app['auth']->logout();
            });
    }
}
