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

use App\Services\ProductService;
use App\DTO\ProductUpdateDTO;

use Illuminate\Support\Facades\Log;


class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

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

                foreach ($request->input('images') as $image) {
                    $product->attachments()->create([
                        'original_filename' => $image["original_filename"],
                        'storage_filename' => $image['storage_filename'],
                        'url' => $image['url']
                    ]);
                
            }
            
            DB::commit();
    
            return response()->json([
                "data" => ProductResource::make($product),
                "message" => "Product created successfully",
            ], 201);
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                "message" => "Failed to create product",
                "error" => $e->getMessage(),
            ], 400);
        }
    }
 
    public function show(Product $product)
    {
        return response()->json([
            "data" =>  ProductResource::make($product),
            "message" => "Product retrieved successfully",
        ], 200);
    }


    // public function update(UpdateProductRequest $request, Product $product)
    // {
    //     DB::beginTransaction();
    
    //     try {
    //         $product->update($request->validated());

    //     if ($request->has('images')) {
    //         if (isset($request->images['deleted']) && count($request->images['deleted']) > 0) {
    //             foreach ($request->images['deleted'] as $attachmentId) {
    //                 $product->attachments()->where('id', $attachmentId)->delete();
    //             }
    //         }

    //         if (isset($request->images['created']) && count($request->images['created']) > 0) {
    //             foreach ($request->images['created'] as $image) {
    //                 $product->attachments()->create([
    //                     'original_filename' => $image['original_filename'],
    //                     'storage_filename' => $image['storage_filename'],
    //                     'url' => $image['url'],
    //                 ]);
    //             }
    //         }
    //     }
                                
    //         DB::commit();
    
    //         return response()->json([
    //             "data" => ProductResource::make($product->fresh()),
    //             "message" => "Product updated successfully",
    //         ], 201);
    
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    
    //         return response()->json([
    //             "message" => "Failed to update product",
    //             "error" => $e->getMessage(),
    //         ], 400);
    //     }
    // }
    public function update(UpdateProductRequest $request, Product $product)
    {
            $dto = ProductUpdateDTO::fromArray($request->validated());

            $updatedProduct = $this->productService->updateProduct($product, $dto);

            return response()->json([
                "data" => ProductResource::make($updatedProduct),
                "message" => "Product updated successfully",
            ], 200);

    }


    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();
    
            foreach ($product->attachments as $image) {
                    Storage::disk('public')->delete("images/".$image->storage_filename);
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
            ], 400);
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
                    return response()->json(['error' => 'Error storing image: ' . $e->getMessage()], 400);
                }
            }
        }
        
        return response()->json(['message' => 'Images stored successfully', 'images' => $storedImages], 200);
    }

    // public function deleteImages(Request $request)
    // {
    //     if ($request->input('images')) {
    //         foreach($request['images'] as $imagePath) {
    //             Storage::disk('public')->delete($imagePath);
    //         }
    //     }
    //     Log::info('Images Input:', ['images' => $request->input('images')]);
    
    //     return response()->json(['message' => 'Images deleted successfully.'], 200);
    // }
    public function deleteImages(Request $request)
    {
    
        foreach ( $request->input('images') as $imagePath) {
            Storage::disk('public')->delete("images/".$imagePath);
        }

        return response()->json(['message' => 'Images deleted successfully.'], 200);
    }

    
    
    
}
