<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
class RoleController extends Controller
{
   
    public function index()
    {
        $roles = Role::all()->pluck("name");
        return response()->json([
            "message" => "role created successfully",
            "data"=> $roles
        ], 200);
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name'=>['required', 'string' , 'unique:roles,name']
        ]);
        $role = Role::create(['name' => $request->name , 'guard_name' => 'api']);
        return response()->json([
            "message" => "role created successfully",
        ], 200);
    }

   
    public function show(Role $role)
    {
        //
    }

 
    public function update(Request $request, Role $role)
    {
        //
    }


    public function destroy(Role $role)
    {
        //
    }

    public function addPermissionToRole(Request $request, Role $role)
    {
        $validatedData = $request->validate([
            'permissions' => ['required', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name' ],
        ]);
        
        $role->syncPermissions($validatedData);
        return response()->json([
            "message" => "Permissions added to role successfully",
        ], 201);
    }
}
