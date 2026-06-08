<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Promo;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed categories first
        $this->call(CategorySeeder::class);

        // Get a category for promos
        $category = Category::first();

        // Create 2 seller users with verified SellerProfiles
        $seller1 = User::factory()->seller()->create([
            'name' => 'Seller Satu',
            'email' => 'seller1@example.com',
        ]);

        $sellerProfile1 = SellerProfile::create([
            'user_id' => $seller1->id,
            'business_name' => 'Warung Makan Enak',
            'business_category' => 'Kuliner',
            'description' => 'Warung makan dengan menu masakan Indonesia yang lezat.',
            'address' => 'Jl. Sudirman No. 10, Jakarta Pusat',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'is_verified' => true,
        ]);

        $seller2 = User::factory()->seller()->create([
            'name' => 'Seller Dua',
            'email' => 'seller2@example.com',
        ]);

        $sellerProfile2 = SellerProfile::create([
            'user_id' => $seller2->id,
            'business_name' => 'Butik Fashion Keren',
            'business_category' => 'Fashion',
            'description' => 'Butik fashion dengan koleksi terkini untuk pria dan wanita.',
            'address' => 'Jl. Thamrin No. 5, Jakarta Pusat',
            'latitude' => -6.1944,
            'longitude' => 106.8229,
            'is_verified' => true,
        ]);

        // Create 5 promos: 2 active, 1 draft, 1 expired, 1 hot deal
        // Active promo 1
        Promo::create([
            'seller_id' => $sellerProfile1->id,
            'category_id' => $category->id,
            'title' => 'Promo Makan Siang Hemat',
            'description' => 'Diskon 30% untuk semua menu makan siang.',
            'discount_percentage' => 30.00,
            'original_price' => 50000.00,
            'promo_price' => 35000.00,
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'is_hot_deal' => false,
            'status' => 'active',
        ]);

        // Active promo 2
        Promo::create([
            'seller_id' => $sellerProfile2->id,
            'category_id' => $category->id,
            'title' => 'Flash Sale Baju Musim Panas',
            'description' => 'Diskon 50% untuk koleksi baju musim panas.',
            'discount_percentage' => 50.00,
            'original_price' => 200000.00,
            'promo_price' => 100000.00,
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'is_hot_deal' => false,
            'status' => 'active',
        ]);

        // Draft promo
        Promo::create([
            'seller_id' => $sellerProfile1->id,
            'category_id' => $category->id,
            'title' => 'Promo Makan Malam Spesial (Draft)',
            'description' => 'Promo makan malam yang belum dipublikasikan.',
            'discount_percentage' => 20.00,
            'original_price' => 80000.00,
            'promo_price' => 64000.00,
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(14)->toDateString(),
            'is_hot_deal' => false,
            'status' => 'draft',
        ]);

        // Expired promo
        Promo::create([
            'seller_id' => $sellerProfile2->id,
            'category_id' => $category->id,
            'title' => 'Promo Lebaran (Expired)',
            'description' => 'Promo spesial lebaran yang sudah berakhir.',
            'discount_percentage' => 40.00,
            'original_price' => 150000.00,
            'promo_price' => 90000.00,
            'start_date' => now()->subDays(10)->toDateString(),
            'end_date' => now()->subDay()->toDateString(),
            'is_hot_deal' => false,
            'status' => 'expired',
        ]);

        // Hot deal promo (active, ending within 24 hours)
        Promo::create([
            'seller_id' => $sellerProfile1->id,
            'category_id' => $category->id,
            'title' => 'Hot Deal! Paket Makan Keluarga',
            'description' => 'Penawaran terbatas! Paket makan keluarga dengan harga spesial.',
            'discount_percentage' => 60.00,
            'original_price' => 300000.00,
            'promo_price' => 120000.00,
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addHours(24)->toDateString(),
            'is_hot_deal' => true,
            'status' => 'active',
        ]);

        // Create a test consumer user
        User::factory()->consumer()->create([
            'name' => 'Consumer Test',
            'email' => 'consumer@example.com',
        ]);

        // Create an admin user
        User::factory()->admin()->create([
            'name' => 'Admin Promora',
            'email' => 'admin@example.com',
        ]);
    }
}
