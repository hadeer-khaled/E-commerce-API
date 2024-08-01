<?php

namespace App\Swagger\schemas;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User Resource",
 *     description="A representation of a user",
 *     required={"id", "title"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=1,
 *         description="The unique identifier of the user"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         example="hadeer",
 *         description="The name of the user"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         example="hadeer@gmail.com",
 *         description="The email of the user"
 *     ),

 * )
 */
class User{

}