<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::all()->pluck('id', 'title');

        $products = [
            ['title' => 'Tesla Model S', 'description' => 'Electric car with autopilot feature.', 'price' => 80000, 'category_id' => $categories['Cars']],
            ['title' => 'BMW 3 Series', 'description' => 'Luxury sedan with powerful engine.', 'price' => 45000, 'category_id' => $categories['Cars']],
            ['title' => 'Nivea Moisturizer', 'description' => 'Daily moisturizer for smooth skin.', 'price' => 10, 'category_id' => $categories['Skin Care']],
            ['title' => 'L’Oreal Sunscreen', 'description' => 'SPF 50+ sunscreen for all skin types.', 'price' => 15, 'category_id' => $categories['Skin Care']],
            ['title' => 'iPhone 13', 'description' => 'Latest Apple smartphone with A15 chip.', 'price' => 999, 'category_id' => $categories['Phones']],
            ['title' => 'Samsung Galaxy S21', 'description' => 'Flagship Android phone with excellent camera.', 'price' => 899, 'category_id' => $categories['Phones']],
            ['title' => 'MacBook Pro', 'description' => 'Apple’s high-performance laptop.', 'price' => 2000, 'category_id' => $categories['Laptops']],
            ['title' => 'Dell XPS 13', 'description' => 'Compact and powerful ultrabook.', 'price' => 1500, 'category_id' => $categories['Laptops']],
            ['title' => 'Louis Vuitton Handbag', 'description' => 'Luxury handbag with iconic design.', 'price' => 2500, 'category_id' => $categories['Bags']],
            ['title' => 'Herschel Backpack', 'description' => 'Durable and stylish backpack.', 'price' => 80, 'category_id' => $categories['Bags']],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
