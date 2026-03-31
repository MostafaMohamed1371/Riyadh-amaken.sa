<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Restaurants', 'slug' => 'restaurants', 'icon' => 'utensils', 'sort_order' => 1],
            ['name' => 'Activities', 'slug' => 'activities', 'icon' => 'map-pin', 'sort_order' => 2],
            ['name' => 'Sports', 'slug' => 'sports', 'icon' => 'trophy', 'sort_order' => 3],
            ['name' => 'Shopping', 'slug' => 'shopping', 'icon' => 'shopping-bag', 'sort_order' => 4],
            ['name' => 'Sightseeing', 'slug' => 'sightseeing', 'icon' => 'camera', 'sort_order' => 5],
            ['name' => 'Entertainment', 'slug' => 'entertainment', 'icon' => 'music', 'sort_order' => 6],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                array_merge($category, ['is_active' => true, 'description' => null])
            );
        }
    }
}
