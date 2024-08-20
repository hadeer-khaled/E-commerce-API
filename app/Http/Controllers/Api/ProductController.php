<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Exception;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

use App\Http\Resources\ProductResource;
use App\Http\Resources\ImageResource;

use App\Models\Product;
use App\Models\Attachment;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $perPage              =  $request->query('perPage', 4);
        $search               =  $request->query('search', null);
        $selectedCategoryId     =  $request->query('category', null);
        $products             = Product::with("attachments","category")
        ->when($search , function($query , $search){
            return $query->where('title' , 'like' , "%{$search}%");
        })      
        ->when($selectedCategoryId, function ($query, $selectedCategoryId) {
            return $query->where('category_id', $selectedCategoryId);
        })
        ->paginate($perPage);

        return response()->json([
            "message" => "Products retrieved successfully",
            "data" => ProductResource::collection($products),
            "links"=> $products->toArray()['links'],
        ], 200);
    }


    public function store(StoreProductRequest $request)
    {
        DB::beginTransaction();
    
        try {
            $product = Product::create($request->validated());

            if ($request->has('images') && count($request->input('images')) > 0) {
                foreach ($request->input('images') as $image) {
                    $product->attachments()->create([
                        'original_filename' => $image["original_filename"],
                        'storage_filename' => $image['storage_filename'],
                        'url' => $image['url']
                    ]);
                }
            }
            
            DB::commit();
    
            return response()->json([
                "data" => new ProductResource($product),
                "message" => "Product created successfully",
            ], 201);
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                "message" => "Failed to create product",
                "error" => $e->getMessage(),
            ], 500);
        }
    }
 
    public function show(Product $product)
    {
        return response()->json([
            "data" =>  new ProductResource($product),
            "message" => "Product retrieved successfully",
        ], 200);
    }


    public function update(UpdateProductRequest $request, Product $product)
    {
        DB::beginTransaction();
    
        try {
            // $validatedData = $request->validated();
            $product->update($request->validated());
                
            if ($request->has('paths') && count($request->input('paths')) > 0) {
                $product->attachments()->delete();
                foreach ($request->input('paths') as $path) {
                    $product->attachments()->create(['storage_filename' => $path]);
                }
            }
                
            DB::commit();
    
            return response()->json([
                "data" => new ProductResource($product->fresh()),
                "message" => "Product updated successfully",
            ], 201);
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                "message" => "Failed to update product",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();
    
            foreach ($product->attachments as $image) {
                    Storage::disk('public')->delete($image->filename);
            }
    
            $product->attachments()->delete();
            $product->delete();
    
            DB::commit();
    
            return response()->json([
                'message' => 'Product deleted successfully',
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'message' => 'Failed to delete product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function storeImages(Request $request)
    {
        $storedImages = [];
        
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                try {
                    $originalName = $image->getClientOriginalName();
                    $storageName = $image->hashName();
                    $path = $image->store('images', 'public');
                    $url = asset('storage/' . $path);
    
                    $storedImages[] = [
                        'original_filename' => $originalName,
                        'storage_filename' => $storageName,
                        'url' => $url,
                    ];
                } catch (Exception $e) {
                    return response()->json(['error' => 'Error storing image: ' . $e->getMessage()], 500);
                }
            }
        }
        
        return response()->json(['message' => 'Images stored successfully', 'images' => $storedImages], 200);
    }
    
    
    
}
