<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Exception;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

use App\Http\Resources\ProductResource;

use App\Models\Product;
use App\Models\Attachment;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::with("attachments","category")->paginate(2);

        return response()->json([
            "data" => ProductResource::collection($products),
            "message" => "Products retrieved successfully",
        ], 200);
    }


    public function store(StoreProductRequest $request)
    {
        DB::beginTransaction();
    
        try {
            $validatedData = $request->validated();
            $product = Product::create($validatedData);
    
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('images', 'public');
    
                    Attachment::create([
                        'filename' => $path,
                        'attachable_id' => $product->id,
                        'attachable_type' => Product::class,
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
            $validatedData = $request->validated();
            $product->update($validatedData);
    
            if ($request->hasFile('images')) {
                $product->attachments()->delete();
    
                foreach ($request->file('images') as $image) {
                    // $image = $request->file('images');
                    $path = $image->store('images', 'public');
    
                    Attachment::create([
                        'filename' => $path,
                        'attachable_id' => $product->id,
                        'attachable_type' => Product::class,
                    ]);
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
    
}
