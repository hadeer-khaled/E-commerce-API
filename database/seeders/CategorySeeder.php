<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // public function run()
    // {
    //     $categories = [
    //         ['title' => 'Cars'],
    //         ['title' => 'Skin Care'],
    //         ['title' => 'Phones'],
    //         ['title' => 'Laptops'],
    //         ['title' => 'Bags'],
    //     ];

    //     foreach ($categories as $category) {
    //         Category::create($category);
    //     }
    // }
    public function run()
    {
        Category::factory()->count(50000)->create();
    }
}
