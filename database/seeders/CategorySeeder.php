<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Kuliner',
                'slug' => 'kuliner',
                'icon' => '🍜',
            ],
            [
                'name' => 'Fashion',
                'slug' => 'fashion',
                'icon' => '👗',
            ],
            [
                'name' => 'Jasa',
                'slug' => 'jasa',
                'icon' => '🔧',
            ],
            [
                'name' => 'Kesehatan',
                'slug' => 'kesehatan',
                'icon' => '💊',
            ],
            [
                'name' => 'Pendidikan',
                'slug' => 'pendidikan',
                'icon' => '📚',
            ],
            [
                'name' => 'Hiburan',
                'slug' => 'hiburan',
                'icon' => '🎭',
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
