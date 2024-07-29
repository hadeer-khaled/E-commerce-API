<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Exception;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

use App\Http\Resources\CategoryResource;

use App\Models\Category;
use App\Models\Attachment;

class CategoryController extends Controller
{

    public function index()
    {
       $categories = Category::with("attachment")->paginate(2);

        return response()->json([
            "data" => CategoryResource::collection($categories),
            "message" => "Categories retrieved successfully",
        ], 200);
    }


    public function store(StoreCategoryRequest $request)
    {
        DB::beginTransaction();
    
        try {
            $validatedData = $request->validated();
            $category = Category::create($validatedData);
    
            if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $path = $image->store('images', 'public');
    
                    Attachment::create([
                        'filename' => $path,
                        'attachable_id' => $category->id,
                        'attachable_type' => Category::class,
                    ]);
            }
    
            DB::commit();
    
            return response()->json([
                "data" => new CategoryResource($category),
                "message" => "Category created successfully",
            ], 201);
    
        } catch (Exception $e) {
            DB::rollBack();
    
            return response()->json([
                "message" => "Failed to create category",
                "error" => $e->getMessage(),
            ], 500);
        }
    }


    public function show(Category $category)
    {
        return response()->json([
            "data" =>  new CategoryResource($category),
            "message" => "Category retrieved successfully",
        ], 200);
    }


    public function update(UpdateCategoryRequest $request, Category $category)
    {
        DB::beginTransaction();
    
        try {
            $validatedData = $request->validated();
            $category->update($validatedData);
    
            if ($request->hasFile('image')) {
                    $category->attachment()->delete();
                    $image = $request->file('image');
                    $path = $image->store('images', 'public');

                    Attachment::create([
                        'filename' => $path,
                        'attachable_id' => $category->id,
                        'attachable_type' => Category::class,
                    ]);
            }
    
            DB::commit();
    
            return response()->json([
                "data" => new CategoryResource($category->fresh()),
                "message" => "Category updated successfully",
            ], 201);
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                "message" => "Failed to update category",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

 
    public function destroy(Category $category)
    {
        try {
            DB::beginTransaction();

            if($category->attachment) {
                Storage::disk('public')->delete($category->attachment->filename);
            }
    
            $category->attachment()->delete();
            $category->delete();
    
            DB::commit();
    
            return response()->json([
                'message' => 'Category deleted successfully',
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'message' => 'Failed to delete Category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
