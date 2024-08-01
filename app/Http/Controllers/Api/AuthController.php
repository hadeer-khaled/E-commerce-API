<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;

use App\Http\Resources\UserResource;

use App\Models\User ;

use App\Utils\PassportHelper;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/register",
     *     summary="Register a new user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         ref="#/components/requestBodies/CreateUser"
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="user registered successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/User"
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
     *                 example="The given data was invalid."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={
     *                     "email": {"The email has already been taken."},
     *                     "password": {"The password must be at least 8 characters."}
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Failed to register user"
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

    public function register (StoreUserRequest $request){
        $validatedData = $request->validated();
        $user = User::create($validatedData);
        $user->syncRoles('user'); // set the role to "user" by default
        return response()->json([ 
            "message" => "user registered successfully",
            "data" => new UserResource($user)    
        ], 201);
    }   

    public function login (Request $request){

        $validatedAttributes = $request->validate([
            "email"=>['required', 'email'],
            "password"=>['required']
        ]);

        $loginStatus = Auth::attempt($validatedAttributes);

        if($loginStatus){
            $user = Auth::user();
            $tokens = PassportHelper::generateTokens( $request->email,  $request->password);
            $user["access_token"] = $tokens["access_token"];
            $user["refresh_token"] = $tokens["refresh_token"];
            return response()->json([
                "message"=>"user logged in successfully",
                "data"=>$user,
            ],200);
        }

        return response()->json([
            "message"=>"credentials don't match",

        ],401);

    } 
}
