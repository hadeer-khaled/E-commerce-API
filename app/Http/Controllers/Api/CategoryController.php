<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Bus; 
use App\Jobs\ExportCategoryChunkJob;
use App\Jobs\SendExportNotification;

use App\Notifications\NotifyUserOfCompletedExport;
use App\Jobs\InsertExportLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Exception;

use Illuminate\Http\Request;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

use App\Http\Resources\CategoryResource;

use App\Models\Category;
use App\Models\Attachment;
use App\Models\User;

use App\Imports\CategoriesImport ;
use App\Exports\CategoryExport ;
//use App\Exports\CategoryExportWithChunks ;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;


use App\Traits\HandlesAttachments;

class CategoryController extends Controller
{
    use HandlesAttachments;


    /**
     * @OA\Get(
     *     path="/categories",
     *     summary="Get a list of categories",
     *     description="Retrieve a paginated list of categories with their attachments.",
     *     tags={"Categories"},
     *     @OA\Response(
     *         response=200,
     *         description="Categories retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *
     *     ),
     *     security={{"bearer":{}}},
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */

    public function index(Request $request)
    {
        $perPage    =  $request->query('perPage', null);
        $search     =  $request->query('search', null);
        $categories =  Category::with("attachment")
                        ->when($search , function($query , $search){
                            return $query->where('title' , 'like' , "%{$search}%");
                        })
                        ->paginate($perPage);

        return response()->json([
            "message" => "Categories retrieved successfully",
            "data" => CategoryResource::collection($categories),
            "links"=> $categories->toArray()['links'],
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/categories",
     *     summary="Create a new category",
     *     tags={"Categories"},
     *     @OA\RequestBody(
     *         ref="#/components/requestBodies/CreateCategory"
     *     ),
     *     security={{"bearer":{}}},
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Category"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Category created successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to create category",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Failed to create category"
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Detailed error message"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="The title field is required."
     *             )
     *         )
     *     )
     * )
     */

    public function store(StoreCategoryRequest $request)
    {
        DB::beginTransaction();

        try {
            // $validatedData = $request->validated();
            $category = Category::create($request->validated());

            $this->handleAttachment($category, $request , 'image' , false);


            DB::commit();

            return response()->json([
                "data" => CategoryResource::make($category),
                "message" => "Category created successfully",
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                "message" => "Failed to create category",
                "error" => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/categories/{id}",
     *     summary="Retrieve a specific category",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *         description="The ID of the category"
     *     ),
     *     security={{"bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *             ref="#/components/schemas/Category"),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Category not found"
     *             )
     *         )
     *     )
     * )
     */
    public function show(Category $category)
    {
        return response()->json([
            "data" =>  CategoryResource::make($category),
            "message" => "Category retrieved successfully",
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/categories/{id}",
     *     summary="Update an existing category",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *         description="The ID of the category to update"
     *     ),
     *     @OA\RequestBody(
     *         ref="#/components/requestBodies/CreateCategory"
     *     ),
     *     @OA\Response(
     *         response=201,
     *         ref="#/components/schemas/Category"
     *     ),
     *     security={{"bearer":{}}},
     *     @OA\Response(
     *         response=400,
     *         description="Failed to update category",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Failed to update category"
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Detailed error message"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="The title field is required."
     *             )
     *         )
     *     )
     * )
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        DB::beginTransaction();

        try {
            // $validatedData = $request->validated();
            $category->update($request->validated());

            $this->handleAttachment($category, $request , 'image' , true);

            // if ($request->hasFile('image')) {
            //         $category->attachment()->delete();
            //         $image = $request->file('image');
            //         $path = $image->store('images', 'public');

            //         Attachment::create([
            //             'filename' => $path,
            //             'attachable_id' => $category->id,
            //             'attachable_type' => Category::class,
            //         ]);
            // }

            DB::commit();

            return response()->json([
                "data" => CategoryResource::make($category->fresh()),
                "message" => "Category updated successfully",
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "message" => "Failed to update category",
                "error" => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/categories/{id}",
     *     summary="Delete a category",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the category to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     security={{"bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Category deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Category deleted successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to delete category",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Failed to delete Category"
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Detailed error message"
     *             )
     *         )
     *     ),
     * )
     */


    public function destroy(Category $category)
    {
        try {
            DB::beginTransaction();

            if($category->attachment) {
                Storage::disk('public')->delete("images/".$category->attachment->storageName);
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
            ], 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/categories/import",
     *     summary="Import categories from a file",
     *     tags={"Categories"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"categories"},
     *                 @OA\Property(
     *                     property="categories",
     *                     type="string",
     *                     format="binary",
     *                     description="The file containing categories to import (xlsx, xls, csv)"
     *                 )
     *             )
     *         )
     *     ),
     *     security={{"bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Import Categories successful",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Import Categories successful"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Import Categories failed"
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 example={"categories": "The categories field is required."}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Import Categories failed"
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Detailed error message"
     *             )
     *         )
     *     )
     * )
     */

    public function import(Request $request){
        $validatedData = $request->validate([
             'categories'=> ['required', 'file', 'mimes:xlsx,xls,csv'],
            ]);
        try {
            Excel::import(new CategoriesImport, $validatedData['categories']);
            return response()->json([
                'message' => 'Import Categories successful',
            ], 200);
        }
        catch (ValidationException $e) {
            return response()->json([
                'message' => 'Import Categories failed',
                'error' => $e->errors(),
            ], 422);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => 'Import Categories failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function uploadImage(Request $request , Category $category){
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = $image->store('images', 'public');
        }
        $category->attachment()->delete();
        $category->attachment()->create( ['filename' => $path]);
        return response()->json([
            "message" => "Category image uploaded successfully",
        ], 201);
    }

    // public function export(Request $request){
    //     $filters = $request->only(['title']);
    //     $user = User::find($request['user_id']);
    //     $fileName = 'category_' . time() . '.xlsx'; ;
    //     $filePath = 'public/' . $fileName;
    //     try {
    //         \Log::info('Queueing export for user ID: ' . $user->id);

    //         // $file =  Excel::download(new CategoryExport($filters), 'category.xlsx' , \Maatwebsite\Excel\Excel::XLSX);

    //         // $file = Excel::download(new CategoryExportWithChunks($filters), 'category.xlsx' , \Maatwebsite\Excel\Excel::XLSX);

    //        Excel::queue(new CategoryExportWithChunks($filters), $filePath)->chain([
    //            new SendExportNotification($user, $fileName)
    //        ]);

    //         // Excel::queue(new CategoryExportWithChunks($filters), $filePath)->chain([
    //         //     new InsertExportLog($request['user_id'], $fileName)
    //         // ]);
    //         return response()->json(['message' => 'Export queued successfully.']);
    //     }
    //     catch (Exception $e) {
    //         \Log::error('Export failed for user ID: ' . $user->id . '. Error: ' . $e->getMessage());

    //         return response()->json([
    //             'message' => 'Export Categories failed',
    //             'error' => $e->getMessage(),
    //         ], 400);
    //     }
    // }


    public function export(Request $request)
    {
        $filters = $request->only(['title']);
        $user = User::find($request['user_id']);

    
        try {
            // Dispatch the export job with the necessary arguments
            ExportCategoryChunkJob::dispatch($filters, 7000, $user);
    
            return response()->json(['message' => 'Export queued successfully.']);
        } catch (Exception $e) {
            \Log::error('Export failed for user ID: ' . $user->id . '. Error: ' . $e->getMessage());
    
            return response()->json([
                'message' => 'Export Categories failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
    
    

    


}
