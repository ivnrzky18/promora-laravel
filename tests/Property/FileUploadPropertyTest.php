<?php

namespace Tests\Property;

use App\Models\Category;
use App\Models\SellerProfile;
use App\Models\User;
use Eris\Generators;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Property-Based Tests for File Upload Validation
 *
 * Validates: Requirements 13.1, 13.2
 */
class FileUploadPropertyTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    // ─── Property 21: Validasi file upload poster_image ──────────────────────

    /**
     * Property 21a: Validation always accepts jpeg/png/webp files with size ≤ 2MB.
     *
     * For any valid image file (jpeg, png, or webp) with size ≤ 2048 KB,
     * the promo creation form SHALL accept the file and create the promo.
     *
     * Validates: Requirements 13.1
     */
    public function testValidImageFormatsAndSizeAreAlwaysAccepted(): void
    {
        Storage::fake('public');

        // Valid MIME types accepted by the validation rule
        $validMimes = Generators::elements(['jpeg', 'png', 'webp']);

        // Valid sizes: 1 KB to 2048 KB (2 MB)
        $validSizes = Generators::choose(1, 2048);

        $iterationIndex = Generators::choose(1, 999999);

        $this->forAll($validMimes, $validSizes, $iterationIndex)
            ->withMaxSize(100)
            ->then(function (string $mime, int $sizeKb, int $idx) {
                // Create a seller with a verified SellerProfile
                $seller = User::factory()->seller()->create([
                    'email' => "seller_prop21a_{$idx}@example.com",
                ]);
                $sellerProfile = SellerProfile::factory()->create([
                    'user_id' => $seller->id,
                ]);

                // Create a category for the promo
                $category = Category::factory()->create();

                // Create a fake image file with the given MIME type and size
                $sizeBytes = $sizeKb * 1024;
                $file = UploadedFile::fake()->create(
                    "poster.{$mime}",
                    $sizeKb,
                    "image/{$mime}"
                );

                // Build valid promo creation payload with the image
                $payload = [
                    'title'               => "Promo Upload Test #{$idx}",
                    'description'         => 'Deskripsi promo dengan gambar valid.',
                    'discount_percentage' => 10,
                    'original_price'      => 100000,
                    'promo_price'         => 90000,
                    'start_date'          => now()->addDay()->toDateString(),
                    'end_date'            => now()->addDays(7)->toDateString(),
                    'category_id'         => $category->id,
                    'poster_image'        => $file,
                ];

                // Submit the promo creation form as the seller
                $response = $this->actingAs($seller)
                                 ->post(route('seller.promos.store'), $payload);

                // Property: valid image should be accepted — form should redirect (success)
                $response->assertRedirect();
                $response->assertSessionHasNoErrors();

                // Clean up for next iteration
                \App\Models\Promo::where('seller_id', $sellerProfile->id)->forceDelete();
                $category->delete();
                $sellerProfile->forceDelete();
                $seller->delete();
                $this->app['auth']->logout();
            });
    }

    /**
     * Property 21b: Validation always rejects files with invalid formats.
     *
     * For any file with a format other than jpeg, png, or webp (e.g., pdf, gif,
     * txt, mp4, doc), the promo creation form SHALL reject the file and return
     * a validation error for poster_image.
     *
     * Validates: Requirements 13.1, 13.2
     */
    public function testInvalidImageFormatsAreAlwaysRejected(): void
    {
        Storage::fake('public');

        // Invalid MIME types that should be rejected
        $invalidMimes = Generators::elements([
            ['ext' => 'pdf',  'mime' => 'application/pdf'],
            ['ext' => 'gif',  'mime' => 'image/gif'],
            ['ext' => 'txt',  'mime' => 'text/plain'],
            ['ext' => 'mp4',  'mime' => 'video/mp4'],
            ['ext' => 'doc',  'mime' => 'application/msword'],
            ['ext' => 'zip',  'mime' => 'application/zip'],
            ['ext' => 'svg',  'mime' => 'image/svg+xml'],
            ['ext' => 'bmp',  'mime' => 'image/bmp'],
            ['ext' => 'tiff', 'mime' => 'image/tiff'],
            ['ext' => 'exe',  'mime' => 'application/octet-stream'],
        ]);

        // Size within the valid range (≤ 2MB) — only format is invalid
        $validSizes = Generators::choose(1, 1024);

        $iterationIndex = Generators::choose(1, 999999);

        $this->forAll($invalidMimes, $validSizes, $iterationIndex)
            ->withMaxSize(100)
            ->then(function (array $mimeInfo, int $sizeKb, int $idx) {
                // Create a seller with a verified SellerProfile
                $seller = User::factory()->seller()->create([
                    'email' => "seller_prop21b_{$idx}@example.com",
                ]);
                $sellerProfile = SellerProfile::factory()->create([
                    'user_id' => $seller->id,
                ]);

                // Create a category for the promo
                $category = Category::factory()->create();

                // Create a fake file with an invalid MIME type
                $file = UploadedFile::fake()->create(
                    "poster.{$mimeInfo['ext']}",
                    $sizeKb,
                    $mimeInfo['mime']
                );

                // Build promo creation payload with the invalid file
                $payload = [
                    'title'               => "Promo Invalid Format #{$idx}",
                    'description'         => 'Deskripsi promo dengan format file tidak valid.',
                    'discount_percentage' => 10,
                    'original_price'      => 100000,
                    'promo_price'         => 90000,
                    'start_date'          => now()->addDay()->toDateString(),
                    'end_date'            => now()->addDays(7)->toDateString(),
                    'category_id'         => $category->id,
                    'poster_image'        => $file,
                ];

                // Submit the promo creation form as the seller
                $response = $this->actingAs($seller)
                                 ->post(route('seller.promos.store'), $payload);

                // Property: invalid format should be rejected — form should return validation error
                $response->assertSessionHasErrors('poster_image');

                // No promo should have been created
                $promoCount = \App\Models\Promo::where('seller_id', $sellerProfile->id)->count();
                $this->assertEquals(
                    0,
                    $promoCount,
                    "No promo should be created when poster_image has invalid format '{$mimeInfo['ext']}'"
                );

                // Clean up for next iteration
                $category->delete();
                $sellerProfile->forceDelete();
                $seller->delete();
                $this->app['auth']->logout();
            });
    }

    /**
     * Property 21c: Validation always rejects files with size > 2MB, even if format is valid.
     *
     * For any valid image file (jpeg, png, or webp) with size > 2048 KB (> 2MB),
     * the promo creation form SHALL reject the file and return a validation error
     * for poster_image.
     *
     * Validates: Requirements 13.1, 13.2
     */
    public function testFilesExceedingMaxSizeAreAlwaysRejected(): void
    {
        Storage::fake('public');

        // Valid MIME types — only size is invalid
        $validMimes = Generators::elements(['jpeg', 'png', 'webp']);

        // Invalid sizes: > 2048 KB (2 MB), up to 10 MB
        $oversizedKb = Generators::choose(2049, 10240);

        $iterationIndex = Generators::choose(1, 999999);

        $this->forAll($validMimes, $oversizedKb, $iterationIndex)
            ->withMaxSize(100)
            ->then(function (string $mime, int $sizeKb, int $idx) {
                // Create a seller with a verified SellerProfile
                $seller = User::factory()->seller()->create([
                    'email' => "seller_prop21c_{$idx}@example.com",
                ]);
                $sellerProfile = SellerProfile::factory()->create([
                    'user_id' => $seller->id,
                ]);

                // Create a category for the promo
                $category = Category::factory()->create();

                // Create a fake image file with valid format but oversized
                $file = UploadedFile::fake()->create(
                    "poster.{$mime}",
                    $sizeKb,
                    "image/{$mime}"
                );

                // Build promo creation payload with the oversized file
                $payload = [
                    'title'               => "Promo Oversized Image #{$idx}",
                    'description'         => 'Deskripsi promo dengan gambar terlalu besar.',
                    'discount_percentage' => 10,
                    'original_price'      => 100000,
                    'promo_price'         => 90000,
                    'start_date'          => now()->addDay()->toDateString(),
                    'end_date'            => now()->addDays(7)->toDateString(),
                    'category_id'         => $category->id,
                    'poster_image'        => $file,
                ];

                // Submit the promo creation form as the seller
                $response = $this->actingAs($seller)
                                 ->post(route('seller.promos.store'), $payload);

                // Property: oversized file should be rejected — form should return validation error
                $response->assertSessionHasErrors('poster_image');

                // No promo should have been created
                $promoCount = \App\Models\Promo::where('seller_id', $sellerProfile->id)->count();
                $this->assertEquals(
                    0,
                    $promoCount,
                    "No promo should be created when poster_image size is {$sizeKb} KB (> 2048 KB)"
                );

                // Clean up for next iteration
                $category->delete();
                $sellerProfile->forceDelete();
                $seller->delete();
                $this->app['auth']->logout();
            });
    }
}
