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
            dd($tokens);
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
