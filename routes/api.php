<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\PasswordResetController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post("register" , [AuthController::class , 'register'] )->name('register'); 
Route::post("login" , [AuthController::class , 'login'])->name('login'); 

Route::apiResource('roles',RoleController::class)->only([ 'index', 'store']);
Route::post('roles/{role}/add-permission',[RoleController::class , 'addPermissionToRole']);

Route::apiResource('permissions', PermissionController::class)->only(['store']);

Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

Route::get('categories/export', [CategoryController::class, 'export'])->name('categories.export');

Route::middleware(['auth:api'])->group(function(){
    
    Route::post("logout" , [AuthController::class , 'logout'])->name('logout'); 
    
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::post('products/store-images' , [ProductController::class , 'storeImages'])->name('products.store-images');
    Route::delete('products/delete-images' , [ProductController::class , 'deleteImages'])->name('products.delete-images');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('categories/import', [CategoryController::class, 'import'])->name('categories.import');
    Route::PUT('categories/{category}/upload-image', [CategoryController::class, 'uploadImage'])->name('categories.upload-image');


    Route::middleware('role:admin')->group(function(){

        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');   
        Route::post('users/{user}/add-role',[UserController::class , 'addRoleToUser']);
    });


});

