<?php

namespace App\Swagger\requestBodies;

/**
 * @OA\RequestBody(
 *     request="UpdateUser",
 *     required=true,
 *     @OA\MediaType(
 *         mediaType="multipart/form-data",
 *         @OA\Schema(
 *             type="object",
 *             required={"name" , "email"},
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 example="hadeer",
 *                 description="The name of the user"
 *             ),
 *             @OA\Property(
 *                 property="email",
 *                 type="string",
 *                 description="The email associated with the user"
 *             ),
 *         )
 *     )
 * )
 */

class UpdateUser
{
}
