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
            ['name' => 'Electronics', 'slug' => 'electronics', 'description' => 'Electronic devices and accessories', 'status' => 1, 'order_by' => 1],
            ['name' => 'Clothing', 'slug' => 'clothing', 'description' => 'Apparel and fashion items', 'status' => 1, 'order_by' => 2],
            ['name' => 'Home & Garden', 'slug' => 'home-garden', 'description' => 'Home improvement and garden supplies', 'status' => 1, 'order_by' => 3],
            ['name' => 'Sports', 'slug' => 'sports', 'description' => 'Sports equipment and fitness gear', 'status' => 1, 'order_by' => 4],
            ['name' => 'Books', 'slug' => 'books', 'description' => 'Books and publications', 'status' => 1, 'order_by' => 5],
            ['name' => 'Toys & Games', 'slug' => 'toys-games', 'description' => 'Toys and gaming products', 'status' => 1, 'order_by' => 6],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
