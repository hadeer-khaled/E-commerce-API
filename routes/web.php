<?php

use Illuminate\Support\Facades\Route;
use App\Models\Category;
use App\Models\Product;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    $category= Category::find(1);
    $category->attachment()->create(['filename' => 'phone_category_image.jpg']);

    $product= Product::find(2);
    $product->attachments()->create(['filename' => 'samsung_image.jpg']);

    $category = Category::find(1);
    $category_attachments = $category->attachment;

    $product = Product::find(2);
    $product_attachments = $product->attachments;

    dd($product_attachments);



    return view('welcome');
});
