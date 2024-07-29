<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

use App\Http\Resources\UserResource;

use App\Models\User;
class UserController extends Controller
{

    public function index()
    {
        $users = User::get();
        return response()->json([ 
            "data" => UserResource::collection($users),
            "message" => "users retrieved successfully"
            ], 200);
    }

  
    public function store(StoreUserRequest $request)
    {
        $validatedData = $request->validated();
        // dd($validatedData['name']);
        $user = User::create($validatedData);
        return response()->json([ 
            "data" => new UserResource($user),
            "message" => "user created successfully"
        ], 201);

    }


    public function show(User $user)
    {
        return response()->json([ 
            "data" => new UserResource($user),
            "message" => "user retrieved successfully"
            ], 200);
    }


    public function update(UpdateUserRequest $request, User $user)
    {
        $validatedData = $request->validated();
        $user->update($validatedData);
        return response()->json([ 
            "data" => new UserResource($user->fresh()),
            "message" => "user updated successfully"
        ], 200);
    }


    public function destroy(User $user)
    {
        $user->delete();
        return response()->json([
            'message' => 'user deleted successfully',
        ], 200);

    }
}
