<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;

use App\Http\Resources\UserResource;

use App\Models\User ;
use Spatie\Permission\Models\Role;

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
        // $validatedData = $request->validated();
        $user = User::create($request->validated());
        // Role::create(['name' => 'user']);
        $user->syncRoles('user'); // set the role to "user" by default
        return response()->json([ 
            "message" => "user registered successfully",
            "data" => UserResource::make($user)    
        ], 201);
    }   

    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Login a user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 description="User email address",
     *                 example="user@example.com"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 description="User password",
     *                 example="password123"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logged in successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="user logged in successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="John Doe"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     format="email",
     *                     example="user@example.com"
     *                 ),
     *                 @OA\Property(
     *                     property="access_token",
     *                     type="string",
     *                     example="access_token_value"
     *                 ),
     *                 @OA\Property(
     *                     property="refresh_token",
     *                     type="string",
     *                     example="refresh_token_value"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="credentials don't match"
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
     *                 example="The email field is required."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(
     *                         type="string",
     *                         example="The email field is required."
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="array",
     *                     @OA\Items(
     *                         type="string",
     *                         example="The password field is required."
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function login (Request $request){

        $validatedAttributes = $request->validate([
            "email"=>['required', 'email'],
            "password"=>['required']
        ]);

        $loginStatus = Auth::attempt($validatedAttributes);

        if($loginStatus){
            $user = Auth::user();
            $tokens = PassportHelper::generateTokens( $request->email,  $request->password);
            $responseData = [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "email_verified_at" => $user->email_verified_at,
                "created_at" => $user->created_at,
                "updated_at" => $user->updated_at,
                "access_token" => $tokens["access_token"],
                "refresh_token" => $tokens["refresh_token"],
                "roles" => $user->getRoleNames(),
            ];
            return response()->json([
                "message"=>"user logged in successfully",
                "data"=>$responseData,
                
            ],200);
        }

        return response()->json([
            "message"=>"credentials don't match",

        ],401);

    } 

    public function logout (Request $request){
        $user = Auth::user();

        if ($user) {
            $user->token()->revoke();

            return response()->json([
                'status' => 200,
                'message' => 'User logged out successfully',
            ]);
        }

        return response()->json([
            'status' => 401,
            'message' => 'User not authenticated',
        ], 401);
    }
}
