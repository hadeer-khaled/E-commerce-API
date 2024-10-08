<?php

namespace App\Swagger\requestBodies;

/**
 * @OA\RequestBody(
 *     request="CreateUser",
 *     required=true,
 *     @OA\MediaType(
 *         mediaType="multipart/form-data",
 *         @OA\Schema(
 *             type="object",
 *             required={"name" , "email" , "password" , "password_confirmation"},
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
 *             @OA\Property(
 *                 property="password",
 *                 type="string",
 *             ),
 *             @OA\Property(
 *                 property="password_confirmation",
 *                 type="string",
 *             ),
 *         )
 *     )
 * )
 */

class CreateUser
{
}
