<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        //
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name'=>['required', 'string' , 'unique:permissions,name']
        ]);
        $permission = Permission::create(['name' => $request->name]);
    }

   
    public function show(string $id)
    {
        //
    }

 
    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(string $id)
    {
        //
    }
}
