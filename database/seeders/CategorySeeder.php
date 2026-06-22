<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Elektronik', 'slug' => 'elektronik', 'icon' => '📱'],
            ['name' => 'Fashion', 'slug' => 'fashion', 'icon' => '👕'],
            ['name' => 'Hiburan & Wisata', 'slug' => 'hiburan-wisata', 'icon' => '🎡'],
            ['name' => 'Jasa & Layanan', 'slug' => 'jasa-layanan', 'icon' => '🛠️'],
            ['name' => 'Kecantikan & Perawatan', 'slug' => 'kecantikan-perawatan', 'icon' => '💄'],
            ['name' => 'Kesehatan', 'slug' => 'kesehatan', 'icon' => '💊'],
            ['name' => 'Makanan & Minuman', 'slug' => 'makanan-minuman', 'icon' => '🍜'],
            ['name' => 'Otomotif', 'slug' => 'otomotif', 'icon' => '🚗'],
            ['name' => 'Pendidikan', 'slug' => 'pendidikan', 'icon' => '📘'],
            ['name' => 'Properti & Rumah', 'slug' => 'properti-rumah', 'icon' => '🏠'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}