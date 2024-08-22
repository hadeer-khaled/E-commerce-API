<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

use App\Http\Resources\UserResource;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;

class UserController extends Controller
{

    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    /**
     * @OA\Get(
     *     path="/users",
     *     summary="Get a list of users",
     *     description="Retrieve a list of all users.",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Users retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/User")
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="users retrieved successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthorized"
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
     *                 example="Internal Server Error"
     *             )
     *         )
     *     )
     * )
     */


    public function index()
    {
        $users = $this->userRepository->getAll();
        return response()->json([ 
            "data" => UserResource::collection($users),
            "message" => "users retrieved successfully"
            ], 200);
    }

    /**
     * @OA\Post(
     *     path="/users",
     *     summary="Create a new user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         ref="#/components/requestBodies/CreateUser"  
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/User"  
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="User created successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to create user",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Failed to create user"
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
    public function store(StoreUserRequest $request)
    {

        $user = $this->userRepository->create($request->validated());
        return response()->json([ 
            "message" => "user created successfully",
            "data" => UserResource::make($user)
        ], 201);

    }


    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="Retrieve a specific user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *         description="The ID of the user"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="User not found"
     *             )
     *         )
     *     )
     * )
     */

    public function show(User $user)
    {
        return response()->json([ 
            "data" => UserResource::make($user),
            "message" => "user retrieved successfully"
            ], 200);
    }

    
    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     summary="Update an existing user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *         description="The ID of the user to update"
     *     ),
     *     @OA\RequestBody(
     *         ref="#/components/requestBodies/UpdateUser"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="user updated successfully",
     *         ref="#/components/schemas/User"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to update user",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Failed to update user"
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
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->userRepository->update($user, $request->validated());
        return response()->json([ 
            "data" => UserResource::make($user->fresh()),
            "message" => "user updated successfully"
        ], 200);
    }


    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     summary="Delete a user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="User deleted successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to delete user",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Failed to delete User"
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

 
    public function destroy(User $user)
    {
        $this->userRepository->delete($user);

        return response()->json([
            'message' => 'user deleted successfully',
        ], 200);

    }

    public function addRoleToUser(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'roles' => ['required', 'array'],
            'roles.*' => ['string', 'exists:roles,name' ],
        ]);
        
        $this->userRepository->addRoleToUser($user, $validatedData['roles']);

        return response()->json([
            "message" => "Roles added to user successfully",
        ], 201);
    }
}
