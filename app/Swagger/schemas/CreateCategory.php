<?php

namespace App\Swagger\schemas;

/**
 * @OA\RequestBody(
 *     request="CreateCategory",
 *     required=true,
 *     @OA\MediaType(
 *         mediaType="multipart/form-data",
 *         @OA\Schema(
 *             type="object",
 *             required={"title"},
 *             @OA\Property(
 *                 property="title",
 *                 type="string",
 *                 example="Electronics",
 *                 description="The title of the category"
 *             ),
 *             @OA\Property(
 *                 property="image",
 *                 type="string",
 *                 format="binary",
 *                 description="The image file associated with the category"
 *             )
 *         )
 *     )
 * )
 */

 class CreateCategory{
    
 }
