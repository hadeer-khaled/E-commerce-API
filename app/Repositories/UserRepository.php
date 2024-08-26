<?php
namespace App\Repositories;

use App\Models\User;
use App\Repositories\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function getAll()
    {
        return User::all();
    }

    public function getUserById(int $id)
    {
        return User::findOrFail($id);
    }

    public function create(array $data)
    {
        $user = User::create($data);
        $user->syncRoles($data['roles']);
        return $user;
    }

    public function update(User $user, array $data)
    {
    
        $user->update($data);
        $user->syncRoles($data['roles']);
    
        return $user->fresh();
    }
    
    public function delete(User $user)
    {
       
        $user->delete();
    
        return response()->json(['message' => 'User deleted successfully'], 200);
    }
    
    public function addRoleToUser(User $user, array $roles)
    {
        $user->syncRoles($roles);
        return $user;
    }
}
