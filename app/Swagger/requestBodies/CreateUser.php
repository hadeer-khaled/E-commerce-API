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
 *             required={"title"},
*               @OA\Property(
*                   property="name",
*                   type="string",
*                   example="John Doe",
*                   description="The name of the user",
*                   maxLength=50
*               ),
*               @OA\Property(
*                   property="email",
*                   type="string",
*                   format="email",
*                   example="johndoe@example.com",
*                   description="The unique email address of the user"
*               ),
*               @OA\Property(
*                   property="password",
*                   type="string",
*                   format="password",
*                   description="The password for the user",
*                   minLength=3
*               ),
 *               @OA\Property(
 *                   property="password_confirmation",
 *                   type="string",
 *                   format="password",
 *                   description="The password confirmation"
 *               )
 *         )
 *     )
 * )
 */

class CreateUser
{
}
