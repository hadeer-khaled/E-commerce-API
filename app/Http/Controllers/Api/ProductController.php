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

use App\Models\Product;
use App\Models\Attachment;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $perPage    =  $request->query('perPage', 4);
        $search     =  $request->query('search', null);
        $products = Product::with("attachments","category")
        ->when($search , function($query , $search){
            return $query->where('title' , 'like' , "%{$search}%");
        })
        ->paginate($perPage);

        $productsPaginate = Product::paginate(8);

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

            if ($request->has('paths') && count($request->input('paths')) > 0) {
                foreach ($request->input('paths') as $path) {
                    $product->attachments()->create(['filename' => $path]);
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
                    $product->attachments()->create(['filename' => $path]);
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
   
        $storedPaths = [];
    
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                try {
                    $path = $image->store('images', 'public');
                    $storedPaths[] = $path;
                } catch (Exception $e) {
                    return response()->json(['error' => 'Error storing image: ' . $e->getMessage()], 500);
                }
            }
        }
    
        return response()->json(['message' => 'Images stored successfully', 'paths' => $storedPaths], 200);
    }
    
    
}
