<?php

namespace App\Swagger\schemas;

/**
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     title="Category Resource",
 *     description="A representation of a category",
 *     required={"id", "title"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1,
 *         description="The unique identifier of the category"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         example="Electronics",
 *         description="The title of the category"
 *     ),
 *     @OA\Property(
 *         property="image",
 *         type="string",
 *         format="url",
 *         example="https://example.com/storage/image.jpg",
 *         nullable=true,
 *         description="The URL of the category's image"
 *     )
 * )
 */
class Category{

}